<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportRequest;
use App\Models\SupportReply;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;
use App\Models\AIConversation; // THÊM DÒNG NÀY

class SupportController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

   public function index()
{
    $supportRequest = null;
    $unreadCount = 0;

    if (Auth::check()) {
        $supportRequest = SupportRequest::where('user_id', Auth::id())
            ->with(['replies.user'])
            ->latest()
            ->first();
        
        if ($supportRequest) {
            $unreadCount = $supportRequest->replies()
                ->where('is_admin', true)
                ->where('is_read', false)
                ->count();
        }
    } else {
        $guestId = session('guest_support_request_id');
        if ($guestId) {
            $supportRequest = SupportRequest::where('id', $guestId)
                ->with(['replies.user'])
                ->first();
        }
    }

    return view('support.index', [
        'supportRequest' => $supportRequest,
        'unreadCount' => $unreadCount,
    ]);
}

    public function createForm()
    {
        return view('support.form');
    }

    public function delete($id)
    {
        $supportRequest = SupportRequest::findOrFail($id);

        // Kiểm tra quyền: chỉ người tạo mới được xoá
        if (Auth::check()) {
            if ($supportRequest->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền xoá yêu cầu này.');
            }
        } else {
            $guestId = session('guest_support_request_id');
            if ($supportRequest->id != $guestId) {
                abort(403, 'Bạn không có quyền xoá yêu cầu này.');
            }
        }

        $supportRequest->delete();

        if (!Auth::check()) {
            session()->forget('guest_support_request_id');
        }

        return redirect()->route('support.index')->with('success', 'Đã xoá yêu cầu hỗ trợ.');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required_if:user_id,null|string|max:255',
            'email' => 'required_if:user_id,null|email|max:255',
            'phone' => 'nullable|string|max:20',
            'type' => 'required|string|max:255',
            'priority' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'attachment' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,gif,pdf,doc,docx',
        ]);

        // Xử lý file đính kèm
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('support_attachments', $fileName, 'public');
        }

        // Tạo dữ liệu support request (LOẠI BỎ is_read)
        $supportData = [
            'user_id' => Auth::check() ? Auth::id() : null,
            'name' => $validated['name'] ?? (Auth::check() ? Auth::user()->name : null),
            'email' => $validated['email'] ?? (Auth::check() ? Auth::user()->email : null),
            'phone' => $validated['phone'] ?? (Auth::check() ? Auth::user()->phone : null),
            'message' => $validated['message'],
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'attachment' => $attachmentPath,
            'status' => 'pending' // Chỉ giữ lại status
        ];

        $support = SupportRequest::create($supportData);

        if (!Auth::check()) {
            session(['guest_support_request_id' => $support->id]);
        }

        return redirect()->route('support.index')->with('success', 'Gửi yêu cầu thành công! Chúng tôi sẽ phản hồi sớm nhất.');
    }

  public function sendReply(Request $request, $id)
{
    $request->validate([
        'reply' => 'nullable|string|max:1000',
        'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt',
    ]);

    $supportRequest = SupportRequest::findOrFail($id);

    // Kiểm tra quyền truy cập
    if (Auth::check()) {
        if ($supportRequest->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền gửi tin nhắn cho yêu cầu này.');
        }
    } else {
        $guestId = session('guest_support_request_id');
        if ($supportRequest->id != $guestId) {
            abort(403, 'Bạn không có quyền gửi tin nhắn cho yêu cầu này.');
        }
    }

    // Process attachment
    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $attachmentPath = $file->storeAs('support_attachments', $fileName, 'public');
    }

    $replyData = [
        'support_request_id' => $id,
        'user_id' => Auth::check() ? Auth::id() : null,
        'reply' => $request->reply ?? '',
        'is_read' => false,
        'is_admin' => false, // QUAN TRỌNG: Tin nhắn từ người dùng
        'attachment' => $attachmentPath,
        // Thêm thông tin người gửi cho khách không đăng nhập
        'name' => Auth::check() ? Auth::user()->name : $supportRequest->name,
        'email' => Auth::check() ? Auth::user()->email : $supportRequest->email,
        'phone' => Auth::check() ? Auth::user()->phone : $supportRequest->phone,
    ];

    $reply = SupportReply::create($replyData);
    $reply->load('user');

    // Cập nhật trạng thái yêu cầu thành "processing"
    $supportRequest->update(['status' => 'processing']);

    // Gửi notification đến admin (ĐÚNG CHỖ NÀY)
    $this->sendRealTimeNotification($supportRequest, $reply);

    return response()->json([
        'success' => true,
        'reply' => $reply,
        'message' => 'Đã gửi tin nhắn thành công!'
    ]);
}

    // ✅ Gửi notification real-time
// Đây là phương thức ĐÚNG trong SupportController.php
private function sendRealTimeNotification($supportRequest, $reply)
{
    // Gửi notification đến admin khi người dùng gửi tin nhắn
    $this->firebaseService->sendToTopic('admin_support', 
        '💬 Tin nhắn mới từ ' . ($supportRequest->name ?? 'Khách hàng'),
        substr($reply->reply, 0, 100) . '...',
        [
            'type' => 'new_support_message',
            'support_request_id' => $supportRequest->id,
            'user_id' => $supportRequest->user_id,
            'timestamp' => now()->toISOString(),
            'action' => 'user_replied'
        ]
    );
}

    // ✅ API để lấy tin nhắn mới (real-time polling)
    public function getNewMessages($id, Request $request)
    {
        $lastMessageId = $request->get('last_message_id', 0);
        
        $messages = SupportReply::where('support_request_id', $id)
            ->where('id', '>', $lastMessageId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($reply) {
                return [
                    'id' => $reply->id,
                    'support_request_id' => $reply->support_request_id,
                    'user_id' => $reply->user_id,
                    'reply' => $reply->reply,
                    'is_read' => $reply->is_read,
                    'is_admin' => $reply->is_admin,
                    'attachment' => $reply->attachment,
                    'created_at' => $reply->created_at->toISOString(),
                    'updated_at' => $reply->updated_at->toISOString(),
                    'user' => $reply->user ? [
                        'id' => $reply->user->id,
                        'name' => $reply->user->name,
                        'email' => $reply->user->email
                    ] : null,
                    'name' => $reply->name // Thêm trường name cho user không đăng nhập
                ];
            });

        return response()->json([
            'messages' => $messages,
            'last_message_id' => $messages->isNotEmpty() ? $messages->last()['id'] : $lastMessageId
        ]);
    }

    public function checkUnread()
    {
        $supportRequest = null;

        if (Auth::check()) {
            $supportRequest = SupportRequest::where('user_id', Auth::id())->first();
        } else {
            $guestId = session('guest_support_request_id');
            if ($guestId) {
                $supportRequest = SupportRequest::find($guestId);
            }
        }

        $hasUnread = false;

        if ($supportRequest) {
            $hasUnread = $supportRequest->replies()
                ->where('user_id', '!=', Auth::id())
                ->where('is_read', false)
                ->exists();
        }

        return response()->json(['has_unread' => $hasUnread]);
    }

    public function markAsRead(Request $request, $id)
    {
        $supportRequest = SupportRequest::with('replies')->findOrFail($id);

        foreach ($supportRequest->replies as $reply) {
            if ($reply->user_id !== Auth::id()) {
                $reply->update(['is_read' => true]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $unreadCount = 0;
        
        if (Auth::check()) {
            $supportRequest = SupportRequest::where('user_id', Auth::id())->first();
            if ($supportRequest) {
                $unreadCount = $supportRequest->replies()
                    ->where('user_id', '!=', Auth::id())
                    ->where('is_read', false)
                    ->count();
            }
        } else {
            $guestId = session('guest_support_request_id');
            if ($guestId) {
                $supportRequest = SupportRequest::find($guestId);
                if ($supportRequest) {
                    $unreadCount = $supportRequest->replies()
                        ->where('is_read', false)
                        ->count();
                }
            }
        }
        
        return response()->json(['unread_count' => $unreadCount]);
    }

    // Thêm vào SupportController.php

    /**
     * Đánh dấu tất cả tin nhắn đã đọc
     */
    public function markAllRepliesAsRead($id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        
        // Kiểm tra quyền truy cập
        if (Auth::check()) {
            if ($supportRequest->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền truy cập yêu cầu này.');
            }
        } else {
            $guestId = session('guest_support_request_id');
            if ($supportRequest->id != $guestId) {
                abort(403, 'Bạn không có quyền truy cập yêu cầu này.');
            }
        }
        
        // Đánh dấu tất cả tin nhắn từ admin là đã đọc
        $supportRequest->replies()
            ->where('is_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Lấy số lượng tin nhắn chưa đọc từ admin
     */
    public function getUnreadAdminMessagesCount()
    {
        $unreadCount = 0;
        
        if (Auth::check()) {
            $supportRequest = SupportRequest::where('user_id', Auth::id())->first();
            if ($supportRequest) {
                $unreadCount = $supportRequest->replies()
                    ->where('is_admin', true)
                    ->where('is_read', false)
                    ->count();
            }
        } else {
            $guestId = session('guest_support_request_id');
            if ($guestId) {
                $supportRequest = SupportRequest::find($guestId);
                if ($supportRequest) {
                    $unreadCount = $supportRequest->replies()
                        ->where('is_admin', true)
                        ->where('is_read', false)
                        ->count();
                }
            }
        }
        
        return response()->json(['unread_count' => $unreadCount]);
    }

    // Thêm phương thức mới vào SupportController
    public function aiChat(Request $request)
    {
        return view('support.ai-chat');
    }

    public function sendToAIChat(Request $request)
    {
        // Chuyển hướng sang AI Chat thay vì hỗ trợ thủ công
        $quickResponses = [
            "Tôi có thể giúp bạn tra cứu đơn hàng. Vui lòng cho biết mã đơn hàng hoặc số điện thoại.",
            "Bạn cần tư vấn sản phẩm nào? Tôi có thể đề xuất sản phẩm phù hợp.",
            "Tôi sẽ giúp bạn kiểm tra thông tin. Hãy cho biết chi tiết yêu cầu."
        ];
        
        return response()->json([
            'ai_response' => $quickResponses[array_rand($quickResponses)],
            'suggested_actions' => [
                'tra_cuu_don_hang', 'tu_van_san_pham', 'hoi_dap_chinh_sach'
            ]
        ]);
    }

    public function getChatData($id)
    {
        $supportRequest = SupportRequest::with('replies')->findOrFail($id);
        
        // Kiểm tra quyền truy cập
        if (Auth::check()) {
            if ($supportRequest->user_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền truy cập yêu cầu này.');
            }
        } else {
            $guestId = session('guest_support_request_id');
            if ($supportRequest->id != $guestId) {
                abort(403, 'Bạn không có quyền truy cập yêu cầu này.');
            }
        }
        
        return response()->json([
            'request' => $supportRequest,
            'replies' => $supportRequest->replies
        ]);
    }

    public function getAIChatHistory()
    {
        $sessionId = session()->getId();
        $conversations = AIConversation::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'conversations' => $conversations
        ]);
    }
}
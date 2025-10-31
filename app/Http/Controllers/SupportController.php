<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportRequest;
use App\Models\SupportReply;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;

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

        if (Auth::check()) {
            // Người dùng đã đăng nhập → lấy theo user_id
            $supportRequest = SupportRequest::where('user_id', Auth::id())
                ->with(['replies.user'])
                ->latest()
                ->first();
        } else {
            // Người dùng là khách → lấy theo session
            $guestId = session('guest_support_request_id');

            if ($guestId) {
                $supportRequest = SupportRequest::where('id', $guestId)
                    ->with(['replies.user'])
                    ->first();
            }
        }

        return view('support.index', compact('supportRequest'));
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
        if (Auth::check()) {
            $data = $request->validate([
                'message' => 'required|string|max:1000',
                'type' => 'required|in:general,order,product,shipping,payment,technical,other',
                'priority' => 'required|in:low,medium,high',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048'
            ]);

            $data['user_id'] = Auth::id();
            $data['name']    = Auth::user()->name;
            $data['email']   = Auth::user()->email;
            $data['phone']   = Auth::user()->phone;
        } else {
            $data = $request->validate([
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'phone'   => 'nullable|string|max:20',
                'message' => 'required|string|max:1000',
                'type' => 'required|in:general,order,product,shipping,payment,technical,other',
                'priority' => 'required|in:low,medium,high',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048'
            ]);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('support_attachments', $filename, 'public');
            $attachmentPath = $path;
        }

        $supportData = [
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'type' => $data['type'],
            'priority' => $data['priority'],
            'attachment' => $attachmentPath,
            'is_read' => false,
            'status' => 'pending'
        ];

        $support = SupportRequest::create($supportData);

        if (!Auth::check()) {
            session(['guest_support_request_id' => $support->id]);
        }

        return redirect()->route('support.index')->with('success', 'Gửi yêu cầu thành công! Chúng tôi sẽ phản hồi sớm nhất.');
    }

    // ✅ ĐÃ THAY BẰNG BẢN MỚI (tích hợp Firebase)
   public function sendReply(Request $request, $id)
{
    $request->validate([
        'reply' => 'required|string|max:1000',
    ]);

    $supportRequest = SupportRequest::findOrFail($id);

    $replyData = [
        'support_request_id' => $id,
        'user_id' => Auth::check() ? Auth::id() : null,
        'reply' => $request->reply,
        'is_read' => false,
        'is_admin' => false, // ✅ Đánh dấu đây là tin nhắn từ user
    ];

    $reply = SupportReply::create($replyData);

    // ✅ Gửi real-time notification đến admin
    $this->sendRealTimeNotification($supportRequest, $reply);

    return response()->json([
        'success' => true,
        'reply' => $reply,
        'message' => 'Đã gửi tin nhắn '
    ]);
}

// ✅ Gửi notification real-time
private function sendRealTimeNotification($supportRequest, $reply)
{
    // Gửi notification đến admin
    $this->firebaseService->sendToTopic('admin_support', 
        '💬 Tin nhắn mới từ ' . $supportRequest->name,
        substr($reply->reply, 0, 100) . '...',
        [
            'type' => 'new_support_message',
            'support_request_id' => $supportRequest->id,
            'user_id' => $supportRequest->user_id,
            'timestamp' => now()->toISOString()
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
}

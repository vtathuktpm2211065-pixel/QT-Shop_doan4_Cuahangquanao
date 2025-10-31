<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportReply;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportRequest;
use App\Models\ShopLocation;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseNotificationService;

class AdminSupportController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index(Request $request)
    {
        $query = SupportRequest::with(['replies', 'user']);
        
        if ($request->has('customer') && $request->customer) {
            $query->where('name', 'like', '%'.$request->customer.'%')
                  ->orWhere('email', 'like', '%'.$request->customer.'%');
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $requests = $query->latest()->get();
        
        $stats = [
            'total' => SupportRequest::count(),
            'pending' => SupportRequest::where('status', 'pending')->count(),
            'processing' => SupportRequest::where('status', 'processing')->count(),
            'resolved' => SupportRequest::where('status', 'resolved')->count(),
        ];
        
        return view('admin.support.requests_list', compact('requests', 'stats'));
    }

    public function advancedList(Request $request)
    {
        $query = SupportRequest::with(['replies', 'user']);
        
        if ($request->has('customer') && $request->customer) {
            $query->where('name', 'like', '%'.$request->customer.'%')
                  ->orWhere('email', 'like', '%'.$request->customer.'%');
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $requests = $query->latest()->get();
        
        $stats = [
            'total' => SupportRequest::count(),
            'pending' => SupportRequest::where('status', 'pending')->count(),
            'processing' => SupportRequest::where('status', 'processing')->count(),
            'resolved' => SupportRequest::where('status', 'resolved')->count(),
        ];
        
        return view('admin.support.advanced_list', compact('requests', 'stats'));
    }

    public function chat($id)
    {
        $request = SupportRequest::with(['replies.user'])->findOrFail($id);
        $shopLocations = ShopLocation::where('is_active', true)->get();
        
        $request->replies()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return view('admin.support.chat', compact('request', 'shopLocations'));
    }

    public function sendQuickLocation(Request $request, $id)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'name' => 'required|string',
            'address' => 'required|string'
        ]);

        $message = "📍 **{$validated['name']}**\n";
        $message .= "🏠 {$validated['address']}\n\n";
        $message .= "🗺️ [Xem trên Google Maps](https://maps.google.com/?q={$validated['latitude']},{$validated['longitude']})\n";
        $message .= "🚗 [Chỉ đường đến đây](https://www.google.com/maps/dir/?api=1&destination={$validated['latitude']},{$validated['longitude']})";

        SupportReply::create([
            'support_request_id' => $id,
            'user_id' => Auth::id(),
            'reply' => $message,
            'is_read' => false,
            'is_admin' => true,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * ✅ Cập nhật hàm reply() – thêm gửi notification Firebase
     */
public function reply(Request $request, $id)
{
    try {
        \Log::info('Admin reply attempt', ['request' => $request->all(), 'id' => $id]);
        
        $validated = $request->validate([
            'reply' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048'
        ]);

        $supportRequest = SupportRequest::with('user')->findOrFail($id);

        $replyData = [
            'support_request_id' => $id,
            'user_id' => Auth::id(),
            'reply' => $validated['reply'],
            'is_read' => false,
            'is_admin' => true,
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('support_attachments', $filename, 'public');
            $replyData['attachment'] = $path;
        }

        $reply = SupportReply::create($replyData);
        
        // Load quan hệ user và format dữ liệu
        $reply->load('user');
        $replyData = [
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
            ] : null
        ];

        $supportRequest->update(['status' => 'processing']);

        // Gửi notification Firebase
        if ($supportRequest->user_id) {
            $this->firebaseService->sendToUser(
                $supportRequest->user_id,
                '💬 Phản hồi từ cửa hàng',
                substr($validated['reply'], 0, 100) . '...',
                [
                    'type' => 'support_reply',
                    'support_request_id' => $supportRequest->id,
                    'timestamp' => now()->toISOString()
                ]
            );
        }

        
        return response()->json([
            'success' => true,
            'reply' => $replyData,
            'message' => 'Đã gửi phản hồi!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Admin reply error: '.$e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra khi gửi phản hồi.'
        ], 500);
    }
}

    public function markAsRead($id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        $supportRequest->replies()
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        $supportRequest->update(['status' => $request->status]);
        
        return response()->json(['success' => true]);
    }

    public function updatePriority(Request $request, $id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        $supportRequest->update(['priority' => $request->priority]);
        
        return response()->json(['success' => true]);
    }

    public function quickReply(Request $request)
    {
        $validated = $request->validate([
            'support_request_id' => 'required|exists:support_requests,id',
            'reply' => 'required|string'
        ]);

        SupportReply::create([
            'support_request_id' => $validated['support_request_id'],
            'user_id' => Auth::id(),
            'reply' => $validated['reply'],
            'is_read' => false,
            'is_admin' => true,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $support = SupportRequest::findOrFail($id);
        $support->replies()->delete();
        $support->delete();

        return redirect()->route('admin.support.index')->with('success', 'Đã xóa yêu cầu hỗ trợ.');
    }
    
    public function getStats()
    {
        $stats = [
            'total' => SupportRequest::count(),
            'pending' => SupportRequest::where('status', 'pending')->count(),
            'processing' => SupportRequest::where('status', 'processing')->count(),
            'resolved' => SupportRequest::where('status', 'resolved')->count(),
        ];
        
        return response()->json($stats);
    }

    public function analytics()
    {
        $stats = [
            'total_requests' => SupportRequest::count(),
            'pending_requests' => SupportRequest::where('status', 'pending')->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
            'resolved_today' => SupportRequest::where('status', 'resolved')
                ->whereDate('updated_at', today())
                ->count(),
        ];
        
        return view('admin.support.analytics', compact('stats'));
    }

    public function getStatsApi()
    {
        $stats = [
            'total' => SupportRequest::count(),
            'pending' => SupportRequest::where('status', 'pending')->count(),
            'processing' => SupportRequest::where('status', 'processing')->count(),
            'resolved' => SupportRequest::where('status', 'resolved')->count(),
        ];
        
        return response()->json($stats);
    }

    public function getRequestsApi(Request $request)
    {
        $query = SupportRequest::with(['replies', 'user']);
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $requests = $query->latest()->get();
        
        return response()->json($requests);
    }

    private function calculateAverageResponseTime()
    {
        $requests = SupportRequest::with('replies')->get();
        $totalTime = 0;
        $count = 0;
        
        foreach ($requests as $request) {
            $firstReply = $request->replies->where('is_admin', true)->first();
            if ($firstReply) {
                $responseTime = $firstReply->created_at->diffInMinutes($request->created_at);
                $totalTime += $responseTime;
                $count++;
            }
        }
        
        return $count > 0 ? round($totalTime / $count) : 0;
    }

    public function getNearbyShops(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50' // km
        ]);

        $shops = ShopLocation::select('*')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', 
            [$validated['latitude'], $validated['longitude'], $validated['latitude']])
            ->where('is_active', true)
            ->having('distance', '<', $request->radius ?? 10)
            ->orderBy('distance')
            ->get();

        return response()->json($shops);
    }

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

}

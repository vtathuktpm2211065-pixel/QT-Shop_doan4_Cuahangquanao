<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportReply;

class SupportController extends Controller
{
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
        // Người dùng đăng nhập
        if ($supportRequest->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xoá yêu cầu này.');
        }
    } else {
        // Người dùng là khách
        $guestId = session('guest_support_request_id');
        if ($supportRequest->id != $guestId) {
            abort(403, 'Bạn không có quyền xoá yêu cầu này.');
        }
    }

    $supportRequest->delete();

    // Xoá khỏi session nếu là khách
    if (!Auth::check()) {
        session()->forget('guest_support_request_id');
    }

    return redirect()->route('support.index')->with('success', 'Đã xoá yêu cầu hỗ trợ.');
}

   public function submit(Request $request)
{
    if (Auth::check()) {
        // Người dùng đã đăng nhập → chỉ cần kiểm tra message
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $data['user_id'] = Auth::id();
        $data['name']    = Auth::user()->name;
        $data['email']   = Auth::user()->email;
        $data['phone']   = Auth::user()->phone;
    } else {
        // Người dùng là khách → cần điền đầy đủ name, email, phone
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:10',
            'message' => 'required|string|max:1000',
        ]);
    }

    $support = SupportRequest::create($data);

    // Nếu là khách thì lưu ID để truy cập lại
    if (!Auth::check()) {
        session(['guest_support_request_id' => $support->id]);
    }

    return redirect()->route('support.index')->with('success', 'Gửi yêu cầu thành công!');
}

public function sendReply(Request $request, $id)
{
    $request->validate([
        'reply' => 'required|string|max:1000',
    ]);

    SupportReply::create([
        'support_request_id' => $id,
        'user_id' => Auth::check() ? Auth::id() : null,
        'name' => Auth::check() ? Auth::user()->name : null,
        'email' => Auth::check() ? Auth::user()->email : null,
        'phone' => Auth::check() ? Auth::user()->phone : null,
        'reply' => $request->reply,
    ]);

    return back()->with('success', 'Đã gửi phản hồi thành công!');
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
            ->where('user_id', '!=', Auth::id()) // Không phải phản hồi của mình
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

}
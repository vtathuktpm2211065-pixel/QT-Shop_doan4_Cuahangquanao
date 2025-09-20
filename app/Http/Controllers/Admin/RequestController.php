<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request as HttpRequest;
use App\Models\SupportRequest;
use App\Models\SupportReply;

class RequestController extends Controller
{
    // Hiển thị danh sách yêu cầu
    public function index()
    {
        $requests = SupportRequest::with('user')->latest()->get();
        return view('admin.support.requests_list', compact('requests'));
    }

    // Giao diện chat với người gửi
    public function chat($id)
    {
        $request = SupportRequest::with('replies', 'user')->findOrFail($id);
        return view('admin.request.chat', compact('request'));
    }

    // Gửi phản hồi
    public function reply(HttpRequest $req, $id)
    {
        $req->validate([
            'reply' => 'required|string'
        ]);

        SupportReply::create([
            'support_request_id' => $id,
            'user_id' => null, // Admin gửi → null
            'reply' => $req->reply,
            'is_read' => false
        ]);

        return back()->with('success', 'Đã gửi phản hồi!');
    }
}

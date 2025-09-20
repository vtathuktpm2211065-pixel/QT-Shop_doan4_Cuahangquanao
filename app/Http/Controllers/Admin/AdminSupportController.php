<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportReply;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportRequest;

class AdminSupportController extends Controller
{
    
    public function index()
    {
        $supportRequest = Auth::check()
            ? SupportRequest::where('user_id', Auth::id())->with('replies')->first()
            : null;

        if ($supportRequest) {
            SupportReply::where('support_request_id', $supportRequest->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('support.index', compact('supportRequest'));
    }

    public function chat($id)
    {
        $request = SupportRequest::with('replies', 'user')->findOrFail($id);
        return view('admin.support.chat', compact('request'));
    }

   public function reply(Request $request, $id)
{
    $request->validate([
        'reply' => 'required|string'
    ]);

    SupportReply::create([
        'support_request_id' => $id,
        'user_id' => Auth::id(),  // ✅ admin hiện tại
        'reply' => $request->reply,
        'is_read' => false,
    ]);

    return back()->with('success', 'Đã gửi phản hồi!');
}

    public function destroy($id)
    {
        $support = SupportRequest::findOrFail($id);
        $support->replies()->delete();
        $support->delete();

        return redirect()->route('admin.requests.index')->with('success', 'Đã xóa yêu cầu hỗ trợ.');
    }
}

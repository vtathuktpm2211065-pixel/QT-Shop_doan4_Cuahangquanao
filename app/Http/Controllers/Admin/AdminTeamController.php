<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamMember;
use App\Models\User;
class AdminTeamController extends Controller
{
    // ================== DANH SÁCH ==================
    public function index()
    {
        $teamMembers = TeamMember::all();

        $roles = [
            'manager' => 'Quản lý nhân viên',
            'staff_sales' => 'Nhân viên Bán hàng',
            'staff_support' => 'Nhân viên Hỗ trợ khách hàng',
            'staff_product' => 'Nhân viên Quản lý sản phẩm cơ bản',
        ];

        $permissions = [
            'view_dashboard' => 'Xem bảng điều khiển',
            'view_revenue_daily' => 'Xem báo cáo doanh thu theo ngày',
            'view_revenue_monthly' => 'Xem báo cáo doanh thu theo tháng',
            'view_revenue_yearly' => 'Xem báo cáo doanh thu theo năm',
            'handle_orders' => 'Quản lý đơn hàng',
            'handle_carts' => 'Quản lý giỏ hàng',
            'manage_stock' => 'Quản lý kho',
            'view_products' => 'Danh sách sản phẩm',
            'add_products' => 'Thêm sản phẩm',
            'manage_categories' => 'Quản lý danh mục',
            'view_reviews' => 'Đánh giá sản phẩm',
            'handle_requests' => 'Hỗ trợ khách hàng',
            'manage_vouchers' => 'Quản lý Voucher',
            'manage_team' => 'Quản lý nhân viên',
        ];

        return view('admin.team.index', compact('teamMembers', 'roles', 'permissions'));
    }

    // ================== TẠO THÀNH VIÊN ==================
    public function create()
    {
        $roles = [
            'manager' => 'Quản lý nhân viên',
            'staff_sales' => 'Nhân viên Bán hàng',
            'staff_support' => 'Nhân viên Hỗ trợ khách hàng',
            'staff_product' => 'Nhân viên Quản lý sản phẩm cơ bản',
        ];

        $permissions = [
            'view_dashboard' => 'Xem bảng điều khiển',
            'view_revenue_daily' => 'Xem báo cáo doanh thu theo ngày',
            'view_revenue_monthly' => 'Xem báo cáo doanh thu theo tháng',
            'view_revenue_yearly' => 'Xem báo cáo doanh thu theo năm',
            'handle_orders' => 'Quản lý đơn hàng',
            'handle_carts' => 'Quản lý giỏ hàng',
            'manage_stock' => 'Quản lý kho',
            'view_products' => 'Danh sách sản phẩm',
            'add_products' => 'Thêm sản phẩm',
            'manage_categories' => 'Quản lý danh mục',
            'view_reviews' => 'Đánh giá sản phẩm',
            'handle_requests' => 'Hỗ trợ khách hàng',
            'manage_vouchers' => 'Quản lý Voucher',
            'manage_team' => 'Quản lý nhân viên',
        ];

        $permissionsByRole = $this->permissionsByRole();

        return view('admin.team.create', compact('roles', 'permissions', 'permissionsByRole'));
    }

    
    // ================== CHỈNH SỬA THÔNG TIN ==================
    public function edit($id)
    {
        $member = TeamMember::findOrFail($id);
        return view('admin.team.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $member = TeamMember::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('team_photos', 'public');
        }

        $member->update($data);

        return redirect()->route('team.index')->with('success', 'Đã cập nhật thông tin!');
    }

    public function destroy($id)
    {
        TeamMember::destroy($id);
        return back()->with('success', 'Đã xoá thành viên!');
    }

    // ================== PHÂN QUYỀN ==================
    public function editRole($id)
    {
        $member = TeamMember::findOrFail($id);

        $roles = [
            'manager' => 'Quản lý nhân viên',
            'staff_sales' => 'Nhân viên Bán hàng',
            'staff_support' => 'Nhân viên Hỗ trợ khách hàng',
            'staff_product' => 'Nhân viên Quản lý sản phẩm cơ bản',
        ];

        $permissions = [
            'view_dashboard' => 'Xem bảng điều khiển',
            'view_revenue_daily' => 'Xem báo cáo doanh thu theo ngày',
            'view_revenue_monthly' => 'Xem báo cáo doanh thu theo tháng',
            'view_revenue_yearly' => 'Xem báo cáo doanh thu theo năm',
            'handle_orders' => 'Quản lý đơn hàng',
            'handle_carts' => 'Quản lý giỏ hàng',
            'manage_stock' => 'Quản lý kho',
            'view_products' => 'Danh sách sản phẩm',
            'add_products' => 'Thêm sản phẩm',
            'manage_categories' => 'Quản lý danh mục',
            'view_reviews' => 'Đánh giá sản phẩm',
            'handle_requests' => 'Hỗ trợ khách hàng',
            'manage_vouchers' => 'Quản lý Voucher',
            'manage_team' => 'Quản lý nhân viên',
        ];

        $permissionsByRole = $this->permissionsByRole();

        if (is_null($member->permissions)) {
            $member->permissions = $permissionsByRole[$member->role] ?? [];
        }

        return view('admin.team.edit-role', compact('member', 'roles', 'permissions', 'permissionsByRole'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:manager,staff_sales,staff_support,staff_product',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $member = TeamMember::findOrFail($id);
        $member->role = $request->role;
        $member->permissions = $request->input('permissions', $this->permissionsByRole()[$request->role] ?? []);
        $member->save();

        return redirect()->route('admin.team.index')->with('success', 'Cập nhật quyền thành công!');
    }

    // ================== TOGGLE BANNED ==================
    public function toggleBan($id)
    {
        $member = TeamMember::findOrFail($id);
        $member->banned = !$member->banned;
        $member->save();

        return back()->with('success', $member->banned ? 'Đã chặn thành viên.' : 'Đã mở chặn thành viên.');
    }

    // ================== HÀM HỖ TRỢ ==================
    private function permissionsByRole()
    {
        return [
            'manager' => [
                'view_dashboard',
                'view_revenue_daily',
                'view_revenue_monthly',
                'view_revenue_yearly',
                'handle_orders',
                'handle_carts',
                'manage_stock',
                'view_products',
                'add_products',
                'manage_categories',
                'handle_requests',
                'manage_vouchers',
            ],
            'staff_sales' => [
                'view_dashboard',
                'handle_orders',
                'handle_carts',
            ],
            'staff_support' => [
                'view_dashboard',
                'handle_requests',
            ],
            'staff_product' => [
                'view_dashboard',
                'view_products',
                'view_reviews',
            ],
        ];
    }
    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:team_members,email|unique:users,email',
        'password' => 'required|string|confirmed|min:6',
        'position' => 'nullable|string|max:255',
        'role' => 'required|in:manager,staff_sales,staff_support,staff_product',
        'permissions' => 'nullable|array',
        'permissions.*' => 'string',
        'bio' => 'nullable|string',
        'photo' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('team_photos', 'public');
    }

    // mã hóa mật khẩu
    $data['password'] = bcrypt($data['password']);

    // nếu không chọn quyền riêng thì gán theo role
    if (empty($data['permissions'])) {
        $data['permissions'] = $this->permissionsByRole()[$data['role']] ?? [];
    }

    // ✅ Tạo trong bảng team_members
    $teamMember = TeamMember::create($data);

    // ✅ Đồng bộ sang bảng users để đăng nhập admin
   $user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'username' => Str::slug($data['name']) . rand(100,999), // thêm username
    'password' => $data['password'], // đã bcrypt sẵn
   
]);
$user->assignRole('staff');
    return redirect()->route('admin.team.create')
        ->with('success', 'Thành viên đã được thêm thành công!');
}

}
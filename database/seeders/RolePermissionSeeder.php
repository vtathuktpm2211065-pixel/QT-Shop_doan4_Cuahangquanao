<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Danh sách quyền
        $permissions = [
            'manage users'    => 'Quản lý người dùng',
            'edit products'   => 'Chỉnh sửa sản phẩm',
            'delete products' => 'Xoá sản phẩm',
            'view orders'     => 'Xem đơn hàng',
            'manage orders'   => 'Quản lý đơn hàng',
            'view_dashboard'  => 'Xem bảng điều khiển',
            'handle_orders'   => 'Quản lý đơn hàng',
            'handle_carts'    => 'Quản lý giỏ hàng',
            'view_products'   => 'Xem sản phẩm',
            'manage_stock'    => 'Quản lý kho',
            'handle_requests' => 'Hỗ trợ khách hàng',
            'handle_reviews'  => 'Quản lý đánh giá',
    'handle_team'     => 'Quản lý nhân viên',
        ];

        // Tạo quyền
        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate(
                ['name' => $key],
                ['description' => is_string($description) ? $description : $key]
            );
        }

        // Tạo role admin và gán toàn bộ quyền
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(array_keys($permissions));

        // Tạo role staff (nhân viên) và gán quyền cơ bản
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
       $staffRole->syncPermissions([
    'view_dashboard',
    'handle_orders',
    'handle_carts',
    'handle_requests', // quản lý hỗ trợ khách hàng
    'handle_reviews',  // quản lý đánh giá sản phẩm
    'handle_team',     // quản lý nhân viên
]);
;

        // Tạo role editor nếu muốn
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions(['edit products', 'view orders']);

        // Tạo role user (người dùng thường) không quyền
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([]);
    }
}

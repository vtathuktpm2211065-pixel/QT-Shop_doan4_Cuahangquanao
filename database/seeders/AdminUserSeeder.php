<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Tạo role nếu chưa có
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        // Tạo user admin
            $admin = User::updateOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'name' => 'Admin',
                    'username' => 'admin',
                    'password' => Hash::make('QTSHOP12345@'),
                ]
            );
        $admin->syncRoles([$adminRole->name]); // gán duy nhất role admin

        // Tạo user staff
        $staff = User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Nhân viên',
                'username' => 'staff',
                'password' => Hash::make('12345678'),
            ]
        );
        $staff->syncRoles([$staffRole->name]); // gán duy nhất role staff
    }
}

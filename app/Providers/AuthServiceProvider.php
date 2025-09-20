<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Các policy được ánh xạ trong ứng dụng.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Đăng ký bất kỳ dịch vụ xác thực / phân quyền nào.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Bạn có thể định nghĩa Gate ở đây nếu muốn
        // Gate::define('admin-only', fn ($user) => $user->is_admin);
    }
}

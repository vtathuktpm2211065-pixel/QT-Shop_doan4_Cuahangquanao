<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    // Bảng mặc định là messages nên không cần khai báo $table nếu theo chuẩn
    public $timestamps = false; // Vì bạn chỉ dùng created_at, không dùng updated_at

    // Các trường được phép gán hàng loạt
    protected $fillable = [
        'user_id',
        'message_text',
        'sender',
        'read_status',
        'created_at',
    ];

    // Kiểu dữ liệu chuyển đổi
    protected $casts = [
        'read_status' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Quan hệ Message thuộc về User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

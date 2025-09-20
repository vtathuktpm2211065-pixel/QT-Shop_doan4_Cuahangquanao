<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    // Nếu bảng tên khác quy ước, khai báo protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'status',
         'voucher_id', 
        'voucher_code',
          'shipping_fee',
        'payment_method',
        'shipping_address',
        'phone_number',
        'full_name',
    ];

    // Quan hệ với User: 1 Order thuộc 1 User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với OrderItem: 1 Order có nhiều OrderItem
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function cart(): BelongsTo
{
    return $this->belongsTo(Cart::class);
}
public function voucher()
{
    return $this->belongsTo(Voucher::class);
}

}

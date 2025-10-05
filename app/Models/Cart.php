<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'total_amount'];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với các item trong giỏ
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
 public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

public function getCalculatedTotalAttribute()
{
    return $this->items->sum(function ($item) {
        $price = $item->price !== null ? $item->price : ($item->variant->price ?? 0);
        return $price * $item->quantity;
    });
}
public function order()
{
    return $this->hasOne(\App\Models\Order::class);
}
public function shippingInfo()
{
    return $this->hasOne(\App\Models\ShippingInfo::class);
}
}

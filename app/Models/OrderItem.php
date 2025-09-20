<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
protected $fillable = [
    'order_id',
    'product_id',
    'quantity',
    'size',
    'color',
    'unit_price',
    'total_price',
];
    /**
     * Một OrderItem thuộc về một Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mỗi OrderItem liên kết với một Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Nếu bạn có bảng variants, và OrderItem có trường variant_id
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}



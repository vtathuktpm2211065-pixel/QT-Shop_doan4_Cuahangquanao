<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'cart_id',
        'product_id',
        'variant_id', 
        'quantity',
        'added_at',
        'size',
        'color',
        'price',
        'stock_quantity',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        // dùng đúng tên cột trong DB: variant_id
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

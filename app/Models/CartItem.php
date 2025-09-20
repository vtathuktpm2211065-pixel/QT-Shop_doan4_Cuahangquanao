<?php
namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CartItem; 
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

public function variant(): BelongsTo
{
    return $this->belongsTo(ProductVariant::class, 'variant_id');
}

public function cart()
{
    return $this->belongsTo(Cart::class);
}

}

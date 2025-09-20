<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductReview;
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'noi_bat',
        'gioi_tinh',
        'price',
        'category_id',
        'image_url',
        'pho_bien',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->variants->sum('stock_quantity');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    public function reviews()
{
    return $this->hasMany(ProductReview::class);
}

}

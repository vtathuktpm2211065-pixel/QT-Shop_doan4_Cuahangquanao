<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'view_count',
        'last_viewed_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

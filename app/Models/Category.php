<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Quan hệ 1 Category có nhiều Product (nếu có bảng products)
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

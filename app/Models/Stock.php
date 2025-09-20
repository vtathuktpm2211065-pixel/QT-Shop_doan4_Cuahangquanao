<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'variant_id',
        'quantity',
        'type',
        'note',
    ];

    /**
     * Quan hệ đến biến thể sản phẩm
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    
}

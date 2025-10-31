<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLocation extends Model
{
    protected $fillable = [
        'name',
        'address', 
        'latitude',
        'longitude',
        'phone',
        'email',
        'business_hours',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function getBusinessHoursArrayAttribute()
    {
        return $this->business_hours ? json_decode($this->business_hours, true) : [];
    }
}
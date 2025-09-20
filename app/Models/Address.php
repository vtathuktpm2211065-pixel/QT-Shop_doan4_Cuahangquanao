<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'province', 'district', 'ward', 'detail', 'is_default','full_name',
    'phone_number'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
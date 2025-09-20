<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'message',
        'is_read',
    ];

    public function replies()
    {
        return $this->hasMany(SupportReply::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


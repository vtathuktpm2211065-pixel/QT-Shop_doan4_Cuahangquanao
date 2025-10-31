<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportReply extends Model
{
    protected $fillable = [
        'support_request_id',
        'user_id',
        'name',
        'email',
        'phone',
        'reply',
        'is_read',
        'is_admin',
        'attachment'
    ];

    public function supportRequest()
    {
        return $this->belongsTo(SupportRequest::class);
    }

    public function request()
    {
        return $this->belongsTo(SupportRequest::class, 'support_request_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
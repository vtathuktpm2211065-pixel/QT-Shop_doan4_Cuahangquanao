<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIConversation extends Model
{
    // THÊM DÒNG NÀY ĐỂ CHỈ ĐỊNH TÊN BẢNG
    protected $table = 'ai_conversations';

    protected $fillable = [
        'user_id', 'session_id', 'message', 'response', 
        'message_type', 'intent', 'confidence', 'context', 'attachments'
    ];

    protected $casts = [
        'context' => 'array',
        'attachments' => 'array',
        'confidence' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
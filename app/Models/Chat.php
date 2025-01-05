<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $table = 'chats';
    protected $fillable = [
        'user_id',
        'email',
        'image_path',
        'chat_message',
    ];

    public function linkToUser(): BelongsTo {
        return $this->belongsTo(Chat::class, 'user_id');
    }
}

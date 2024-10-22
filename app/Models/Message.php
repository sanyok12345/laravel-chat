<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message', 'user_id', 'reply_to'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageReply::class, 'reply_to');
    }

    public function parentMessage(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MessageReply::class, 'reply_to'); // The message this one is replying to
    }

    public function formatMessage()
    {
        $reply_to_message = null;

        if ($this->parentMessage) {
            $reply_to_message = [
                'id' => $this->parentMessage->id,
                'message' => $this->parentMessage->message,
                'user' => $this->parentMessage->user,
            ];
        }

        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'message' => $this->message,
            'reply_to_message' => $reply_to_message,
            'user' => $this->user,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = ['name'];

    public function messageReactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Message::class, 'message_reactions')
                    ->withPivot('user_id')
                    ->withTimestamps();
    }
}

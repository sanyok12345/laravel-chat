<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['username', 'name', 'email', 'password', 'token'];

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isOwnerOfMessage(Message $message): bool
    {
        return $this->id === $message->user_id;
    }

    public function isOwnerOfReaction(Reaction $reaction): bool
    {
        return $this->id === $reaction->user_id;
    }
}

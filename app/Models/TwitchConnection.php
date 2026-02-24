<?php

namespace App\Models;

use App\Settings\TwitchManagement;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TwitchConnection extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'scope',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'scope' => 'array',
        ];
    }

    protected function isBotConnection(): Attribute
    {
        return Attribute::get(fn () => $this->id === app(TwitchManagement::class)->botConnection);
    }

    protected function isChatConnection(): Attribute
    {
        return Attribute::get(fn () => $this->id === app(TwitchManagement::class)->chatConnection);
    }
}

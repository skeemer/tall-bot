<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitchConnection extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'scope',
    ];

    protected function casts()
    {
        return [
            'expires_at' => 'datetime',
            'scope' => 'array',
        ];
    }
}

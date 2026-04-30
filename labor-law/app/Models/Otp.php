<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'code',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function scopeActiveForEmail(Builder $query, string $email): Builder
    {
        return $query
            ->where('email', $email)
            ->where('expires_at', '>', now());
    }
}

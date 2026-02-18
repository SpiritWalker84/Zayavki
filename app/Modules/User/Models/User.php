<?php

namespace App\Modules\User\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public const ROLE_DISPATCHER = 'dispatcher';
    public const ROLE_MASTER = 'master';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isDispatcher(): bool
    {
        return $this->role === self::ROLE_DISPATCHER;
    }

    public function isMaster(): bool
    {
        return $this->role === self::ROLE_MASTER;
    }
}

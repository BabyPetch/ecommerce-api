<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    const ROLE_CUSTOMER = 'customer';
    const ROLE_ADMIN    = 'admin';

    // Relationship: User มีหลาย Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Helper: เช็คว่าเป็น Admin ไหม
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
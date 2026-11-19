<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    const ROLE_ADMIN   = 1;
    const ROLE_MANAGER = 2;
    const ROLE_CLIENT  = 3;

    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'login',
        'last_name',
        'first_name',
        'middle_name',
        'role_id',
    ];

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->last_name, $this->first_name, $this->middle_name]);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function isAdmin()
    {
        return (int) $this->role_id === self::ROLE_ADMIN;
    }

    public function isManager()
    {
        return (int) $this->role_id === self::ROLE_MANAGER;
    }

    public function isStaff()
    {
        return in_array((int) $this->role_id, [self::ROLE_ADMIN, self::ROLE_MANAGER], true);
    }
}
<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends User
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->role = 'admin';
            $model->status = 'active';
        });

        static::saving(function ($model) {
            if ($model->role !== 'admin') {
                throw new \Exception('Admin model must have admin role');
            }
        });
    }
}

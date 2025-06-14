<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    public function profile()
    {
        return $this->hasOne(\App\Models\Profile::class, 'user_id', 'id');
    }

    public function cart()
    {
        return $this->hasMany(\App\Models\Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
        'failed_login_attempts',
        'locked_until',
        'email_verified_at'
    ];

    /**
     * Get the user's role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'password' => 'hashed',
    ];


    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Check if the user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' && $this->status === 'active';
    }

    /**
     * Check if the user account is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' || $this->status === null;
    }

    /**
     * Check if the user account is locked
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        if ($this->locked_until && now()->lt($this->locked_until)) {
            return true;
        }

        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount();
            return true;
        }

        return false;
    }

    /**
     * Lock the user account
     */
    public function lockAccount(): void
    {
        $this->update([
            'locked_until' => now()->addMinutes(15),
            'status' => 'locked'
        ]);
    }

    /**
     * Unlock the user account
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
            'status' => 'active'
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementLoginAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Check if user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Override accessor to ensure role is lowercase
     */
    public function getRoleAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Override mutator to ensure role is lowercase
     */
    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = strtolower($value);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;



class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'birthday',
        'sex',
        'mobile_number',
        'avatar',
        'is_admin',
        'is_active',
        'role_id',
        'country_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $url = 'http://localhost:8000/api/reset-password/'.$token;
//        $url = 'http://localhost:8000/api/reset-password?token='.$token;

        $this->notify(new ResetPasswordNotification($url));
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function isAdministrator() {
        return $this->is_admin;
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }

}

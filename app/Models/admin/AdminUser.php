<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;    
use Laravel\Sanctum\HasApiTokens;


class AdminUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "admin_user";

    protected $fillable = [       
        'name',
        'unique_id',      
        'mobile_number',
        'email',
        'password',
        'user_type',
        'verified_at',
        'email_verified_at',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'status', // 0=delete, 1=active, 2=inactive
        'deactivate_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];
}

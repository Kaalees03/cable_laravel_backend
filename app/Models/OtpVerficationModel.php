<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpVerficationModel extends Model
{
    use HasFactory;
    protected $table = 'otp_verification';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'mobile_number',
        'email',
        'otp_code',
        'otp_type', // 1=send_otp, 2=resend_otp
        'is_verified', // 0=not_verified, 1=verified, 2=delete
        'verified_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

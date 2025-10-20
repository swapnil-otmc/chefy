<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginOtp extends Model
{
    use HasFactory;
     const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';
    protected $table = 'user_login_otps';

     protected $fillable = [
        'user_id','OTP','start time','end time','status','timestamp',
    ];

// ----------------------foreign Keys--------------------------
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }


    /////// new code
    public function saveOtp(String $userId, String $otp, String $loginTime){
                    $userLoginOtp = new UserLoginOtp();
                    $userLoginOtp->user_id = $userId;
                    $userLoginOtp->OTP = $otp;
                    $userLoginOtp->starttime = $loginTime;
                    $userLoginOtp->save();        
    }
}

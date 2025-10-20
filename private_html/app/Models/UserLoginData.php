<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginData extends Model
{
    use HasFactory;
      const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

    protected $table = 'user_login_data';


    protected $fillable = [
        'users_id','accesscode','device_type_id','token','devicedetail','logintime','logouttime','app_id','status','timestamp',
    ];

    public function deviceType() {
        return $this->belongsTo(DeviceType::class,'device_type_id');
    }

    public function appData() {
        return $this->belongsTo(AppData::class,'app_id');
    }

    public static function checkExistingCode(int $user_id) {
        return UserLoginData::where('user_id', $user_id)
        ->where('app_id', 6)
        ->where('device_type_id', 6)
        ->where('status', 'A')
        ->value('accesscode');
    }

    public static function create(int $user_id, String $access_code) {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $userLoginData = new UserLoginData();
        $userLoginData->user_id = $user_id;
        $userLoginData->accesscode = $access_code;
        $userLoginData->device_type_id = 6;
        $userLoginData->token = "";
        $userLoginData->devicedetail = "Jio TV";
        $userLoginData->logintime = $timestamp;
        $userLoginData->logouttime = null;
        $userLoginData->app_id = 6;
        $userLoginData->status = 'A';
        $userLoginData->created_at = $timestamp;
        $userLoginData->updated_at = $timestamp;
        $userLoginData->save();
    }

    // updated code start
    // Create a user record in User Login Data table

    public function createUser(String $userId, $accessToken, $device,$token,$deviceDetail,$loginTime,$appId){
        $userLoginData = new UserLoginData();
        $userLoginData->user_id = $userId;
        $userLoginData->accesscode = $accessToken;
        $userLoginData->device_type_id = $device;
        $userLoginData->token = $token;
        $userLoginData->devicedetail = $deviceDetail;
        $userLoginData->logintime = $loginTime;
        $userLoginData->app_id = $appId;
        $userLoginData->save();
    }

    // check existing user not
    public function getUser(String $userId, $appId, $access){
        return UserLoginData::where('user_id', $userId)
            ->where('app_id', $appId)
            ->where('accesscode', $access)
            ->where('status', UserLoginData::ACTIVE_STATUS)
            ->first();
    }

    public function logOutUser($userLoginDataId,$logoutTime){
        $userLoginData = UserLoginData::find($userLoginDataId);
        $userLoginData->status = UserLoginData::INACTIVE_STATUS;
        $userLoginData->logouttime = $logoutTime;
        $userLoginData->save();
    }
}

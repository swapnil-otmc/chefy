<?php

namespace App\Http\Controllers;

use Carbon\Carbon;  
use App\Models\User;
use App\Models\UserLoginOtp;
use App\Models\UserLoginData;
// use App\Http\Controllers\PaymentHistory;
use App\Models\Payment;
use App\Models\PaymentHistory;
use App\Http\Controllers\SMSController;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    // private $fixed_otp_for = array("7798180671","9768634049","7066710242");
    private $fixed_otp_for = array("9326571635");
    //send otp to user for login process 

      public function login(Request $request) {

        if(!empty($request['mobile']) || strlen($request['mobile'])>10) {

            // Generate Access Token
            $accessToken = "";
            $accessToken = substr(uniqid(), 0, 8);
            $loginTime = Carbon::now()->format('Y-m-d h:i:s');

            // 4-Digit OTP
            $otp = random_int(1000, 9999);

            // Mobile Number Check
            // $user = new User();
            // $user = $user->where('mobile', $request['mobile'])
            // ->where('status', User::ACTIVE_STATUS)
            // ->first();
            //updated code
            $user = User::numberCheck($request['mobile']);

            if(is_null($user)) {

                // Mobile Number Entry
                $user = new User();
                $user->mobile = $request['mobile'];
                $user->userrole = $request['userRoleId'];
                $user->save();

                // Find User Id
                $userId = $user->id;
            }
            else {
                // Find User Id
                $userId = $user->id;
            }

            // Limit Instances
            self::limitInstance($request, $userId, $accessToken);

            // Unique OTP
            // $userLoginOtp = new UserLoginOtp();
            // $userLoginOtp->user_id = $userId;
            // $userLoginOtp->OTP = $otp;
            // $userLoginOtp->starttime = $loginTime;
            // $userLoginOtp->save();

            // SAME OTP TESTING

            if(in_array($request['mobile'], $this->fixed_otp_for)) {

                // Same OTP
                $userLoginOtp = new UserLoginOtp();
                $userLoginOtp = $userLoginOtp->where('user_id', $userId)
                ->where('status', 'A')
                ->whereNull('endtime')
                ->first();

                if(is_null($userLoginOtp)) {
                    // $userLoginOtp = new UserLoginOtp();
                    // $userLoginOtp->user_id = $userId;
                    // $userLoginOtp->OTP = $otp;
                    // $userLoginOtp->starttime = $loginTime;
                    // $userLoginOtp->save();

                    // update code
                    $userLoginOtp = UserLoginOtp::saveOtp($userId,$otp,$loginTime);

                }
                else {
                    $userLoginOtp = new UserLoginOtp();
                    $userLoginOtp = $userLoginOtp->where('user_id', $userId)
                    ->where('status', UserLoginData::ACTIVE_STATUS)
                    ->where('status', 'A')
                    ->whereNull('endtime')
                    ->first();
                    $otp = $userLoginOtp->OTP;
                }
            }
            else {
                // Unique OTP
                // $userLoginOtp = new UserLoginOtp();
                // $userLoginOtp->user_id = $userId;
                // $userLoginOtp->OTP = $otp;
                // $userLoginOtp->starttime = $loginTime;
                // $userLoginOtp->save();

                //updated code
                $userLoginOtp = UserLoginOtp::saveOtp($userId,$otp,$loginTime);
            }

            //--- Same OTP ---//
            // $userLoginOtp = new UserLoginOtp();
            // $userLoginOtp = $userLoginOtp->where('user_id', $userId)
            // ->where('status', 'A')
            // ->whereNull('endtime')
            // ->first();
            // if(is_null($userLoginOtp)) {
            //     $userLoginOtp = new UserLoginOtp();
            //     $userLoginOtp->user_id = $userId;
            //     $userLoginOtp->OTP = $otp;
            //     $userLoginOtp->starttime = $loginTime;
            //     $userLoginOtp->save();
            // }
            // else {
            //     $userLoginOtp = new UserLoginOtp();
            //     $userLoginOtp = $userLoginOtp->where('user_id', $userId)
            //     ->where('status', UserLoginData::ACTIVE_STATUS)
            //     ->whereNull('endtime')
            //     ->first();
            //     $otp = $userLoginOtp->OTP;
            // }

            // /SAME OTP TESTING

            // Send OTP Code
            $filters = collect();
            $filters->put('mobile', "91".$request['mobile']);
             $msg = "Your Bhaktiflix Verification Code is: ".$otp;
            $filters->put('message', $msg);
            $filters->put('userId', $userId);

            if(!is_null($filters)) {
                $sendSMS = SMSController::readySMS($request, $filters);
            }

            $success['userId'] = $userId;
            $success['access'] = $accessToken;
            $success['otp'] = $otp;
            $success['appId'] = $request['appId'];
            $success['OTPNotification'] = "False";
            return $this->sendResponse($success, 'OTP Send');
        }
        else {
            return $this->sendError('Invalid Mobile Number');
        }
    }
///////Check Otp 
 public function loginCheck(Request $request) {

        if($this->accessCheck($request)) {

            $endTime = Carbon::now()->format('Y-m-d h:i:s');
            $success['userId'] = $request['userId'];
            $success['access'] = $request['access'];
            $success['appId'] = $request['appId'];

            // $userLoginData = new UserLoginData();
            // $userLoginData = $userLoginData->where('user_id', $request['userId'])
            // ->where('app_id', $request['appId'])
            // ->where('accesscode', $request['access'])
            // ->where('status', UserLoginData::ACTIVE_STATUS)
            // ->first();

            // added code 
            $userLoginData = UserLoginData::getUser($request['userId'],$request['appId'],$request['access']);


            if(!is_null($userLoginData)) {

                $userLoginOtp = new UserLoginOtp();
                $userLoginOtp = $userLoginOtp->where('user_id', $request['userId'])
                ->where('OTP', $request['otp'])
                ->where('status', UserLoginData::ACTIVE_STATUS)
                ->first();

                if(is_null($userLoginOtp)) {
                    return $this->sendError('Please Enter Correct OTP');
                }
                else {

                    $userLoginOtpId = $userLoginOtp->id;

                    // Deactivate Old OTP
                    // $userLoginOtp = UserLoginOtp::find($userLoginOtpId);
                    // $userLoginOtp->status = 'I';
                    // $userLoginOtp->endtime = $endTime;
                    // $userLoginOtp->save();

                    if(!in_array($request['mobile'], $this->fixed_otp_for)) {
                        // Deactivate Old OTP
                        $userLoginOtp = UserLoginOtp::find($userLoginOtpId);
                        $userLoginOtp->status = 'I';
                        $userLoginOtp->endtime = $endTime;
                        $userLoginOtp->save();
                    }

                    // User Details Check
                    $user = new User();
                    $user = $user->where('mobile', $request['mobile'])
                    ->where('status', User::ACTIVE_STATUS)
                    ->find($request['userId']);

                    if(empty($user->name) && empty($user->email)) {
                        $success['redirect'] = "Signup";
                    }
                    else {
                        $success['redirect'] = "Home";
                    }

                    return $this->sendResponse($success, 'Login Successfully Done');
                }
            }
            else {
                return $this->sendError('Invalid Access Code');
            }
        }
        else {
            return $this->sendError('User id, Access and app Id not found');
        }
    }

///// logout user 
   public function logout(Request $request) {

        if($this->accessCheck($request)) {

            $logoutTime = Carbon::now()->format('Y-m-d h:i:s');
            $success['userId'] = $request['userId'];
            $success['access'] = $request['access'];
            $success['appId'] = $request['appId'];

            // $userLoginData = new UserLoginData();

            // $userLoginData = $userLoginData->where('user_id', $request['userId'])
            // ->where('app_id', $request['appId'])
            // ->where('accesscode', $request['access'])
            // ->where('status', UserLoginData::ACTIVE_STATUS)
            // ->first();
            /// addedcode-------------------------
            $userLoginData = UserLoginData::getUser($request['userId'],$request['appId'],$request['access']);
       
            if(!is_null($userLoginData)) {

                $userLoginDataId = $userLoginData->id;

                // $userLoginData = UserLoginData::find($userLoginDataId);
                // $userLoginData->status = 'I';
                // $userLoginData->logouttime = $logoutTime;
                // $userLoginData->save();

                ///// added code 
                $userLoginData = UserLoginData::logOutUser($userLoginDataId,$logoutTime);

                return $this->sendResponse($success, 'Logout');
            }
            else {
                return $this->sendError('Please try Again!!!');
            }
        }
        else {
            return $this->sendError('User id, Access and app Id not found');
        }
    }

    /// Checking Acess of user 
    public static function accessCheck(Request $request) {

        if(isset($request->userId) && isset($request->access) && isset($request->appId)) {

            if(!empty($request['userId']) && !empty($request['access']) && !empty($request['appId'])) {

                $user = new User();
                $user = $user->where('status', User::ACTIVE_STATUS)
                ->find($request['userId']);

                if(!is_null($user)) {
                    
                    $userLoginData = new UserLoginData();
                    $userLoginData = $userLoginData->where('user_id', $request['userId'])
                    ->where('app_id', $request['appId'])
                    ->where('accesscode', $request['access'])
                    ->where('status', UserLoginData::ACTIVE_STATUS)
                    ->whereNull('logouttime')
                    ->first();

                    if(!is_null($userLoginData)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    //// limit access 
     private function limitInstance(Request $request, int $userId, string $accessToken) {

        $loginTime = Carbon::now()->format('Y-m-d h:i:s');

        // Check for same device login
        $userLoginDataCount = UserLoginData::where("status", UserLoginData::ACTIVE_STATUS)
        ->where("device_type_id", $request["device"])
        ->where("user_id", $userId)
        ->where("app_id", $request['appId'])
        ->whereNull("logouttime")
        ->count();

        if($userLoginDataCount > 0) {
            // Deactivate access code
            $userLoginData = UserLoginData::where('status', UserLoginData::ACTIVE_STATUS)
            ->where("device_type_id", $request["device"])
            ->where('user_id', $userId)
            ->where('app_id', $request['appId'])
            ->whereNull('logouttime')
            ->update([
                "status" => "I",
                "logouttime" => $loginTime
            ]);
        }

        // Allow Android OR iOS but NOT BOTH
        if(in_array($request["device"], array(1, 2))) {
            $userLoginDataCount = UserLoginData::where("status", UserLoginData::ACTIVE_STATUS)
            ->whereIn("device_type_id", [1, 2])
            ->where("user_id", $userId)
            ->where("app_id", $request['appId'])
            ->whereNull("logouttime")
            ->count();

            if($userLoginDataCount > 0) {
                $userLoginData = UserLoginData::where("status", UserLoginData::ACTIVE_STATUS)
                ->whereIn("device_type_id", [1, 2])
                ->where("user_id", $userId)
                ->where("app_id", $request['appId'])
                ->whereNull("logouttime")
                ->update([
                    "status" => "I",
                    "logouttime" => $loginTime
                ]);
            }
        }

        //Check if user exists in User Login Data table
        $userLoginDataCount = UserLoginData::where('status', UserLoginData::ACTIVE_STATUS)
        ->where('user_id', $userId)
        ->where('app_id', $request['appId'])
        ->whereNotIn('device_type_id', [6])
        ->whereNull('logouttime')
        ->count();

        // If 2 or More active records already exist
        if($userLoginDataCount >= 2) {
            // Deactivate all but the latest one
            $userLoginData = UserLoginData::where('status', UserLoginData::ACTIVE_STATUS)
            ->where('user_id', $userId)
            ->where('app_id', $request['appId'])
            ->whereNull('logouttime')
            ->orderBy("id", "ASC")
            ->limit(1)
            ->update([
                "status" => "I",
                "logouttime" => $loginTime
            ]);
        }
        // Create a user record in User Login Data table
        // $userLoginData = new UserLoginData();
        // $userLoginData->user_id = $userId;
        // $userLoginData->accesscode = $accessToken;
        // $userLoginData->device_type_id = $request['device'];
        // $userLoginData->token = $request['token'];
        // $userLoginData->devicedetail = $request['deviceDetail'];
        // $userLoginData->logintime = $loginTime;
        // $userLoginData->app_id = $request['appId'];
        // $userLoginData->save();

        // Updated code 
        $userLoginData = UserLoginData::createUser($userId,$accessToken,$request['device'],$request['token'],$request['deviceDetail'],$loginTime,$request['appId']);
    }


       public function subscriptionToggle(Request $request) {
        // $validator = $request->validated();
        // dd($validator);
        try {
    $request->validate([
        'mobile_number' => 'required|digits:10',
    ], [
        'mobile_number.digits' => 'The mobile number must be exactly 10 digits.',
    ]);
        // $validator = $this->validateMobileNumber($request);
        // $validator = $this->validateAction($validator);

        // if ($validator->fails()) {
        //     $response = $this->getErrorMessages($validator->errors());
        //     return $this->sendResponse($response, 'Errors Found');
        // }
        $userID = User::getIDFromMobileNumber($request->mobile_number);
        $alreadyPaid = Payment::verifyPaymentByUserID($userID);

        switch ($request->action) {
            case 'activate':
                if ($alreadyPaid) {
                    $response['message'] = 'Already Activated';
                } else {
                    $paymentInfo = $this->getPaymentInfo($userID);
                    $paymentID = Payment::create($paymentInfo);
                    $paymentCleared = PaymentHistory::create($paymentID, $paymentInfo);

                    $response['message'] = $paymentCleared
                        ? 'Activation Successful'
                        : 'Something Went Wrong. Please Try Again.';
                }
                break;
            case 'deactivate':
                if (!$alreadyPaid) {
                    $response['message'] = 'Already Deactivated';
                } else {
                    $paymentStatus = Payment::deactivate($userID);

                    if ($paymentStatus && PaymentHistory::deactivate($userID)) {
                        $response['message'] = 'Deactivation Successful';
                    } else {
                        $response['message'] = 'Something Went Wrong. Please Try Again.';
                    }
                }
                break;
            default:
                $response['message'] = 'Invalid action.';
        }
        return $this->sendResponse($response, 'No Errors Found');
        } catch (\Exception $e) {
    return $this->sendError($e);
}
    }

      private function getPaymentInfo(int $userID) {
        return [
            'userID' => $userID,
            'appID' => config('global.APP_ID'),
            'description' => 'Yearly Subscription',
            'currency' => 'INR',
            'paymentStatus' => 'Success',
            'subscriptionStatus' => 'Active',
            'amount' => config('global.AMOUNT'),
            'comment' => 'Internal',
            'subcriptionEndDate' => Carbon::now()->addYear(1)->format('Y-m-d H:i:s'),
            'timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}




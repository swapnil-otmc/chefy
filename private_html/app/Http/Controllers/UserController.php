<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    //// get profile start
     public function getProfile(Request $request) {

        if(LoginController::accessCheck($request)) {

            $user = User::where('status', User::ACTIVE_STATUS)
            ->where('id', $request['userId'])
            ->get();

            // $getPaymentHistory = PaymentHistory::where('status', PaymentHistory::ACTIVE_STATUS)
            // ->where('user_id', $request['userId'])
            // ->with('payment')
            // ->first();

            /// un commetnt after make payment controller
            // $getPaymentHistory = Payment::where('user_id', $request['userId'])
            // ->where('status', 'A')
            // ->where('p_status', 'Success')
            // ->first();

            // if(!is_null($getPaymentHistory)) {

            //     #$expiry_date =  Carbon::parse($getPaymentHistory->payment['sub_end_date'])->format('j-F-Y');
            //     $expiry_date =  Carbon::parse($getPaymentHistory['sub_end_date'])->format('j-F-Y');
            // }
            // else {
            //     $expiry_date = "NA";
            // }

            if(!is_null($user)) {

                $userArray = array();

                foreach($user as $data) {

                    $details = array();
                    $details['id'] = $data['id'];
                    $details['name'] = is_null($data->name) ? "" : $data->name;
                    $details['email'] = is_null($data->email) ? "" : $data->email;
                    // $details['name'] = $data->name;
                    // $details['email'] = $data->email;
                    $details['mobile'] = $data->mobile;
                    //// uncomment after payment controller
                    // $details['expiry_date'] = $expiry_date;
                    array_push($userArray, $details);
                }

                $success['userId'] = $request['userId'];
                $success['access'] = $request['access'];
                $success['appId'] = $request['appId'];
                $success['is_login'] = true;
                $success['userData'] = $userArray;

                return $this->sendResponse($success, 'User Data');
            }
            else {
                return $this->sendError("User Not Found");
            }
        }
        else {
            $success['userId'] = '0';
            $success['access'] = 'guest';
            $success['appId'] = $request['appId'];
            $success['is_login'] = false;
            $success['userData'] = array();

            return $this->sendResponse($success, 'User Data');
        }
    }


    public function updateProfile(Request $request) {

        if(LoginController::accessCheck($request)) {

            $user = User::where('status', User::ACTIVE_STATUS)
            ->where('id',$request['userId'])
            ->first();

            if(!is_null($user)) {

                $user->name = $request['name'];
                $user->save();

                $user = User::where('status', User::ACTIVE_STATUS)
                ->where('id',$request['userId'])
                ->first();

                $success['userId'] = $request['userId'];
                $success['access'] = $request['access'];
                $success['appId'] = $request['appId'];
                $success['name'] = $user->name;
                // $success['updateUser'] = $updateUserData;

                return $this->sendResponse($success, 'User Data Updated');
            }
            else {
                return $this->sendError("User Not Found");
            }
        }
        else {
            return $this->sendError('Invalid Mobile Number');
        }
    }

      public function deactivateProfile(Request $request) {
        if(LoginController::accessCheck($request)) {
            $user = User::where('status', User::ACTIVE_STATUS)
            ->where('id',$request['userId'])
            ->first();
            if(!is_null($user)) {
                $user->is_delete = 'true';
                $user->status = 'I';
                $user->save();
                $message = "Success";
                return $this->sendResponse($message, 'User Deactivated');
            }
            else {
                return $this->sendError("User Not Found");
            }
        }
        else {
            return $this->sendError('Invalid Mobile Number');
        }
    }
    public function activateProfile(Request $request){
        // check user exist or not 

        $user = User::where('id',$request['userId'])->first();
        
        if(!is_null($user)){
        if($user->status == 'A' && $user->is_delete == 'flase'){
            return $this->sendResponse('User Is Already Activate');
        }
        else{
                $user->is_delete = 'flase';
                $user->status = 'A';
                $user->save();
                $message = "Success";
                return $this->sendResponse($message, 'User Activated');
        }

    }
    else{
            return $this->sendError('User Not Found Please Enter Correct Credentials');
    }
    }


}

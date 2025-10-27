<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\PaymentHistory;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
      private $razorpayId = "rzp_test_cwoFAyX1U6xx9Z";
    private $razorpayKey = "IlFa0e0ab405fcHD4foaQ30x";
    const SHA256 = 'sha256';

       public function initiateOrder(Request $request) {
 if(LoginController::accessCheck($request)) {
        // Generate random receipt id
        $receiptId = Str::random(20);

        // Create an object of razorpay
        $api = new Api($this->razorpayId, $this->razorpayKey);
        // $api = new Api($this->liveRazorpayId, $this->liveRazorpayKey);

        // In razorpay you have to convert rupees into paise we multiply by 100
        // Currency will be INR

        // Creating order
        $order = $api->order->create(array(
            'receipt' => $receiptId,
            'amount' => $request['amount'] * 100,
            'currency' => 'INR'
            )
        );

        // Return response on payment page
        $response = [
            'orderId' => $order['id'],
            'razorpayId' => $this->razorpayId,
            'amount' => $request['amount'] * 100,
            'name' => $request['name'],
            'currency' => 'INR',
            'email' => $request['email'],
            'contactNumber' => $request['contactNumber'],
            'address' => $request['address'],
            'description' => 'Testing description',
        ];

        // Let's checkout payment page is it working
        return $this->sendResponse($response, 'Payment Content Data');
    }
    else {
            return $this->sendError('Something Wrong');
        }
    }



    public function makePayment(Request $request) {

        if(LoginController::accessCheck($request)) {

            $orderId = $request['orderId'];
            $payid = $request['payid'];
            $contact = $request['contact'];
            $data = $request['data'];
            $email = $request['email'];
            $wallet = $request['wallet'];
            $signature = $request['signature'];
            $gateway = $request['gateway'];
            $status = $request['status'];

            // $payload = $orderId . '|' . $payid . $this->razorpayKey;
            // $actualSignature = hash_hmac(self::SHA256, $payload, "");
            // if($signature == $actualSignature){
            //     var_dump("Match");
            // } else {
            //     var_dump("not Match");
            // }
            // var_dump($actualSignature);
            // die;

            $p_status = $status;

            if($status == "Fail") {

                $p_status = "Others";
            }

            $transcation_date = Carbon::now()->format('Y-m-d h:i:s');
            $date = Carbon::now()->addYear(1);
            $date = $date->format('Y-m-d h:i:s');

            $payment = new Payment();
            $payment->user_id = $request['userId'];
            $payment->app_id = $request['appId'];
            $payment->description = "Yearly Subscription";
            $payment->order_id = $orderId;
            $payment->currency = "INR";
            $payment->coupon_id	= 000;
            $payment->amount = config('global.AMOUNT');
            $payment->p_status = $p_status;
            $payment->sub_start_date = Carbon::now()->format('Y-m-d h:i:s');
            $payment->sub_end_date = $date;
            $payment->sub_status = "Active";
            $payment->Transaction_date = $transcation_date;
            $payment->save();

            $payment_id = $payment->id;

            $payment_history = new PaymentHistory();
            $payment_history->user_id = $request['userId'];
            $payment_history->payment_id = $payment_id;
            $payment_history->order_id = $orderId;
            $payment_history->razorpay_id = $payid;
            $payment_history->razorpay_hash = $signature;
            $payment_history->razorpay_response = $data;
            $payment_history->razorpay_status = $status;
            $payment_history->amount = 449;
            $payment_history->p_status = $p_status;
            $payment_history->transaction_date = $transcation_date;
            $payment_history->sub_status = "Active";
            $payment_history->save();

            $success['userId'] = $request['userId'];
            $success['access'] = $request['access'];
            $success['appId'] = $request['appId'];
            $success['status'] = $status;

            return $this->sendResponse($success, 'Payment SuccessFully Done.');
        }
        else {
            return $this->sendError('Something Wrong');
        }
    }

        public function getTransactionHistory(Request $request) {

        if(LoginController::accessCheck($request)) {
            
            $getPaymentHistory = PaymentHistory::where('status', PaymentHistory::ACTIVE_STATUS)
            ->where('user_id', $request['userId'])
            ->where('razorpay_status', 'Success')
            ->with('payment')
            ->get();
           
            dd('hello',  $getPaymentHistory->count());
            if(!is_null($getPaymentHistory)) {

                $historyArray = array();

                foreach($getPaymentHistory as $data) {

                    $details = array();
                    $transaction_date = Carbon::parse($data->transaction_date)->format('Y-m-d');
                    $details['transaction_date'] = $transaction_date;
                    $details['order_id'] = $data->order_id;
                    $details['plan_name'] = ('Yearly Subscribtion');
                    $details['payable_amount'] = $data->amount;
                    $details['order_status'] = $data->p_status;
                    $expiry_date =  Carbon::parse($data->payment['sub_end_date'])->format('Y-m-d');
                    $details['expiry_date'] = $expiry_date;

                    array_push( $historyArray, $details);
                }

                $success['userId'] = $request['userId'];
                $success['access'] = $request['access'];
                $success['appId'] = $request['appId'];
                $success['transaction_history'] = $historyArray;

                return $this->sendResponse($success,'Payment History');
            }
            else {
                return $this->sendError('Payment History not Found');
            }
        }
        else {
            return $this->sendError('Something Wrong');
        }
    }

}

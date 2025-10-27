<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;
     const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';


    Protected $table = 'payment_histories';

protected $fillable = [
        'user_id', 
        'payment_id', 
        'order_id', 
        'razorpay_id', 
        'razorpay_hash', 
        'razorpay_status', 
        'razorpay_response', 
        'amount', 
        'p_status', 
        'Transaction_date', 
        'Sub_status', 
        'status', 
        'created_at',
        'updated_at'
    ];

    public function user() 
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function payment() 
    {
        return $this->belongsTo(Payment::class,'payment_id');
    }

    public static function create(int $paymentID, Array $paymentInfo) 
    {
        $paymentHistory = new PaymentHistory();
        $paymentHistory->user_id = $paymentInfo['userID'];
        $paymentHistory->payment_id = $paymentID;
        $paymentHistory->order_id = $paymentInfo['comment'];
        $paymentHistory->razorpay_id = $paymentInfo['comment'];
        $paymentHistory->razorpay_hash = $paymentInfo['comment'];
        $paymentHistory->razorpay_response = $paymentInfo['comment'];
        $paymentHistory->razorpay_status = $paymentInfo['paymentStatus'];
        $paymentHistory->amount = $paymentInfo['amount'];
        $paymentHistory->p_status = $paymentInfo['paymentStatus'];
        $paymentHistory->transaction_date = $paymentInfo['timestamp'];
        $paymentHistory->sub_status = $paymentInfo['subscriptionStatus'];
        return $paymentHistory->save();
    }

    public static function deactivate(int $userID) 
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        return PaymentHistory::where('user_id', $userID)
        ->where('p_status', 'Success')
        ->update([
            'p_status' => 'Others',
            'sub_status' => 'Inactive',
            'status' => 'I',
            'updated_at' => $timestamp
        ]);
    }

    public static function createRecordUsingCoupon(Payment $payment) 
    {
        $paymentHistory = new PaymentHistory();
        $paymentHistory->user_id = $payment->user_id;
        $paymentHistory->payment_id = $payment->id;
        $paymentHistory->order_id = $payment->description;
        $paymentHistory->razorpay_id = $payment->description;
        $paymentHistory->razorpay_hash = $payment->description;
        $paymentHistory->razorpay_response = $payment->description;
        $paymentHistory->razorpay_status = $payment->p_status;
        $paymentHistory->amount = $payment->amount;
        $paymentHistory->p_status = $payment->p_status;
        $paymentHistory->transaction_date = $payment->Transaction_date;
        $paymentHistory->sub_status = $payment->sub_status;
        return $paymentHistory->save();
    }
}

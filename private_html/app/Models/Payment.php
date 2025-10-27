<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

    protected $pStatus = [
        'Payment Initiate',
        'Success',
        'Others'
    ];
    Protected $table = 'payments';

     protected $fillable = [
        'user_id','app_id','description','order_id','image','currancy','coupan_id','amount','p_status','sub_start_date','sub_end_date','Transaction_date','sub_status','status','timestamp',
    ];

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function appData() {
        return $this->belongsTo(AppData::class,'app_id');
    }

    public function coupanCode() {
        return $this->belongsTo(CoupanCode::class,'coupan_id');
    }

    public static function verifyPaymentByUserID(int $user_id) {
        $count = Payment::where('user_id', $user_id)
        ->where('p_status', 'Success')
        ->count();
        return (bool) ($count > 0);
    }

    public static function create(Array $paymentInfo) {
        $payment = new Payment();
        $payment->user_id = $paymentInfo['userID'];
        $payment->app_id = $paymentInfo['appID'];
        $payment->description = $paymentInfo['description'];
        $payment->order_id = $paymentInfo['comment'];
        $payment->currency = 'INR';
        $payment->coupon_id = 000;
        $payment->amount = $paymentInfo['amount'];
        $payment->p_status = $paymentInfo['paymentStatus'];
        $payment->sub_start_date = $paymentInfo['timestamp'];
        $payment->sub_end_date = $paymentInfo['subcriptionEndDate'];
        $payment->sub_status = $paymentInfo['subscriptionStatus'];
        $payment->Transaction_date = $paymentInfo['timestamp'];
        $payment->save();

        return $payment->id;
    }

    public static function deactivate(int $userID) {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        return Payment::where('user_id', $userID)
        ->where('p_status', 'Success')
        ->update([
            'image' => 'Closed',
            'p_status' => 'Others',
            'sub_end_date' => $timestamp,
            'sub_status' => 'Inactive',
            'status' => 'I',
            'updated_at' => $timestamp
        ]);
    }

    public static function getPayment($userId) 
    {
        return self::where('user_id', $userId)
            ->where('p_status', 'Success')
            ->first();
    }

    public static function extendSubscriptionUsingCoupon($payment, $coupon)
    {
        
        $payment->sub_end_date = Carbon::parse($payment->sub_end_date)
            ->addDays($coupon->duration)
            ->format('Y-m-d H:i:s');
        $payment->coupon_id = $coupon->id;
        $payment->description = 'Subscription extended by ' . $coupon->duration . ' days using coupon.';
        $payment->updated_at = now();
        
        return $payment->save();
    }

    public static function createRecordUsingCoupon($userId, $couponId, $duration)
    {
        $timestamp = Carbon::now()
            ->format('Y-m-d H:i:s');

        // Create Payment Record
        $payment = new Payment();
        $payment->user_id = $userId;
        $payment->app_id = config('global.APP_ID');
        $payment->description = 'Coupon Subscription for ' . $duration . ' days.';
        $payment->order_id = 'Coupon ID Used - ' . $couponId;
        // $payment->currancy = 'INR';
        // Updated Code
        $payment->currency = 'INR';
        /////
        // $payment->coupan_id = $couponId;
        //// Updated Code
        $payment->coupon_id = $couponId;
        $payment->amount = config('global.AMOUNT');
        $payment->p_status = 'Success';
        $payment->sub_start_date = $timestamp;
        $payment->sub_end_date = Carbon::now()
            ->addDays($duration)
            ->format('Y-m-d H:i:s');
        $payment->sub_status = 'Active';
        $payment->Transaction_date = $timestamp;
        $payment->save();

        return $payment;
    }
}

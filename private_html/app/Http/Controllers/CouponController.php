<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CouponRequest;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Models\Payment;
use App\Models\PaymentHistory;

// use App\Http\Controllers\LoginController;
use App\Models\Coupon;



class CouponController extends Controller
{
    //
     public function activateCoupon(CouponRequest $request) 
    {
        $validated = $request->validated();

        // if(!AuthController::verifyAccess($validated['userId'], $validated['access'])) 

        //// Updated code
        if(!LoginController::accessCheck($request)) 
        //////
        {
            return $this->jsonResponse('failed', 'Invalid Access . Please try again.', 400);
            /// Updated Code
            // return $this->sendError('failed', 'Invalid Access . Please try again.', 400);
        }

        $coupon = Coupon::findCoupon($validated['couponCode']);
       
        if(!$coupon)
        {
            return $this->jsonResponse('failed', 'Invalid or expired. Coupon Code.', 404);
            /// Updated code 
            // return $this->sendError('failed', 'Invalid or expired. Coupon Code.', 404);
            //////
        }

        $updated = Coupon::activateCoupon($coupon);
        
        if(!$updated) 
            
        {
            
            return $this->jsonResponse('failed', 'Failed to apply coupon. Please try again.', 500);
            //////
            ///// Updatd Code 
            // return $this->sendError('failed', 'Failed to apply coupon. Please try again.', 500);
        }
        
        $alreadyPaid = Payment::getPayment($validated['userId']);
        
        if($alreadyPaid) 
        {
            
            // Extend Subscription
            $alreadyPaid = Payment::extendSubscriptionUsingCoupon($alreadyPaid, $coupon);
            
            if($alreadyPaid)
            {
                return $this->jsonResponse('success', 'Subscription extended successfully.');
                // Updaetd Code
                // return $this->sendResponse('success', 'Subscription extended successfully.');
            }
        }
        else 
        {
            // Create Payment Record
            $payment = Payment::createRecordUsingCoupon(
                $validated['userId'], 
                $coupon->id, 
                $coupon->duration
            );

            // Record Payment History
            $paymentClear = PaymentHistory::createRecordUsingCoupon($payment);

            if($paymentClear) 
            {
                
                return $this->jsonResponse('success', 'Coupon Applied Successfully.', 200);
                //// Updated Code
                // return $this->sendResponse('success', 'Coupon Applied Successfully.', 200);
            }
        }
        return $this->jsonResponse('failed', 'Subscription updated. However, failed to update payment record.', 500);
        //// Updated Code
        // return $this->sendResponse('failed', 'Subscription updated. However, failed to update payment record.', 500);
    }


    private function jsonResponse(string $status, string $message, int $httpCode = 200)
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
        ], $httpCode);
    }
}

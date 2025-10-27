<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    Protected $table = 'coupons';
    protected $fillable = [
        'code','amount','discount','duration','uses','expiry_date','last_used','status','timestamp',
    ];



     public static function findCoupon($code)
    {
        return self::where('code', $code)
            ->where('status', 'A')
            ->where('discount', 100)
            ->where('uses', '>', 0)
            ->where(function ($query) {
                $query->where('expiry_date', '>', now())
                    ->orWhereNull('expiry_date');
            })
            ->first();
    }

    public static function activateCoupon($coupon)
    {
        $coupon->uses -= 1;
        $coupon->last_used = now();
        $coupon->status = $coupon->uses <= 0 ? 'I' : 'A';
        $coupon->updated_at = now();

        return $coupon->save();
    }
}

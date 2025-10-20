<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsHistory extends Model
{
    use HasFactory;
    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';
    protected $table = 'sms_histories';
    protected $fillable = [
        'mobile','sms_type','user_id','message','message_status','message_deliverytime','route','sms_api','status','timestamp',
    ];

    
// -----------------------foreign Keys------------------------

    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    } 
}

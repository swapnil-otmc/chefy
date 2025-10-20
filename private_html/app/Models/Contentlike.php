<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contentlike extends Model
{
    use HasFactory;
    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';
    const CONTENT_LIKE = 1;

    protected $table = "content_likes";

    //
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'like','content_id','user_id','app_id','status','timestamp',
    ];
    


// -----------------------foreign Keys------------------------

    public function contentData(){
        return $this->belongsTo(ContentData::class,'content_id');
    }
    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }   

    public function appData(){
        return $this->belongsTo(AppData::class,'app_id');
    }   
}

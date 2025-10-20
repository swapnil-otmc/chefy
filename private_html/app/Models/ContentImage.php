<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends Model
{
    use HasFactory;
    Protected $table = "content_images";

    protected $fillable = [
         'content_data_id','h_image','v_image','app_data_id','status','timestamp',
    ];
    


// -----------------------foreign Keys------------------------

    
    public function appData(){
        return $this->belongsTo(AppData::class,'app_data_id');
    }
    
     
    public function contentData(){
        return $this->belongsTo(ContentData::class,'content_data_id');
    }
}

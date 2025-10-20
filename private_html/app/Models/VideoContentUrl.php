<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoContentUrl extends Model
{
    use HasFactory;
     const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';


    Protected $table = "video_content_urls";

    protected $fillable = [
        'url','videourl_type_id','content_data_id', 'skip_intro', 'app_id','status','timestamp',
    ];
    



// -----------------------foreign Keys------------------------

    
    public function videoURLType(){
        return $this->belongsTo(VideourlType::class,'videourl_type_id');
    } 

    public function appData(){
        return $this->belongsTo(AppData::class,'app_id');
    } 

    public function contentData(){
        return $this->belongsTo(ContentData::class,'content_data_id');
    } 
}

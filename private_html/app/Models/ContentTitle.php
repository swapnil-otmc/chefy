<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTitle extends Model
{
    use HasFactory;
    Protected $table = "content_titles";

    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

     protected $fillable = [
        'h_image','v_image','name', 'app_data_id','category_type_id','status','timestamp',
    ];
    


// -----------------------foreign Keys------------------------

    
    public function appData(){
        return $this->belongsTo(AppData::class,'app_data_id');
    }
    
     
    public function categoryType(){
        return $this->belongsTo(CategoryType::class,'category_type_id');
    } 
}

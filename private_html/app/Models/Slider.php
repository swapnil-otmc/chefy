<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    Protected $table =  'sliders';

    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

    protected $fillable = [
        'name', 'image', 'priority','content_id','category_id','status','app_data_id','timestamp',
    ];


// -----------------------foreign Keys------------------------
       public function contentType(){
        return $this->belongsTo(ContentType::class,'content_id');
    } 

    public function categoryType(){
        return $this->belongsTo(CategoryType::class,'category_id');
    } 
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoryType extends Model
{
    use HasFactory;
    Protected $table = "sub_category_types";

    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';
    const HORIZONTAL_STATUS = "Horizontal";  


     protected $styleThumb = [
        'Horizontal',
        'Vertical'
    ];


    protected $fillable = [
        'name','poster', 'styleType','thumbSize','priority','home_priority','category_id','status','timestamp',
    ];


    public function getStyleThumbAttribute($value)
    {
        return Arr::get($this->styleThumb, $value);
    }

}

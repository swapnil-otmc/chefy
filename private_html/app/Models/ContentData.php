<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentData extends Model
{
    use HasFactory;
    Protected $table = "content_data";

    protected $fillable = [
        'name','title','description','sub_category_id','category_type_id','videourl_type_id','starcast','director','crew','content_type_id','tags','genre','sub_description','pg_rating_id','language_id','release_date','duration','status','timestamp',
    ]; 


//------------------------foreign Keys----------------------

    public function subCategoryType(){
        return $this->hasMany(SubCategoryType::class,'id','sub_category_id');
    }

    public function categoryType(){
        return $this->belongsTo(CategoryType::class, 'category_type_id');
    }

    public function videoURLType(){
        return $this->hasMany(VideourlType::class, 'id','videourl_type_id');
    }

    public function contentImage(){
        return $this->belongsTo(ContentImage::class, 'id','content_data_id');
    } 

    public function contentType(){
        return $this->hasMany(ContentType::class, 'id','content_type_id');
    }

    public function language(){
        return $this->hasMany(Language::class, 'id', 'language_id');
    }
public function videoUrls()
{
    return $this->hasMany(VideoContentUrl::class, 'content_data_id', 'id');
}


}

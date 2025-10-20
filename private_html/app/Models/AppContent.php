<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppContent extends Model
{
    use HasFactory;
    Protected $tabale = 'app_contents';

     const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';


     protected $fillable = [
        'content_data_id',
        'app_data_id',
        'category_id',
        'sub_category_id',
        'content_types_id',
        'content_title_id',
        'sub_category_priority',
        'status',
        'timestamp'
    ];

            public function contentData() {
        return $this->belongsTo(ContentData::class,'content_data_id');
    } 
    
    public function appData() {
        return $this->belongsTo(AppData::class,'app_data_id');
    } 

    public function categoryType() {
        return $this->belongsTo(CategoryType::class,'category_id');
    } 

    public function subCategoryType() {
        return $this->belongsTo(SubCategoryType::class,'sub_category_id');
    } 
    
    public function contentType() {
        return $this->belongsTo(ContentType::class,'content_types_id');
    } 

    public function contentTitle() {
        return $this->belongsTo(ContentTitle::class, 'content_title_id');
    } 

    public function contentImage() {
        return $this->belongsTo(ContentImage::class, 'content_data_id','content_data_id');
    } 
    
    public function appContentImage() {
        return $this->belongsTo(AppContentImage::class, 'content_data_id', 'id');
    }

    public function videoContentUrl() {
        return $this->belongsTo(VideoContentUrl::class, 'content_data_id', 'id');
    }

    public static function getContent($appID, $ContentID) {
        // dd($appID);
        return AppContent::where('app_data_id', $appID)
        ->where('content_data_id', $ContentID)
        ->where('status', 'A')
        ->with('contentData', 'subCategoryType', 'contentType', 'appData', 'contentImage', 'videoContentUrl', 'categoryType')
        ->first();
    }

    /// addded code
   

    


}

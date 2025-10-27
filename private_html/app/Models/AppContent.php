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

   

  public function appContent($request,$id)
    {
        // Initialize the query
        // $query = AppContent::where('app_data_id', 11) // Example app_data_id
        //     ->where('status', self::ACTIVE_STATUS)
        //     ->with([
        //         'contentData' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'subCategoryType' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'contentType' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'appData' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'contentImage' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'videoContentUrl' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'categoryType' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         },
        //         'contentTitle' => function ($query) {
        //             $query->where('status', self::ACTIVE_STATUS);
        //         }
        //     ]);

        // // Apply ordering based on certain conditions (priority)
        // // if (in_array($request['sub_category_id'], [44, 45, 46])) {
        // //     // For these specific sub_category_ids, order by ASC
        // //     $query->orderBy('priority', 'ASC');
        // // } else {
        // //     // For other cases, order by DESC
        // //     $query->orderBy('priority', 'DESC');
        // // }

        // // If on Homepage, limit results (reduce data)
        // // if ($request['redirection'] == 'home') {
        // //     $query->take(30); // Limit results to 30
        // // }

        // // // Additional filtering by menuId if needed
        // // if ($request['menu_id'] !== 0) {
        // //     $query->where('category_id', $request['menu_id']);
        // // }

        // // Execute the query and fetch the results
        // return $query->get();

        $query = AppContent::where('sub_category_id', $id)
            ->with([
                'contentData' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'subCategoryType' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'contentType' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'appData' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'contentImage' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'videoContentUrl' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'categoryType' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                },
                'contentTitle' => function ($query) {
                    $query->where('status', self::ACTIVE_STATUS);
                }
            ])
            ->orderBy('priority','DESC')
        ->get();
        //   dd( $query->toarray());
        return $query;
    }


}

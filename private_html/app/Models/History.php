<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;  

class History extends Model
{
      const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';
    use HasFactory;
    Protected $table = 'history';

       protected $fillable = ['user_id', 'app_id', 'content_data_id', 'status', 'timestamp'];

       /**
     * Join User
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appData() {
        return $this->belongsTo(AppData::class, 'app_id');
    }

    public function contentData() {
        return $this->belongsTo(ContentData::class, 'content_data_id');
    }

    public function contentImage() {
        return $this->belongsTo(ContentImage::class, 'content_data_id', 'content_data_id');
    }

    /**
     * Get User's Content History
     * @return Collection
     */
    public static function getContents($request) {
        return History::where('status', History::ACTIVE_STATUS)
        ->where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->orderBy('updated_at', 'DESC')
        ->with('contentData', 'contentImage')
        ->get()
        ->take(10);
    }

    /**
     * Get Content from History
     * @return Array
     */
    public static function getContent($request) {
        return History::where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->where('content_data_id', $request['contentId'])
        ->where('status', History::ACTIVE_STATUS)
        ->first();
    }

    /**
     * Add New Content to History
     * @return String Content ID
     */
    public static function addContent($request) {
        $content = new History();
        $content->user_id = $request['userId'];
        $content->app_id = $request['appId'];
        $content->content_data_id = $request['contentId'];
        $content->save();
        return $content->content_data_id;
    }

    /**
     * Update Existing Content in History
     * @return String Content ID
     */
    public static function updateContent($request, $content) {
        $content->user_id = $request['userId'];
        $content->app_id = $request['appId'];
        $content->content_data_id = $request['contentId'];
        $content->updated_at = Carbon::now()->format('Y-m-d h:i:s');
        $content->save();
        return $content->content_data_id;
    }

    public static function addFromContinueWatching($request) {
        $content = History::where('status', History::ACTIVE_STATUS)
        ->where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->with('contentData', 'contentImage')
        ->first();
        if($content) {
            return $content->update(['content_data_id' => $request['contentId']]);
        }
        return false;
    }
}

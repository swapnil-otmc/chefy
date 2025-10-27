<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContinueWatching extends Model
{
    use HasFactory;
     const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

    Protected $table = 'continue_watchings';

     protected $fillable = ['user_id', 'app_id', 'content_data_id', 'history_id', 'watch_seconds', 'status', 'timestamp'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appData() {
        return $this->belongsTo(AppData::class, 'app_id');
    }

    public function contentData() {
        return $this->belongsTo(ContentData::class, 'content_data_id');
    }

    public function history() {
        return $this->belongsTo(History::class, 'history_id');
    }

    public function contentImage() {
        return $this->belongsTo(ContentImage::class, 'content_data_id', 'content_data_id');
    }

    public function videoContentUrl() {
        return $this->belongsTo(VideoContentUrl::class, 'content_data_id', 'id');
    }

    public static function getContents($request) {
        return ContinueWatching::where('status', ContinueWatching::ACTIVE_STATUS)
        ->where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->orderBy('updated_at', 'DESC')
        ->with('contentData', 'contentImage')
        ->get();
    }

    public static function getContent($request) {
        return ContinueWatching::where('status', ContinueWatching::ACTIVE_STATUS)
        ->where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->where('content_data_id', $request['contentId'])
        ->first();
    }

    public static function updateContent($request) {
        $content = ContinueWatching::where('status', ContinueWatching::ACTIVE_STATUS)
        ->where('user_id', $request['userId'])
        ->where('app_id', $request['appId'])
        ->where('content_data_id', $request['contentId']) 
        ->first();
        if($content) {
            $content->content_data_id = $request['contentId'];
            $content->watch_seconds = $request['watchSecond'];
            $content->save();
        }
        return $content->watch_seconds;
    }

    public static function addContent($request) {
        $content = new ContinueWatching();
        $content->user_id = $request['userId'];
        $content->app_id = $request['appId'];
        $content->content_data_id = $request['contentId'];
        $content->history_id = History::addContent($request);
        $content->watch_seconds = $request['watchSecond'];
        $content->save();
        return $content->watch_seconds;
    }
    public static function deactivateContent($request) {
        $content = ContinueWatching::where('user_id', $request['userId'])
        ->where('app_id', $request['appId'])
        ->where('id', $request['continueWatchingId'])
        ->where('status', ContinueWatching::ACTIVE_STATUS)
        ->first();
        if($content) {
            return $content->update(['status' => 'I']);
        }
        return false;
    }
}

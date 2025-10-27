<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchLater extends Model
{
     const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';
    use HasFactory;
    Protected $table = 'watch_laters';

    protected $fillable = ['user_id', 'app_id', 'content_data_id', 'history_id', 'status', 'timestamp'];

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

    /**
     * Get User's Watchlist
     * @return Collection
     */
    public static function getContents($request) {
        return WatchLater::where('status', WatchLater::ACTIVE_STATUS)
        ->where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->orderBy('updated_at', 'DESC')
        ->with('contentData', 'contentImage')
        ->get();    
        // ->take(10);
    }

    public static function getContent($request) {
        return WatchLater::where('status', WatchLater::ACTIVE_STATUS)
        ->where('app_id', $request['appId'])
        ->where('user_id', $request['userId'])
        ->where('content_data_id', $request['contentId'])
        ->with('contentData')
        ->first();
    }

    public static function addContent($request, $historyContentID) {
        $watchlistContent = new WatchLater();
        $watchlistContent->user_id = $request['userId'];
        $watchlistContent->app_id = $request['appId'];
        $watchlistContent->content_data_id = $request['contentId'];
        $watchlistContent->history_id = $historyContentID;
        $watchlistContent->save();
        return $watchlistContent;
    }

    public static function deactivateContent($request) {
        $watchlistContent = WatchLater::where('user_id', $request['userId'])
        ->where('app_id', $request['appId'])
        ->where('id', $request['watchlaterId'])
        ->where('status', WatchLater::ACTIVE_STATUS)
        ->first();
        if($watchlistContent) {
            return $watchlistContent->update(['status' => 'D']);
        }
        return false;
    }
}

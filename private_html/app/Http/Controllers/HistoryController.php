<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\WatchLater;
use App\Models\ContinueWatching;


use App\Http\Controllers\LoginController;

class HistoryController extends Controller
{
    //
    
    /**
     * Add Content to User's Content History
     */
    public function addHistory(Request $request) {
         
        if(LoginController::accessCheck($request)) {
            $response['userId'] = $request['userId'];
            $response['access'] = $request['access'];
            $response['app_id'] = $request['appId'];
            $response['content_data_id'] = $request['contentId'];
            $success['addHistory'] = $this->getHistoryContentID($request);
            return $this->sendResponse($response, 'Content is added in History');
        }
        return $this->sendError('Invalid Mobile Number');
    }

///// get HistoryContentID
       private function getHistoryContentID($request) {
        $content = History::getContent($request);
        if(is_null($content)) {
            return History::addContent($request);
        }
        return History::updateContent($request, $content);
    }
///// get history data

 /**
     * Get User's Content History 
     */
    public function getHistory(Request $request) {
        
        if(LoginController::accessCheck($request)) {
            $response['userId'] = $request['userId'];
            $response['access'] = $request['access'];
            $response['appId'] = $request['appId'];
            $response['history'] = $this->setHistoryContents($request);
            
            return $this->sendResponse($response, 'History');
        }
        return $this->sendError('Invalid Mobile Number');
    }

    /**
     * Content Formating for History Contents
     */
    private function setHistoryContents($request) {
        
        $contents = array();
        
        foreach(History::getContents($request) as $historyContent) {
            
            $content = array();
            $content['content_id'] = $historyContent->contentData['id'];
            $content['name'] = $historyContent->contentData['name'];
            $content['duration'] = gmdate("H:i:s", $historyContent->contentData['duration']);
            $content['h_image'] = (empty($historyContent->contentImage['h_image'])) ? "" : config('global.CONTENT_IMAGE_PATH').$historyContent->contentImage['h_image'];
            $content['v_image'] = (empty($historyContent->contentImage['v_image'])) ? "" : config('global.CONTENT_IMAGE_PATH').$historyContent->contentImage['v_image'];
            
            array_push($contents, $content);
        }
       
        return $contents;
    }

     /**
     * Add Content to Watch Later
     */
    public function addWatchlist(Request $request) {
        if(LoginController::accessCheck($request)) {
            if(is_null(WatchLater::getContent($request))) {
                $historyContentID = $this->getHistoryContentID($request);
                $watchlistContent = WatchLater::addContent($request, $historyContentID);
                $response['userId'] = $request['userId'];
                $response['access'] = $request['access'];
                $response['app_id'] = $request['appId'];
                $response['content_id'] = $watchlistContent->content_data_id;
                $response['watch_later_id'] = $watchlistContent->id;
                $response['content_name'] = $watchlistContent->contentData['name'];
                return $this->sendResponse($response, 'Content is added in Watchlater List');
            }
            return $this->sendError('Already exist in your WatchLater List');
        }
        return $this->sendError('Something Wrong');
    }

       /**
     * Return Content from Watch Later
     */
    public function getWatchlist(Request $request) {
        if(LoginController::accessCheck($request)) {
            $response['userId'] = $request['userId'];
            $response['access'] = $request['access'];
            $response['appId'] = $request['appId'];
            $response['Watch Later'] = $this->setWatchlist($request);
            if(!empty($this->setWatchlist($request))) {
                $message = 'WatchList';
            }
            else {
                $message = 'WatchList_Empty';
            }
            return $this->sendResponse($response, $message);
        }
        return $this->sendError('Invalid Mobile Number');
    }

       /**
     * Content Formating for Watchlist Contents
     */
    private function setWatchlist($request) {
        $contents = array();
        foreach(WatchLater::getContents($request) as $watchlistContent) {
            $content = array();
            $content['watchlaterId'] = $watchlistContent->id;
            $content['content_id'] = $watchlistContent->contentData['id'];
            $content['name'] = $watchlistContent->contentData['name'];
            $content['duration'] = gmdate("H:i:s", $watchlistContent->contentData['duration']);
            $content['h_image'] = (empty($watchlistContent->contentImage['h_image'])) ? "" : config('global.CONTENT_IMAGE_PATH').$watchlistContent->contentImage['h_image'];
            $content['v_image'] = (empty($watchlistContent->contentImage['v_image'])) ? "" : config('global.CONTENT_IMAGE_PATH').$watchlistContent->contentImage['v_image'];
            array_push($contents, $content);
        }
        return $contents;
    }

     /**
     * Remove Content from Watch Later
     */
    public function removeWatchlist(Request $request)
{
    // Validate the incoming request
    // $request->validate([
    //     'watchlaterId' => 'required|integer|exists:watch_laters,content_data_id',
    // ], [
    //     'watchlaterId.exists' => 'The selected Watch Later ID does not exist.',
    // ]);

        if(LoginController::accessCheck($request)) {
            if(WatchLater::deactivateContent($request)) {
                $response['userId'] = $request['userId'];
                $response['access'] = $request['access'];
                $response['app_id'] = $request['appId'];
                return $this->sendResponse($response, 'Deleted Successfully');
            }
            return $this->sendError('Empty');
        }
        return $this->sendError('Something Wrong');
    }

       /**
     * Add Content to Continue Watching
     */
    public function addContinueWatching(Request $request) {
        if(LoginController::accessCheck($request)) {
            $addcontinuewatching = ContinueWatching::getContent($request);
            $response['userId'] = $request['userId'];
            $response['access'] = $request['access'];
            $response['app_id'] = $request['appId'];
            if($addcontinuewatching) {
                History::addFromContinueWatching($request);
                $response['watch_seconds'] = ContinueWatching::updateContent($request);
                $message = 'Content is Updated in your ContinueWatching List';
            }
            else {
                $response['watch_seconds'] = ContinueWatching::addContent($request);
                $message = 'Content is added in your ContinueWatching List';
            }
            return $this->sendResponse($response, $message);
        }
        return $this->sendError('Something Wrong');
    }


     /**
     * Return Content from Continue Watching
     */
    public function getContinueWatching(Request $request) {
        if(LoginController::accessCheck($request)) {
            $response['userId'] = $request['userId'];
            $response['access'] = $request['access'];
            $response['appId'] = $request['appId'];
            $response['Continue Watch'] = $this->setContinueWatchingContent($request);
            return $this->sendResponse($response, 'Continue Watch List');
        }
        return $this->sendError('Invalid Mobile Number');
    }

      private function setContinueWatchingContent($request) {
        $contents = array();
        foreach(ContinueWatching::getContents($request) as $continueWatchingContent) {
            $content = array();
            $content['continueWatchingId'] = $continueWatchingContent->id;
            $content['content_id'] = $continueWatchingContent->contentData['id'];
            $content['name'] = $continueWatchingContent->contentData['name'];
            $content['genre'] = $continueWatchingContent->contentData->genre;
            $content['duration'] = $continueWatchingContent->contentData['duration'];
            $content['watch_seconds'] = $continueWatchingContent->watch_seconds;
            $content['h_image'] = (empty($continueWatchingContent->contentImage['h_image'])) ? '' : config('global.CONTENT_IMAGE_PATH').$continueWatchingContent->contentImage['h_image'];
            $content['v_image'] = (empty($continueWatchingContent->contentImage['v_image'])) ? '' : config('global.CONTENT_IMAGE_PATH').$continueWatchingContent->contentImage['v_image'];
            $content['videoURL'] = (empty($continueWatchingContent->videoContentUrl['url'])) ? '' : config('global.CONTENT_VIDEO_PATH').$continueWatchingContent->videoContentUrl['url'];
            array_push($contents, $content);
        }
        return $contents;
    }


       /**
     * Remove Content from Continue Watching
     */
    public function deleteContinueWatching(Request $request) {
        if(LoginController::accessCheck($request)) {
            if(ContinueWatching::deactivateContent($request)) {
                $success['userId'] = $request['userId'];
                $success['access'] = $request['access'];
                $success['app_id'] = $request ['appId'];
                return $this->sendResponse($success, 'Deleted Successfully');
            }
            return $this->sendError('Empty');
        }
        return $this->sendError('Something Wrong');
    }

}

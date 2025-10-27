<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\AppContent;
use App\Models\ContentType;
use App\Models\Contentlike;
use App\Models\CategoryType;

class ContentController extends Controller
{
    //

     public function getContentDetailByID(Request $request) {
        if(LoginController::accessCheck($request)) {
            $user_id = $request['userId'];
            $access_code = $request['access'];
            $is_login = true;
            UserController::validateSubscription($user_id);
        }
        else {
            $user_id = '0';
            $access_code = 'guest';
            $is_login = false;
        }
        $continue_Watching = 0;
        $watchlater_status = 'False';
        $like_status = 'False';
        $sub_status = 'Fail';
        $appContent = AppContent::getContent($request["appId"], $request["contentId"]);
        if(!empty($request["appId"]) && !empty($request["contentId"])) {
            if(LoginController::accessCheck($request)) {
                $continueWatching = ContinueWatching::where("status", "A")
                ->where("app_id", $request["appId"])
                ->where("user_id", $user_id)
                ->where("content_data_id", $request["contentId"])
                ->first();
                if(!is_null($continueWatching)) {
                    $continue_Watching = $continueWatching->watch_seconds;
                }
                $watchLaterStatus = WatchLater::where("app_id", $request["appId"])
                ->where("user_id", $user_id)
                ->where("content_data_id", $request["contentId"])
                ->where("status", "A")
                ->first();
                if(!is_null($watchLaterStatus)) {
                    $watchlater_status = "True";
                    $watch_later_id = $watchLaterStatus->id;
                }
                $contentLikeStatus = Contentlike::where("app_id", $request["appId"])
                ->where("user_id", $user_id)
                ->where("content_id", $request["contentId"])
                ->where("like", Contentlike::CONTENT_LIKE)
                ->where("status", "A")
                ->first();
                if(!is_null($contentLikeStatus) && $contentLikeStatus->like == "1") {
                    $like_status = "True";
                }
                $payment = Payment::where("app_id", $request["appId"])
                ->where("user_id", $user_id)
                ->where("status", "A")
                ->orderBy("id","desc")
                ->first();
                if(!is_null($payment)) {
                    if($payment->p_status == "Success") {
                        $sub_status = "Success";
                    }
                }
            }
            $contentLike = Contentlike::where("app_id", $request["appId"])
            ->where("content_id", $request["contentId"])
            ->where("like", Contentlike::CONTENT_LIKE)
            ->where("status", "A")
            ->get();
            $contentLikeCount = $contentLike->count();
        }
        else {
            return $this->sendError("Parameter Missing");
        }
        if(!is_null($appContent)) {
            $contentDetails["id"] = $appContent->contentData->id;
            $contentDetails["name"] = $appContent->contentData->name;
            $contentDetails["description"] = $appContent->contentData->description;
            $contentDetails["starcast"] = $appContent->contentData->starcast;
            $contentDetails["director"] = $appContent->contentData->director;
            $contentDetails["crew"] = $appContent->contentData->crew;
            $contentDetails["tags"] = $appContent->contentData->tags;
            $contentDetails["genre"] = $appContent->contentData->genre;
            $contentDetails["director"] = $appContent->contentData->director;
            $contentDetails["sub_description"] = $appContent->contentData->sub_description;
            $release_date = Carbon::parse($appContent->contentData->release_date)->format('Y-m-d');
            $contentDetails["release_te"] = $release_date;
            $duration = gmdate("H:i:s", $appContent->contentData->duration);
            $contentDetails["duration"] = $duration;
            if(empty($appContent->contentImage["h_image"])) {
                $h_image = "http://goldflixsouth.in/images/goldflix_south_1920_X_1080.png";
            }
            else {
                $h_image = config('global.CONTENT_IMAGE_PATH').$appContent->contentImage["h_image"];
            }
            $contentDetails["h_image"] = $h_image;
            if(empty($appContent->contentImage["v_image"])) {
                $v_image = "http://goldflixsouth.in/images/goldflix_south_444_X_666.png";
            }
            else {
                $v_image = config('global.CONTENT_IMAGE_PATH').$appContent->contentImage["v_image"];
            }
            $contentDetails["v_image"] = $v_image;
            $contentDetails["likeCount"] = $contentLikeCount;
            $contentDetails["likeStatus"] = $like_status;
            $contentDetails["watchTime"] = $continue_Watching;
            $contentDetails["watchLaterStatus"] = $watchlater_status;
            if(isset($watch_later_id)) {
                $contentDetails["watch_later_id"] = $watch_later_id;
            }
            else {
                $contentDetails["watch_later_id"] = "";
            }
            $contentDetails["likeOption"] = 1;
            $contentDetails["pg_rating"] = $appContent->contentData->pg_rating_id;
            if(empty($appContent->videoContentUrl->skip_intro)) {
                if($appContent->category_id == 1) {
                    $skip_intro = 30;
                }
                else if($appContent->category_id == 3) {
                    $skip_intro = 20;
                }
                else {
                    $skip_intro = 0;
                }
            }
            else {
                $skip_intro = $appContent->videoContentUrl->skip_intro;
            }
            $contentDetails["skip_intro"] = $skip_intro;
            $contentDetails["videoURL"] = config('global.CONTENT_VIDEO_PATH').$appContent->videoContentUrl->url;
            $contentDetails["payment_status"] = $sub_status;
            $contentDetails["paymentOption"] = 0;
            $relatedPlayList = AppContent::where("app_data_id", $request["appId"])
            ->where("status", "A")
            ->where("content_data_id","!=", $appContent->content_data_id)
            ->where("sub_category_id", $appContent->sub_category_id)
            // ->with("contentData", "subCategoryType", "contentImage", "videoContentUrl");

            // updated code
            ->with([
    "contentData" => fn($query) => $query->where("status", "A"),
    "subCategoryType" => fn($query) => $query->where("status", "A"),
    "contentImage" => fn($query) => $query->where("status", "A"),
    "videoContentUrl" => fn($query) => $query->where("status", "A"),
            ]);
            if(!is_null($appContent->content_title_id)) {
                $relatedPlayList = $relatedPlayList->where("content_title_id", $appContent->content_title_id)
                ->where("content_data_id", ">", $appContent->content_data_id);
            }
            if($relatedPlayList->count()==0) {
                $relatedPlayList = $relatedPlayList->where("content_data_id", "<", $appContent->content_data_id);
            }
            $relatedPlayList = $relatedPlayList->skip(0)->take(30)->get();
            if(!is_null($relatedPlayList)) {
                $playlistArray = array();
                foreach($relatedPlayList as $data) {
                    $details = array();
                    $details["id"] = $data->contentData->id;
                    $details["name"] = $data->contentData->name;
                    $details["duration"] = gmdate("H:i:s", $appContent->contentData->duration);
                    $details["genre"] = $data->contentData->genre;
                    if(empty($data->contentImage["h_image"])) {
                        $h_image = "http://goldflixsouth.in/images/goldflix_south_1920_X_1080.png";
                    }
                    else {
                        $h_image = config('global.CONTENT_IMAGE_PATH').$data->contentImage["h_image"];
                    }
                    $details["h_image"] = $h_image;
                    if(empty($data->contentImage["v_image"])) {
                        $v_image = "http://goldflixsouth.in/images/goldflix_south_444_X_666.png";
                    }
                    else {
                        $v_image = config('global.CONTENT_IMAGE_PATH').$data->contentImage["v_image"];
                    }
                    $details["v_image"] = $v_image;
                    // $details["videoURL"] =  config('global.CONTENT_VIDEO_PATH').$data->videoContentUrl["url"];

                    // updated code
                    $details["videoURL"] = config('global.CONTENT_VIDEO_PATH') . ($data->videoContentUrl->url ?? '');
                    array_push($playlistArray, $details);
                }
                $contentDetails["relatedPlaylist"] = $playlistArray;
            }
            else {
                return $this->sendError("Releted Video Not Found");
            }
            $success["userId"] = $user_id;
            $success["access"] = $access_code;
            $success["appId"] = $request["appId"];
            $success["is_login"] = $is_login;
            $success["contentList"] = $contentDetails;
            return $this->sendResponse($success, "Content Lists");
        }
        else {
            return $this->sendError("No Content Found");
        }
    }

     public function searchContent(Request $request) {
        // Check if User is Logged In
        if(LoginController::accessCheck($request)) {
            $user_id = $request["userId"];
            $access_code = $request["access"];
        }
        else {
            $user_id = "0";
            $access_code = "guest";
        }
        // Get Keyword
        $likeValue = $request["LIKE"];
        // If Keyword is Not Null/Empty
        if(!is_null($likeValue)) {
            $searchArr = array();
            $categoryID = $this->getCategoryID($likeValue);
            if(!is_null($categoryID)) {
                $contentData = $this->getSearchedVideos($categoryID);
            }
            else {
                $contentData = $this->getSeachedContent($likeValue);
            }
            if(count($contentData) > 0) {
                foreach($contentData as $data) {
                    $contentData = array();
                    $contentData["content_id"] = $data->id;
                    $contentData["name"] = $data->name;
                    $contentData["description"] = $data->description;
                    $contentData["duration"] = gmdate("H:i:s", $data->duration);
                    $contentData["genre"] = $data->genre;
                    $contentData["h_image"] = empty($data->h_image) ? "" : config("global.CONTENT_IMAGE_PATH").$data->h_image;
                    $contentData["v_image"] = empty($data->v_image) ? "" : config("global.CONTENT_IMAGE_PATH").$data->v_image;
                    array_push($searchArr, $contentData);
                }
            }
            $success["userId"] = $user_id;
            $success["access"] = $access_code;
            $success["appId"] = $request["appId"];
            $success["searchPlaylist"] = $searchArr;
            return $this->sendResponse($success, "Search Content");
        }
        else {
            $searchArr = array();
            $contentData = DB::table("app_contents AS ac")
            ->distinct("cd.id")
            ->select("cd.id", "cd.title AS name", "cd.description", "cd.duration", "cd.genre", DB::raw('IF(ct.h_image IS NULL, ci.h_image, ct.h_image) AS h_image'), DB::raw('IF(ct.v_image IS NULL, ci.v_image, ct.v_image) AS v_image'))
            ->leftjoin('content_data AS cd', 'ac.content_data_id', 'cd.id')
            ->leftjoin('content_images AS ci', 'ci.content_data_id', 'cd.id')
            ->leftjoin('content_titles AS ct', 'ct.id', 'ac.content_title_id')
            // ->whereIn('cd.id', array(1,2,4))
            // updated code
            // ->whereIn('cd.id', fn($query)=>$query->where())
            ->orderBy('cd.id', 'DESC')
            ->limit(10)
            ->get();
            if(count($contentData) > 0) {
                foreach($contentData as $data) {
                    $contentData = array();
                    $contentData['content_id'] = $data->id;
                    $contentData['name'] = $data->name;
                    $contentData['description'] = $data->description;
                    $contentData['duration'] = gmdate("H:i:s", $data->duration);
                    $contentData['genre'] = $data->genre;
                    $contentData['h_image'] = config('global.CONTENT_IMAGE_PATH').$data->h_image;
                    $contentData['v_image'] = config('global.CONTENT_IMAGE_PATH').$data->v_image;
                    array_push($searchArr, $contentData);
                }
            }
            $success['userId'] = $user_id;
            $success['appId'] = $request['appId'];
            $success['access'] = $access_code;
            $success['searchPlaylist'] = $searchArr;
            return $this->sendResponse($success, 'Search Page');
        }
    }

    private function getCategoryID(String $keyword) {
        $category = CategoryType::where('name', 'like', $keyword)
        ->where('status', 'A')
        ->first();
        if(!is_null($category)) {
            return $category['id'];
        }
        else {
            return null;
        }
    }
    private function getSeachedContent(String $keyword) {

        $query = DB::table('content_data AS cd')
        ->select(
            'cd.id',
            'cd.title AS name',
            'cd.description',
            'cd.duration',
            'cd.genre',
            DB::raw('IF(ct.h_image IS NULL, ci.h_image, ct.h_image) AS h_image'),
            DB::raw('IF(ct.v_image IS NULL, ci.v_image, ct.v_image) AS v_image')
        )
        ->leftjoin('content_images AS ci', 'cd.id', 'ci.content_data_id')
        ->leftjoin('app_contents AS ac', 'ac.content_data_id', 'cd.id')
        ->leftjoin('content_titles AS ct', 'ac.content_title_id', 'ct.id')
        ->where('cd.status', 'A')
        ->where('cd.title', 'LIKE', '%'.$keyword.'%')
        ->where(DB::raw('IF(ac.content_title_id IS NOT NULL, ac.priority, 1)'), 1)
        ->distinct('cd.id')
        ->limit(50)
        ->orderBy('cd.title')
        ->get();

        return $query;
    }
}

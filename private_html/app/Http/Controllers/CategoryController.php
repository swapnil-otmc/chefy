<?php

namespace App\Http\Controllers;
use DB;
use DateTime;
use Carbon\Carbon;
use App\Models\SubCategoryType;
use App\Models\AppContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\LoginController;
class CategoryController extends Controller
{
    //get home page content 
     /**
     * Return Category-wise Content for Home 
     */
    public function getHomePageContentList(Request $request){
        
        if(LoginController::accessCheck($request)) {
            $user_id = $request['userId'];
            $access_code = $request['access'];
            // UserController::validateSubscription($user_id);
        }
        else {
            $user_id = '0';
            $access_code = 'guest';
        }
        
        // UserController::validateSubscriptions();
        $carbon = Carbon::now()->format('Y-m-d h:i:s');

        log::debug("Start Time of getCategoryListById " .$carbon);

        $appContent = SubCategoryType::where('status', SubCategoryType::ACTIVE_STATUS)
        ->where('home_priority', '!=', '')
        ->orderBy('home_priority', 'ASC')
        ->get();

        if(!is_null($appContent)) {
            $contentLists = array();

            foreach($appContent as $details) {
                $contentData = array();

                $category_name = $details->name;
                $redirection = "home";
                $categoryTypeArray = $this->getCategoryListById($request, $details->id, $details->category_id = 0, $redirection);
                $contentData['category_id'] = (int)$details->id;
                $contentData['content_data_id'] = (int)$details->id;
                $contentData['category_name'] = $category_name;
                $contentData['thumbSize'] = $details->thumbSize;
                $contentData['styleType'] = $details->styleType;
                $contentData['homePriority'] = $details['home_priority'];

                if($details->category_id == 4) {
                    $contentData['titleOption'] = 0;
                }
                else {
                    $contentData['titleOption'] = 1;
                }
                $contentData['dataList'] = $categoryTypeArray;

                if(!empty($categoryTypeArray)) {
                    array_push($contentLists, $contentData);
                }
            }
            log::debug("End Time of getCategoryListById" .$carbon);

            $success['userId'] = $user_id;
            $success['access'] = $access_code;
            $success['appId'] = $request['appId'];
            $success['contentList'] = $contentLists;

            return $this->sendResponse($success, 'Content Lists');
        } 
        else {
            return $this->sendError('No Content Found');
        }
        
    }

    private function getCategoryListById(Request $request, $id, $menuId, $redirection) {
        // $appContent = AppContent::appContent($id);

        // return $this->sendError($appContent);
        
        $appContent = DB::table('app_contents')
        ->leftJoin('category_types', 'app_contents.category_id', '=', 'category_types.id')
        ->leftJoin('sub_category_types', 'app_contents.sub_category_id', '=', 'sub_category_types.id')
        ->leftJoin('content_data', 'app_contents.content_data_id', '=', 'content_data.id')
        ->leftJoin('content_images', 'content_data.id', '=', 'content_images.content_data_id')
        ->leftJoin('content_titles', 'app_contents.content_title_id', '=', 'content_titles.id')
        ->select(
            'content_data.id',
            'content_data.name',
            'content_titles.id AS content_title_id',
            'sub_category_types.styleType',
            'sub_category_types.thumbSize',
            'content_data.release_date',
            'content_data.release_date',
            'content_data.genre',
            'content_data.duration',
            'content_images.h_image',
            'content_images.v_image',
            'sub_category_types.id AS sub_category_id',
            'content_titles.name AS content_title_name',
            'content_titles.h_image AS content_title_h_image',
            'content_titles.v_image AS content_title_v_image'
        )
        ->where('app_contents.app_data_id', $request['appId'])
            ->where('app_contents.sub_category_id', $id)
            ->where('app_contents.status', 'A')
            ->where('category_types.status', 'A')
            ->where('sub_category_types.status', 'A')
        ->orderBy('app_contents.priority', 'DESC');

        if(in_array($id, array(44, 45, 46))) {
            $appContent = $appContent->orderBy('app_contents.priority', 'ASC');
        }
        else {
            $appContent = $appContent->orderBy('app_contents.priority', 'DESC');
        }

        // If on Homepage, reduce results
        if($redirection == "home") {
            $appContent = $appContent->skip(0)->take(30);
        }

        if($menuId !== 0) {
            $appContent = $appContent->where('app_contents.category_id', $menuId);
        }
        $appContent =  $appContent->get();

        if(!is_null($appContent)) {

            $contentLists = array();
            $title_ids = array();
            array_push($title_ids,0);
            $category_id = 0;

            foreach($appContent as $details) {

                $contentData = array();
                $contentData['id'] = (int)$details->id;

                if(is_null($details->content_title_id)) {
                    $contentData['name'] = $details->name;
                    $contentData['styleType'] = $details->styleType;
                    $contentData['thumbSize'] = $details->thumbSize;
                    $contentData['year'] = $details->release_date;
                    $contentData['genre'] = $details->genre;
                    $contentData['duration'] = $this->convert_seconds($details->duration);
                    $contentData['shows'] = " ";

                    if(empty($details->h_image)) {
                        $contentData['h_image'] = "http://goldflixsouth.in/images/goldflix_south_1920_X_1080.png";
                    }
                    else {
                        $contentData['h_image'] = config('global.CONTENT_IMAGE_PATH').$details->h_image;
                    }

                    if(empty($details->v_image)) {
                        $contentData['v_image'] = "http://goldflixsouth.in/images/goldflix_south_444_X_666.png";
                    }
                    else {
                        $contentData['v_image'] = config('global.CONTENT_IMAGE_PATH').$details->v_image;
                    }
                }
                else {
                    if(array_search($details->content_title_id, $title_ids)) {
                        array_push($title_ids, $details->content_title_id);
                        $category_id = $details->sub_category_id;
                    }
                    else {
                        $contentData['name'] = $details->content_title_name;
                        $contentData['styleType'] = $details->styleType;
                        $contentData['thumbSize'] = $details->thumbSize;
                        $contentData['year'] = $details->release_date;
                        $contentData['genre'] = $details->genre;
                        $contentData['duration'] = $this->convert_seconds($details->duration);
                        $contentData['shows'] = " ";

                        if(empty($details->content_title_h_image)) {
                            $contentData['h_image'] = "http://goldflixsouth.in/images/goldflix_south_1920_X_1080.png";
                        }
                        else {
                            $contentData['h_image'] = config('global.CONTENT_IMAGE_PATH').$details->content_title_h_image;
                        }

                        if(empty($details->content_title_v_image)) {
                            $contentData['v_image'] = "http://goldflixsouth.in/images/goldflix_south_444_X_666.png";
                        }
                        else {
                            $contentData['v_image'] = config('global.CONTENT_IMAGE_PATH').$details->content_title_v_image;
                        }
                        array_push($title_ids, $details->content_title_id);
                    }
                }

                $contentData['titleOption'] = 1;

                if(count($contentData) > 0) {
                    array_push($contentLists, $contentData);
                }
            }
            return $contentLists;
        }
        else {
            return $this->sendError('No Content Found');
        }
    }

     private function convert_seconds($seconds) {

        $dt1 = new DateTime("@0");
        $dt2 = new DateTime("@$seconds");

        if($seconds >= 3600) {

            return $dt1->diff($dt2)->format('%hh %im');
        }
        else if($seconds >= 60) {

            return $dt1->diff($dt2)->format('%im %ss');
        }
        else {

            return $dt1->diff($dt2)->format('%ss');
        }
    }



 




     /**
     * Return Content by Sub-Category ID for 'More' 
     */
    public function getMoreContentList(Request $request) {

        if(LoginController::accessCheck($request)) {
            $user_id = $request['userId'];
            $access_code = $request['access'];
        }
        else {
            $user_id = '0';
            $access_code = 'guest';
        }

        $appContent = AppContent::where('app_data_id', $request['appId'])
        ->where('status', AppContent::ACTIVE_STATUS)
        ->where('sub_category_id', $request['subCategoryId'])
        ->first();

        if(!is_null($appContent)) {

            $redirection = "";
            $categoryTypeArray = $this->getCategoryListById($request, $appContent->sub_category_id, $appContent->category_id, $redirection);

            $success['userId'] = $user_id;
            $success['access'] = $access_code;
            $success['appId'] = $request['appId'];
            $success['contentList'] = $categoryTypeArray;

            return $this->sendResponse($success, 'Content Lists');
        } 
        else {
            return $this->sendError('No Content Found');
        }
    }


        public function getMenuContentList(Request $request) {

        if(LoginController::accessCheck($request)) {
            $user_id = $request['userId'];
            $access_code = $request['access'];
        }
        else {
            $user_id = '0';
            $access_code = 'guest';
        }

        $appContent = AppContent::where('app_data_id', $request['appId'])
        ->where('app_contents.status', AppContent::ACTIVE_STATUS)
        ->where('app_contents.category_id', $request['menuId'])
        ->with([ 'subCategoryType' => function($query) {
            $query->where('status', AppContent::ACTIVE_STATUS);
        }]);

        $appContent = $appContent->join('sub_category_types', 'app_contents.sub_category_id', '=', 'sub_category_types.id')
        ->orderBy('sub_category_types.priority', 'ASC');

        $appContent = $appContent->with('contentData','subCategoryType','contentType','appData','contentImage')
        ->where('sub_category_types.status', 'A')
        ->get();

        $collection = collect($appContent);
        $appContent = $collection->unique('sub_category_id');
        $appContent->values()->all();

        if(!is_null($appContent)) {

            $contentLists = array();
            $detail = array();

            foreach($appContent as $details) {

                $contentData = array();

                $category_name = $details->subCategoryType->name;
                $redirection = "home";
                $categoryTypeArray = $this->getCategoryListById($request, $details->sub_category_id, $details->category_id, $redirection);
                $contentData['category_id'] = (int)$details->sub_category_id;
                $contentData['content_data_id'] = (int)$details->content_data_id;
                $contentData['category_name'] = $category_name;

                if($details->category_id == 4) {
                    $contentData['titleOption'] = 0;
                }
                else {
                    $contentData['titleOption'] = 1;
                }

                $contentData['thumbSize'] = $details->subCategoryType->thumbSize;
                $contentData['styleType'] = $details->subCategoryType->styleType;
                $contentData['dataList'] = $categoryTypeArray;

                array_push($contentLists, $contentData);
            }

            $success['userId'] = $user_id;
            $success['access'] = $access_code;
            $success['appId'] = $request['appId'];
            $success['contentList'] = $contentLists;

            return $this->sendResponse($success, 'Content Lists');
        }
        else {
            return $this->sendError('No Content Found');
        }
    }

}

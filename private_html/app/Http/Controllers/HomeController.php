<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\AppMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;

class HomeController extends Controller
{
    //
       public function getSlider(Request $request) {

        if(LoginController::accessCheck($request)) {
            $user_id = $request['userId'];
            $access_code = $request['access'];
        }
        else {
            $user_id = '0';
            $access_code = 'guest';
        }

        $app_id = $request['appId'];
        // $appSlider = AppSlider::where('status', AppSlider::ACTIVE_STATUS)
        // ->where('app_data_id', $app_id)
        // ->get(['slider_id'])
        // ->pluck('slider_id');

        // updated code
         $slider = Slider::where('status', Slider::ACTIVE_STATUS)
            ->orderBy('priority','desc')
            ->get();
            // return $this->sendError($slider);

        // if(!is_null($slider)) {
        if($slider->count() > 0) {

            // $slider = Slider::whereIn('id', $appSlider)
            // ->where('status', Slider::ACTIVE_STATUS)
            // ->orderBy('priority','asc')
            // ->get();

            $slider_data = array();

            foreach ($slider as $data) {

                $arr = array();
                $arr['id'] = $data['id'];
                $arr['name'] = $data['name'];
                $arr['image'] = config('global.SLIDER_IMAGE_PATH').$data['image'];
                $arr['content_id'] = $data['content_id'];
                $arr['category_id'] = $data['category_id'];

                array_push($slider_data, $arr);
            }

            $success['userId'] = $user_id;
            $success['access'] = $access_code;
            $success['appId'] = $app_id;
            $success['sliderList'] = $slider_data;

            return $this->sendResponse($success, 'Slider Lists');
        }
        else {
            return $this->sendError('No Slider Found');
        }
    }

     public function getMenu(Request $request) {

        if(LoginController::accessCheck($request)) {
            $user_id = $request['userId'];
            $access_code = $request['access'];
        }
        else {
            $user_id = '0';
            $access_code = 'guest';
        }

        $app_id = $request['appId'];

        // $appMenu = AppMenu::where('status', AppMenu::ACTIVE_STATUS)
        // ->where('app_data_id', $app_id)
        // ->get(['menu_id'])
        // ->pluck('menu_id');

        // updated code====
        
        
      $appMenu= AppMenu::where('status', AppMenu::ACTIVE_STATUS)
            ->orderBy('priority','desc')
            ->get();

        // return $this->sendError( $appMenu);
        if(!is_null($appMenu)) {
        // if($appMenu->count()>0) {

            // $menu= Menu::whereIn('id', $appMenu)
            // ->where('status', Menu::ACTIVE_STATUS)
            // ->orderBy('priority','asc')
            // ->get();

            $menu_data = array();

            foreach ($appMenu as $data) {

                $arr = array();
                $arr['id'] = $data['id'];
                $arr['name'] = $data['name'];
                $arr['priority'] = $data['priority'];

                array_push($menu_data, $arr);
            }

            $success['userId'] = $user_id;
            $success['access'] = $access_code;
            $success['app_id'] = $app_id;
            $success['menuList'] = $menu_data;

            return $this->sendResponse($success, 'Menu Lists');
        }
        else {
            return $this->sendError('No Menu Found');
        }
    }
}

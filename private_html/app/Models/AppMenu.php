<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMenu extends Model
{
    use HasFactory;
    
    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

    Protected $table = "menus";

     protected $fillable = [
        'sub_menu_id', 'name','app_data_id','priority','status',
    ];

      public function menu(){
        return $this->belongsTo(Menu::class,'sub_menu_id');
    } 

    public function appData(){
        return $this->belongsTo(AppData::class,'app_data_id');
    } 


}

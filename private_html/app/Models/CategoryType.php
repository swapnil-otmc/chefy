<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    use HasFactory;
    Protected $table = 'category_types';

    Protected $fillable = ([
         "name",
         "poster",
         "home_priority",
         "status"	
    ]);
}

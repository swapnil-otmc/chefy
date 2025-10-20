<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    use HasFactory;
    Protected $table = "content_types";

         protected $fillable = [
        'name','status','timestamp',
    ];

}

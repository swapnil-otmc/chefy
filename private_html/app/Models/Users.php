<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';


    protected $table = 'users';

     protected $fillable = [
        'name', 'email', 'password','mobile','userrole','birthdate','status','timestamp',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    protected $primaryKey = 'id';

    public function userRole() {
        return $this->belongsTo(UserRole::class,'userrole_id');
    }

    public static function getIDFromMobileNumber(String $mobile_number) {
        return User::where('mobile', $mobile_number)
        ->where('status', 'A')
        ->value('id');
    }

    public static function findOrCreate(string $mobile, string $name = null): User {
        return self::firstOrCreate(
            ['mobile' => $mobile, 'status' => 'A'], // Conditions to find
            ['name' => $name, 'userrole' => 1] // Attributes to use if creating
        );
    }
}

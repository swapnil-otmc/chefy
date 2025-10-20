<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

   const ACTIVE_STATUS = 'A';
    const INACTIVE_STATUS = 'I';

    // protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'password','mobile','userrole','birthdate','status','timestamp',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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

    /// new code 
    public function numberCheck(String $mobile){
        return User::where('mobile',$mobile)
            ->where('status', User::ACTIVE_STATUS)
            ->first();

    
    }
}
 
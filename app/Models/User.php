<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use UUIDModel;
    use TimestampedModel;

    protected $table = 'users';

    protected  $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'role'
    ];

    protected $hidden = [ 
        'pivot',
        'password'
    ];

    protected static $searchField = 'email';
    
    public static $filters = [
        'role',
        'created_at',
        'updated_at'
    ];

    public static function searchField(){
        return self::$searchField;
    }

    public static function findByUsername($username)
    {
        return User::where('username', '=', $username)->first();
    }

}

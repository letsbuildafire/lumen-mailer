<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpArticle extends Model
{
    use UUIDModel;
    use TimestampedModel;
    
    protected $table = 'help';

    protected  $fillable = [
        'title',
        'content',
        'section',
    ];

    protected $hidden = [ 
        'pivot'
    ];
    
    protected static $searchField = 'title';

    public static $filters = [
        'section',
        'created_at',
        'updated_at'
    ];

    public static function searchField(){
        return self::$searchField;
    }

}

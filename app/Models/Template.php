<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use UUIDModel;
    use TimestampedModel;

    protected $table = 'templates';

    protected $hidden = [
        'pivot'
    ];

    protected $fillable = [
        'name',
        'source',
        'default_content',
    ];

    protected static $searchField = 'name';

    public static $filters = [
        'created_at',
        'updated_at',
    ];

    public static function findByName($name)
    {
        return Template::where('name', '=', $name)->first();
    }

    public static function findBySource($source)
    {
        return Template::where('source', '=', $source)->first();
    }
    
    public static function searchField()
    {
        return self::$searchField;
    }

    public function emailers()
    {
        return $this->hasMany('App\Models\Emailer');
    }
    
}

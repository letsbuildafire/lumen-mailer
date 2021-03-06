<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailerClick extends Model
{
    protected $table = 'emailer_clicks';

    protected  $fillable = [
        'emailer_id',
        'address_id',
        'url',
        'opened_at',
        'useragent',
        'ip_address'
    ];

    protected $hidden = [ 
    ];

    protected $casts = [
    ];

    protected static $searchField = 'emailer_id';

    public $timestamps = false;
    
    public static $filters = [
        'emailer_id',
        'address_id',
        'useragent'
    ];

    public static function searchField()
    {
        return self::$searchField;
    }

    public function findByEmailerUUID($emailer_uuid)
    {
        return EmailerOpen::where('emailer_id', '=', $emailer_uuid);
    }

    public function findByAddressUUID($address_uuid)
    {
        return EmailerOpen::where('address_id', '=', $address_uuid);
    }

    public function findByURL($url)
    {
        return EmailerOpen::where('url', '=', $url);
    }

    public function emailer()
    {
        return $this->belongsTo('\App\Models\Emailer', 'emailer_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo('\App\Models\Address', 'address_id', 'id');
    }

    public function getOpenedAtAttribute($value)
    {
        return Carbon::parse($value, env('DB_TIMEZONE'))->format('c');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Emailer extends Model
{
    use UUIDModel;
    use TimestampedModel;
    
    protected $table = 'emailers';

    protected  $fillable = [
        'subject',
        'content',
        'signature',
        'return_name',
        'return_address',
        'distribute_at',
        'template_id'
    ];

    protected $hidden = [ 
        'pivot',
        'quadrant_uid',
        'quadrant_list_uid'
    ];

    protected $casts = [
        'approved' => 'boolean',
        'api_extended_status_received' => 'boolean',
    ];

    protected $appends = [
        'opens',
        'unique_opens',
        'clicks',
        'unique_clicks'
    ];

    protected static $searchField = 'subject';
    
    public static $filters = [
        'created_at',
        'updated_at',
        'return_address',
        'approved',
        'status'
    ];

    public static function searchField()
    {
        return self::$searchField;
    }

    public static function findBySubject($subject)
    {
        return Emailer::where('subject', '=', $subject)->first();
    }

    public static function findByReturnAddress($return_address)
    {
        return Emailer::where('return_address', '=', $return_address)->first();
    }

    public function api_sending_status_numbers()
    {
        return json_decode($this->api_sending_status_numbers);
    }

    public function template()
    {
        return $this->belongsTo('\App\Models\Template');
    }

    public function lists()
    {
        return $this->belongsToMany('\App\Models\AddressList', 'emailer_lists', 'emailer_id', 'list_id');
    }

    public function stats()
    {
        return $this->hasMany('\App\Models\EmailerStat', 'emailer_id');
    }

    public function getDistributeAtAttribute($value)
    {
        return Carbon::parse($value, env('DB_TIMEZONE'))->format('c');
    }

    public function getOpensAttribute()
    {
        return $this->stats()->sum('opens');
    }

    public function getUniqueOpensAttribute()
    {
        return $this->stats()->where('opens', '>', 0)->count();
    }

    public function getClicksAttribute()
    {
        return $this->stats()->sum('clicks');
    }

    public function getUniqueClicksAttribute()
    {
        return $this->stats()->where('clicks', '>', 0)->count();
    }

}

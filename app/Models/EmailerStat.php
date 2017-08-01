<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailerStat extends Model
{
    protected $table = 'emailer_stats';

    protected  $fillable = [
        'emailer_id',
        'address_id',
        'extended_status',
        'bounced',
        'opens',
        'clicks',
        'status',
        'unsubscribed'
    ];

    protected $hidden = [ 
    ];

    protected $casts = [
        'unsubscribed' => 'boolean',
    ];

    protected static $searchField = 'id';

    public $timestamps = false;
    
    public static $filters = [
        'emailer_id',
        'address_id',
        'bounced',
        'unsubscribed'
    ];

    public static function searchField()
    {
        return self::$searchField;
    }

    public function findByEmailerUUID($emailer_uuid)
    {
        return EmailerStat::where('emailer_id', '=', $emailer_uuid);
    }

    public function findByAddressUUID($address_uuid)
    {
        return EmailerStat::where('address_id', '=', $address_uuid);
    }

    public function emailer()
    {
        return $this->belongsTo('\App\Models\Emailer', 'emailer_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo('\App\Models\Address', 'address_id', 'id');
    }

}

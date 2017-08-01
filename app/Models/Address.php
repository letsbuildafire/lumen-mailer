<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use UUIDModel;
    use TimestampedModel;

    protected $table = 'addresses';

    protected $hidden = [
        'pivot',
        'lists'
    ];

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'spam',
        'active'
    ];

    protected $casts = [
        'spam' => 'boolean',
        'active' => 'boolean',
    ];

    protected static $searchField = 'email';

    public static $filters = [
        'spam',
        'active',
        'created_at',
        'updated_at',
    ];

    public static function findByEmail($email)
    {
        return Address::where('email', '=', $email)->first();
    }
    
    public static function searchField()
    {
        return self::$searchField;
    }
    
    public function address_lists()
    {
        return $this->belongsToMany('\App\Models\AddressList', 'list_addresses', 'address_id', 'list_id')
            ->withPivot('custom_data');
    }

    public function listsCount()
    {
        return $this->belongsToMany('\App\Models\AddressList', 'list_addresses', 'address_id', 'list_id')
            ->selectRaw('count(list_id) as aggregate')
            ->groupBy('address_id');
    }

    public function getListsCountAttribute()
    {
        if(!array_key_exists('listsCount', $this->relations)) $this->load('listsCount');

        $related = $this->getRelation('listsCount')->first();
        return ($related) ? $related->aggregate : 0;
    }

    public function emailers()
    {
        return $this->belongsToMany('\App\Models\Emailer', 'emailer_stats', 'address_id', 'emailer_id')
            ->withPivot('extended_status', 'bounced', 'opens', 'unsubscribed');
    }

    public function emailersCount()
    {
        return $this->belongsToMany('\App\Models\Emailer', 'emailer_stats', 'address_id', 'emailer_id')
            ->selectRaw('count(emailer_id) as aggregate')
            ->groupBy('address_id');
    }

    public function getEmailersCountAttribute()
    {
        if(!array_key_exists('emailersCount', $this->relations)) $this->load('emailersCount');

        $related = $this->getRelation('emailersCount')->first();
        return ($related) ? $related->aggregate : 0;
    }
    
}

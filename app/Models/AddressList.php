<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressList extends Model
{
    use UUIDModel;
    use TimestampedModel;
    
    protected $table = 'lists';

    protected $hidden = [
        'pivot',
        'addresses',
        'quadrant_uid'
    ];

    protected $fillable = [
        'name',
        'custom_fields'
    ];

    protected static $searchField = 'name';

    public static $filters = [
        'created_at',
        'updated_at',
    ];

    public static function findByName($name)
    {
        return AddressList::where('name', '=', $name)->first();
    }
    
    public static function searchField()
    {
        return self::$searchField;
    }
    
    public function addresses()
    {
        return $this->belongsToMany('\App\Models\Address', 'list_addresses', 'list_id', 'address_id')
            ->withPivot('custom_data');
    }

    public function addressesCount()
    {
        return $this->belongsToMany('\App\Models\Address', 'list_addresses', 'list_id', 'address_id')
            ->selectRaw('count(address_id) as aggregate')
            ->groupBy('list_id');
    }

    public function getAddressesCountAttribute()
    {
        if(!array_key_exists('addressesCount', $this->relations)) $this->load('addressesCount');

        $related = $this->getRelation('addressesCount')->first();
        return ($related) ? $related->aggregate : 0;
    }

    public function emailers()
    {
        return $this->belongsToMany('\App\Models\Emailer', 'emailer_lists', 'list_id', 'emailer_id');
    }
    
}

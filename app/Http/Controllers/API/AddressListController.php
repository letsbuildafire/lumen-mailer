<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use DB;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Helpers\MySQLBuilder;
use App\Models\Address;
use App\Models\AddressList;
use App\Models\EmailerStat;

use App\Http\Controllers\QuadrantController as Quadrant;
use Laravel\Lumen\Routing\Controller as BaseController;

class AddressListController extends BaseController
{
    use APIController;

    const MODEL = 'App\Models\AddressList';
    
    /**
     * POST a new address list
     * @param  Request $request 
     */
    public function post(Request $request)
    {
        try
        {
            $list = new AddressList;
            $list->name = $request->input('name');

            $custom_fields = $request->input('custom_fields');
            if(is_array($custom_fields)){
                $custom_fields = json_encode($custom_fields);
            }

            $list->custom_fields = !empty($custom_fields) ?
                $custom_fields : null;
            
            // *** disabled due to the way Quadrant's API handles lists ***
            // TODO: validate response
            // $quadrant = new Quadrant();
            // $res = $quadrant->postList([]);
            // $list->quadrant_uid = $res->uid;

            $list->save();

            return response()->json(['data' => $list], 201);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
    
    /**
     * PUT an address list
     * @param  Request $request 
     * @param  string  $id      Address list uuid
     */
    public function put(Request $request, $id)
    {
        try
        {
            if($list = AddressList::find($id))
            {
                $list->name = $request->input('name', $list->name);
                
                $custom_fields = $request->input('custom_fields');
                if(is_array($custom_fields))
                {
                    $custom_fields = json_encode($custom_fields);
                }

                $list->custom_fields = !empty($custom_fields) ?
                    $custom_fields : $list->custom_fields;

                $list->save();
                return response()->json(['data' => $list], 202);
            }
            else
            {
                throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Handle removal of an address list
     * @param  Request $request
     * @param  string  $list      Address list to remove
     */
    private function remove(Request $request, $list)
    {
        // check to see if this list has been dispatched to
        $used = $list->emailers()
            ->whereIn('status', ['PENDING', 'PAUSED', 'RUNNING', 'COMPLETED'])
            ->count();

        if($used)
        {
            throw new APIBadRequestException(sprintf('Cannot remove %s with active or complete %s',
                strtolower(env('TITLES_LISTS')),
                strtolower(env('TITLES_EMAILERS'))));
        }

        // keep track of the address ids to remove.
        $addresses = DB::table('list_addresses')
            ->where('list_id', '=', $list->id)
            ->lists('address_id');

        // detach any addresses from this list
        DB::table('list_addresses')
            ->where('list_id', '=', $list->id)
            ->delete();

        // detach any scheduled emailers from this list
        DB::table('emailer_lists')
            ->where('list_id', '=', $list->id)
            ->delete();
        
        // find any addresses that are still linked to another list
        $linked_to = DB::table('list_addresses')
            ->whereIn('address_id', $addresses)
            ->lists('address_id');

        // find any addresses that have been sent an email
        $sent_to = DB::table('emailer_stats')
            ->whereIn('address_id', $addresses)
            ->lists('address_id');

        // then we can remove the orphaned addresses
        $result = Address::whereIn('id', $addresses)
            ->whereNotIn('id', $sent_to)
            ->whereNotIn('id', $linked_to)
            ->delete();

        // and delete the list
        return $list->delete();
    }


    /**
     * DELETE address lists from an array of uuids
     * @param  Request $request 
     */
    public function deleteMany(Request $request)
    {
        try
        {
            // set the execution limit a bit higher, deleting might take a while.
            set_time_limit(300);

            DB::beginTransaction();

            if(!$request->has('lists'))
            {
                // we probably don't want to truncate the lists table!
                // $this->truncate($request);
                throw new APIBadRequestException('Request invalid or missing customer list array', 422);
            }

            if(!is_array($lists = $request->input('lists')))
            {
                throw new APIBadRequestException('Request invalid or missing customer list array', 422);
            }

            $removed = 0;
            foreach(AddressList::whereIn('id', $lists)->get() as $list)
            {
                $removed += $this->remove($request, $list);

                // *** disabled due to the way Quadrant's API handles lists ***
                // if(!empty($list->quadrant_uid))
                // {
                    // try to delete the list from Quadrant's system.
                    // if this fails, it's probably because we shouldn't
                    // be removing it. (active emailers)
                    // $quadrant = new Quadrant();
                    // $res = $quadrant->deleteList($list->quadrant_uid);
                // }
            }

            DB::commit();

            return response()->json(['count' => $removed], 200);
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * DELETE an address list
     * @param  Request $request  
     * @param  string  $id       Address list uuid
     * @param  function  $callback
     */
    public function delete(Request $request, $id, $callback = null)
    {
        try
        {
            if($list = AddressList::find($id))
            {
                // set the execution limit a bit higher, deleting might take a while.
                set_time_limit(300);

                DB::beginTransaction();

                $this->remove($request, $list);

                // *** disabled due to the way Quadrant's API handles lists ***
                // if(!empty($list->quadrant_uid))
                // {
                    // try to delete the list from Quadrant's system.
                    // if this fails, it's probably because we shouldn't
                    // be removing it. (active emailers)
                    // TODO: validate response
                    // $quadrant = new Quadrant();
                    // $res = $quadrant->deleteList($list->quadrant_uid);
                // }

                DB::commit();

                return response()->json([], 204);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    }

    /**
     * GET all addresses belonging to a specific address list
     * @param  Request $request 
     * @param  string  $id      Address list uuid
     */
    public function getAddresses(Request $request, $id)
    {
        try
        {   
            if($list = AddressList::find($id))
            {
                return $this->all($request, $list->addresses());
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }   
    }

    /**
     * POST a new address or addresses to an address list
     * @param  Request $request 
     * @param  string  $id      Address list UUID
     */
    public function postAddress(Request $request, $id)
    {
        try
        {   
            DB::beginTransaction();

            if($list = AddressList::find($id))
            {
                if(!$request->has('email'))
                {
                    // set the execution limit a bit higher, importing might take a while.
                    set_time_limit(300);

                    $emails = [];
                    $addresses = [];
                    $ext_data = [];
                    foreach($request->except('user') as $data)
                    {
                        if(!isset($data['email']))
                        {
                            throw new APIBadRequestException('Email missing from address entity', 422);
                        }

                        // create a new address from the data array
                        $emails[] = $data['email'];
                        $addresses[] = [
                            'id' => Uuid::generate(4)->string,
                            'email' => $data['email'],
                            'first_name' => $data['first_name'],
                            'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
                        ];

                        // parse custom data if it exists
                        $custom_data = isset($data['custom_data']) ?
                            $data['custom_data'] : null;

                        if(!empty($custom_data) && is_array($custom_data))
                        {
                            $ext_data[$data['email']] = json_encode($custom_data);
                        }
                        else
                        {
                            $ext_data[$data['email']] = null;
                        }
                    }
                    
                    // insert or update the addresses in the addresses table.
                    $query_builder = new MySQLBuilder('addresses');
                    $query_builder->setColumns(['id', 'email', 'first_name', 'last_name']);
                    $query_builder->setUpdateColumns(['first_name', 'last_name']);
                    $query_builder->insertOrUpdate($addresses);

                    // query for the uuids of the addresses we added.
                    $address_ids = DB::table('addresses')
                        ->whereIn('email', $emails)
                        ->lists('email', 'id');

                    $list_addresses = [];
                    foreach($address_ids as $addr_id => $email){
                        $list_addresses[] = [
                            'list_id' => $id,
                            'address_id' => $addr_id,
                            'custom_data' => $ext_data[$email]
                        ];
                    }

                    // sync the addresses with the list and data that is list-specific.
                    $query_builder = new MySQLBuilder('list_addresses');
                    $query_builder->setColumns(['list_id', 'address_id', 'custom_data']);
                    $query_builder->setUpdateColumns(['custom_data']);
                    $query_builder->insertOrUpdate($list_addresses);

                    DB::commit();

                    // *** disabled due to the way Quadrant's API handles lists ***
                    // if(!empty($list->quadrant_uid))
                    // {
                        // TODO: validate response
                        // $quadrant = new Quadrant();
                        // $res = $quadrant->putList($list->quadrant_uid,$addresses,[]);

                        // if($res->add_subscriber_count != count($addresses))
                        // {
                            // throw new APIException('Failed to add all addreses using Quadrant API');
                        // }
                    // }

                    // return the number of updated / new entries
                    return response()->json(['count' => count($addresses)], 201);
                }
                else
                {

                    $address = Address::firstOrNew(['email' => $request->input('email')]);
                    $address->first_name = $request->input('first_name');
                    $address->last_name = $request->input('last_name');
                    $address->code = $request->input('code');
                    $address->save();

                    // parse custom data if it exists
                    $custom_data = $request->has('custom_data') ?
                        $request->input('custom_data') : null;

                    if(!empty($custom_data) && is_array($custom_data))
                    {
                        $list->addresses()->sync([
                            $address->id => [
                                'custom_data' => json_encode($custom_data)
                            ]
                        ], false);

                        // *** disabled due to the way Quadrant's API handles lists ***
                        // if(!empty($list->quadrant_uid))
                        // {
                        //     TODO: validate response
                        //     $quadrant = new Quadrant();
                        //     $res = $quadrant->putList($list->quadrant_uid, [
                        //         [
                        //             'email' => $address->email,
                        //             'data' => array_merge($custom_data, [
                        //                 'uuid' => $address->id,
                        //                 'first_name' => $address->first_name,
                        //                 'last_name' => $address->last_name,
                        //             ])
                        //         ]
                        //     ], []);
                        // }
                    }
                    else
                    {
                        $list->addresses()->sync([$address->id], false);

                        // *** disabled due to the way Quadrant's API handles lists ***
                        // if(!empty($list->quadrant_uid))
                        // {
                        //     TODO: validate response
                        //     $quadrant = new Quadrant();
                        //     $res = $quadrant->putList($list->quadrant_uid, [
                        //         [
                        //             'email' => $address->email, 
                        //             'data' => [
                        //                 'uuid' => $address->id,
                        //                 'first_name' => $address->first_name,
                        //                 'last_name' => $address->last_name
                        //             ]
                        //         ]
                        //     ], []);
                        // }
                    }

                    DB::commit();

                    return response()->json(['data' => $address], 201);
                }

            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }   
    }

    /**
     * Handle removal of addresses from a list
     * @param  Request $request
     * @param  string  $address Address uuids to remove
     */
    protected function removeAddresses(Request $request, $addresses)
    {
        // find any addresses that are still linked to another list
        $linked_to = DB::table('list_addresses')
            ->whereIn('address_id', $addresses)
            ->lists('address_id');

        // find any addresses that have been sent an email
        $sent_to = DB::table('emailer_stats')
            ->whereIn('address_id', $addresses)
            ->lists('address_id');

        // then we can remove the orphaned addresses
        return Address::whereIn('id', $addresses)
            ->whereNotIn('id', $sent_to)
            ->whereNotIn('id', $linked_to)
            ->delete();
    }

    /**
     * DELETE an address by uuid from this list
     * @param  Request $request 
     * @param  string  $id      Address list uuid
     * @param  string  $address      Address uuid
     */
    public function deleteAddress(Request $request, $id, $address)
    {
        try
        {   
            if($list = AddressList::find($id))
            {
                if(!($address = Address::find($address)))
                {
                    throw new APINotFoundException('Resource not found');
                }

                DB::beginTransaction();

                $list->addresses()->detach([$address->id]);

                // *** disabled due to the way Quadrant's API handles lists ***
                // if(!empty($list->quadrant_uid))
                // {
                    // update the lists on Quadrant's system
                    // TODO: validate response
                    // $quadrant = new Quadrant();
                    // $res = $quadrant->putList($list->quadrant_uid,[],[$address]);
                // }

                // remove the address completely if it no longer belongs to a list
                // and does not have any stats saved for it.
                $this->removeAddresses($request, [$address->id]);

                DB::commit();

                return response()->json([], 204);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }   
    }

    /**
     * DELETE an array of addresses from this list
     * @param  Request $request 
     * @param  string  $id      Address list uuid
     */
    public function deleteAddresses(Request $request, $id)
    {
        try
        {   
            if($list = AddressList::find($id))
            {
                if(!$request->has('uuid') || !is_array($request->input('uuid')))
                {
                    throw new APIBadRequestException('Request missing address array', 422);
                }

                DB::beginTransaction();

                $uuids = $request->input('uuid');
                $list->addresses()->detach($uuids);

                // *** Disabled due to the way Quadrant's API handles lists ***
                // if(!empty($list->quadrant_uid))
                // {
                    // Update the lists on Quadrant's system
                    // TODO: validate response
                    // $quadrant = new Quadrant(1);
                    // $res = $quadrant->putList($list->quadrant_uid,[],$addresses);
                // }

                // remove the address completely if it no longer belongs to a list
                // and does not have any stats saved for it.
                $result = $this->removeAddresses($request, $uuids);

                DB::commit();

                return response()->json(['count' => $result], 200);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }   
    }

    /**
     * Block an address by uuid from distribution and remove from Quadrant's system
     * @param  Request $request 
     * @param  string  $id      Address List UUID
     * @param  string  $address      Address UUID
     */
    public function blockAddress(Request $request, $id, $address)
    {
        try
        {   
            if($list = AddressList::find($id))
            {
                if(!($address = Address::find($address)))
                {
                    throw new APINotFoundException('Resource not found');
                }

                DB::beginTransaction();

                $address->spam = !$address->spam;
                $address->save();

                // *** disabled due to the way Quadrant's API handles lists ***
                // if(!empty($list->quadrant_uid))
                // {
                //     $quadrant = new Quadrant();
                //     if($address->spam)
                //     {
                //         $data = [
                //             'uuid' => $address->id,
                //             'first_name' => $address->first_name,
                //             'last_name' => $address->last_name,
                //         ];

                //         if($custom_data = $address->address_lists()->where('list_id', $list->id)->first())
                //         {
                //             if($custom_data = json_decode($custom_data->pivot->custom_data))
                //             {
                //                 $data = array_merge($data, (array)$custom_data);
                //             }
                //         }

                //         // TODO: validate response
                //         $res = $quadrant->putList($list->quadrant_uid,[
                //             [
                //                 'email' => $address->email,
                //                 'data' => $data
                //             ]
                //         ],[]);
                //     }
                //     else
                //     {
                //         $res = $quadrant->putList($list->quadrant_uid,[],[$address]);
                //     }
                // }

                DB::commit();

                return response()->json(['data' => $address], 200);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }   
    }

    /**
     * Block addresses by uuid from distribution and remove from Quadrant's system
     * @param  Request $request 
     * @param  string  $id      Address List UUID
     */
    public function blockAddresses(Request $request, $id)
    {
        try
        {   
            if($list = AddressList::find($id))
            {
                DB::beginTransaction();

                if(!$request->has('addresses') || !is_array($request->input('addresses')))
                {
                    throw new APIBadRequestException('Request missing address array', 422);
                }

                $result = Address::whereIn('id', $request->input('addresses'))
                    ->update(['spam' => DB::raw('!spam')]);
                
                // *** Disabled due to the way Quadrant's API handles lists ***
                // if(!empty($list->quadrant_uid))
                // {
                //     $addresses = Address::whereIn('id', $request->input('addresses'));
                //     foreach($addresses as $address)
                //     {

                //         $data = [
                //             'uuid' => $address->id,
                //             'first_name' => $address->first_name,
                //             'last_name' => $address->last_name,
                //         ];

                //         if($address->spam)
                //         {
                //             if($custom_data = $address->address_lists()->where('list_id', $list->id)->first())
                //             {
                //                 if($custom_data = json_decode($custom_data->pivot->custom_data))
                //                 {
                //                     $data = array_merge($data, (array)$custom_data);
                //                 }
                //             }

                //             $add[] = [
                //                 'email' => $address->email,
                //                 'data' => $data
                //             ];
                //         }
                //         else
                //         {
                //             $remove[] = ['email' => $address->email]; 
                //         }
                //     }

                //     // Update the list on Quadrant's system
                //     // TODO: validate response
                //     $quadrant = new Quadrant();
                //     $res = $quadrant->putList($list->quadrant_uid, $add, $remove);

                //     }
                // }

                DB::commit();

                return response()->json(['count' => $result], 200);
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }   
    }

    /**
     * Export all addresses belonging to a specific Address List to CSV
     * @param  Request $request 
     * @param  string  $id      Address List UUID
     */
    public function exportAddresses(Request $request, $id)
    {
        // Set the execution limit a bit higher, exporting might take a while.
        set_time_limit(300);

        try
        {   
            if($list = AddressList::find($id))
            {
                // parse our header row columns
                $primary = array_keys(Address::first()->attributesToArray());
                if(!empty($list->custom_fields))
                {
                    // parse custom field names
                    $custom = array_map(function($field){
                        return $field->name;
                    }, json_decode($list->custom_fields));
                }
                else
                {
                    $custom = [];
                }

                $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
                $csv->insertOne(array_merge($primary, $custom));

                // insert data for each address in the list
                foreach($list->addresses()->get() as $address)
                {
                    $data = $address->toArray();
                    $custom_data = json_decode($address->pivot->custom_data);
                    foreach($custom as $field)
                    {
                        if(!empty($custom_data) && !empty($custom_data->$field))
                        {
                            array_push($data, $custom_data->$field);
                        }
                        else
                        {
                            array_push($data, '');
                        }
                    }
                    $csv->insertOne($data);
                }

                $filename = sprintf('%s - %s.csv', $list->name, date('Y-m-d'));
                return response($csv->__toString())
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', "attachment; filename='$filename'");
            }
            else
            {
               throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }   
    }

}

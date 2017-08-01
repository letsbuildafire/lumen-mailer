<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use DB;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\AddressList;

use App\Http\Controllers\QuadrantController as Quadrant;
use Laravel\Lumen\Routing\Controller as BaseController;

class AddressController extends BaseController
{
    use APIController;

    const MODEL = 'App\Models\Address';
    
    /**
     * GET an address by uuid
     * @param  Request $request 
     * @param  string  $id      Address uuid
     */
    public function get(Request $request, $id)
    {
        // if we don't need the custom data from the associated list
        if(!$request->input('custom_data')){
            return parent::get($request, $id);
        }
        else
        {
            $list_id = $request->input('custom_data', null);
            if(!$list_id)
            {
                throw new APIBadRequestException('Missing uuid in custom_data parameter', 422);
            }
        }

        try
        {   
            if($address = Address::find($id))
            {
                // check to see if there is a list for custom data
                if($custom_data = $address->address_lists()->where('list_id', $list_id)->first())
                {
                    if($custom_data = json_decode($custom_data->pivot->custom_data))
                    {
                        $address->custom_data = $custom_data;
                    }
                }
                return response()->json(['data' => $address], 200);
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
     * POST a new address
     * @param  Request $request 
     */
    public function post(Request $request)
    {
        try
        {
            $address = new Address;
            $address->first_name = $request->input('first_name');
            $address->last_name = $request->input('last_name');
            $address->email = $request->input('email');
            $address->code = $request->input('code');
            
            $address->save();
            return response()->json(['data' => $address], 201);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
    
    /**
     * PUT updates to an address by uuid
     * @param  Request $request 
     * @param  string  $id      Address uuid
     */
    public function put(Request $request, $id)
    {
        try
        {
            if($address = Address::find($id))
            {
                $address->first_name = $request->input('first_name', $address->first_name);
                $address->last_name = $request->input('last_name', $address->last_name);
                $address->email = $request->input('email', $address->email);
                $address->code = $request->input('code', $address->code);

                $address->save();

                // update the custom data for a given list if specified.
                if($request->input('sync_list')
                    && $list = AddressList::find($request->input('sync_list')))
                {

                    $custom_data = json_encode($request->input('custom_data',
                         json_decode($address->custom_data)));
                    if(!$custom_data){
                        $custom_data = null;
                    } 

                    $list->addresses()->sync([
                        $address->id => [
                            'custom_data' => $custom_data
                        ]], false);

                    $address->custom_data = $custom_data;
                }

                return response()->json(['data' => $address], 200);
            }
            else{
                throw new APINotFoundException('Resource not found');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * DELETE an address by uuid
     * @param  Request $request  
     * @param  string  $id       Address uuid
     * @param  function  $callback 
     */
    public function delete(Request $request, $id, $callback = null)
    {
        try
        {   
            if($address = Address::find($id))
            {
                DB::beginTranaction();

                foreach($address->address_lists()->get() as $list)
                {
                    // *** disabled due to the way Quadrant's API handles lists ***
                    // if($list->quadrant_uid)
                    // {
                        // TODO: validate response
                        // $quadrant = new Quadrant();
                        // $res = $quadrant->putList($list->quadrant_uid,[],[
                        //     ['email' => $address->email]
                        // ]);
                    // }
                }

                // remove the address from any lists it belongs to.
                $address->address_lists()->detach();

                // if the address has not had an email sent to it, we can remove it
                if(!$address->emailers()->count())
                {
                    $address->delete();
                }
                else
                {
                    throw new APIBadRequestException(sprintf('Cannot remove an %s /
                        that has had an %s sent to it. Block it instead.',
                        strtolower(env('TITLE_ADDRESSES')),
                        strtolower(env('TITLE_EMAILERS'))));
                }

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
     * GET lists that an address belongs to
     * @param  Request $request 
     * @param  string  $id      Address uuid
     */
    public function getLists(Request $request, $id)
    {
        try
        {   
            if($address = Address::find($id))
            {
                $lists = $address->address_lists()->get();
                return response()->json(['data' => $lists], 200);
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

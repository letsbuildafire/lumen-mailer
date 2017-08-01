<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Address;
use App\Models\Emailer;
use App\Models\EmailerOpen;
use App\Models\EmailerClick;
use App\Helpers\EmailerHelpers;
use Illuminate\Http\Request;

use App\Exceptions\BadRequestException;
use App\Http\Controllers\QuadrantController as Quadrant;

class EmailerController extends Controller
{

    public static $model = 'App\Models\Emailer';

    /**
     * Generate a tracking pixel to track an open event for an
     * emailer_uuid, address_uuid pair.
     * @param  Request $request      
     * @param  string  $emailer_uuid The Emailer uuid
     * @param  string  $address_uuid The Address uuid
     * @return string                Image code to display.
     */
    public function pixel(Request $request, $emailer_uuid, $address_uuid)
    {
        try
        {
            $open = new EmailerOpen;
            $open->emailer_id = $emailer_uuid;
            $open->address_id = $address_uuid;
            $open->useragent = $request->header('User-Agent');
            $open->ip_address = $request->ip();

            $open->save();

            // Update the open count in the stats table
            DB::table('emailer_stats')->where('emailer_id', '=', $emailer_uuid)
                ->where('address_id', '=', $address_uuid)
                ->increment('opens');

            // Send back a transparent 1px-by-1px png
            $im = imagecreatefromstring(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII='));
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            die();
        }
        catch(Exception $e)
        {
            // TODO: Handle error
            // This might be a result of foreign key not existing.
            
            // Send back a transparent 1px-by-1px png
            $im = imagecreatefromstring(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII='));
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            die();
        }
    }

    /**
     * Track an click event for an emailer_uuid, address_uuid, url set.
     * @param  Request $request      
     * @param  string  $emailer_uuid The Emailer uuid
     * @param  string  $url          The base64 encoded redirect url
     * @param  string  $address_uuid The Address uuid
     */
    public function click(Request $request, $emailer_uuid, $url, $address_uuid)
    {
        try
        {
            $redirect = EmailerHelpers::base64url_decode($url);

            $click = new EmailerClick;
            $click->emailer_id = $emailer_uuid;
            $click->address_id = $address_uuid;
            $click->url = $redirect;
            $click->useragent = $request->header('User-Agent');
            $click->ip_address = $request->ip();

            $click->save();

            // Update the open count in the stats table
            DB::table('emailer_stats')->where('emailer_id', '=', $emailer_uuid)
                ->where('address_id', '=', $address_uuid)
                ->increment('clicks');

            return redirect()->to($redirect);
        }
        catch(Exception $e)
        {
            // TODO: Handle error
            // This might be a result of foreign key not existing. Redirect anyway
            return redirect()->away($redirect);
        }
    }

    /**
     * Render an emailer
     * @param  Request $request
     * @param  string  $emailer_uuid The Emailer uuid
     */
    public function show(Request $request, $emailer_uuid, $address_uuid = null)
    {
        if($emailer = Emailer::find($emailer_uuid))
        {
            if($address = Address::find($address_uuid)){
                try
                {
                    $open = new EmailerOpen;
                    $open->emailer_id = $emailer_uuid;
                    $open->address_id = $address_uuid;
                    $open->useragent = $request->header('User-Agent');
                    $open->ip_address = $request->ip();

                    $open->save();

                    // Update the open count in the stats table
                    DB::table('emailer_stats')->where('emailer_id', '=', $emailer_uuid)
                        ->where('address_id', '=', $address_uuid)
                        ->increment('opens');
                }
                catch(Exception $e)
                {
                    // TODO: Handle error
                    // This might be a result of foreign key not existing. Display
                    // the emailer anyway.
                }
            } else {
                // If we aren't specifying an address uuid we are likely previewing
                // the emailer and don't want to generate working tracking links.
                $emailer_uuid = null;
            }

            return view("emails.{$emailer->template->source}", [
                'content' => $emailer->content,
                'signature' => $emailer->signature,
                'emailer_id' => $emailer_uuid,
                'address_id' => $address_uuid
            ]);
        }
        else
        {
            return view('default');
        }
    }

    /**
     * Render an unsubscribe page for a specific address and emailer combination
     * @param  Request $request      
     * @param  string  $emailer_uuid The Emailer uuid
     * @param  string  $address_uuid The Address uuid
     */
    public function unsubscribe(Request $request, $emailer_uuid, $address_uuid)
    {
        if($emailer = Emailer::find($emailer_uuid))
        {
            return view('unsubscribe', [
                'emailer_uuid' => $emailer_uuid,
                'address_uuid' => $address_uuid
            ]);
        }
        else
        {
            throw new BadRequestException(sprintf('%s not found', env('TITLE_EMAILERS')), 404);
        }
    } 

    /**
     * Unsubscribe an address and mark it as unsubscribed at that specific emailer
     * @param  Request $request      
     * @param  string  $emailer_uuid The Emailer uuid
     * @param  string  $address_uuid The Address uuid
     */
    public function confirmUnsubscribe(Request $request, $emailer_uuid, $address_uuid)
    {
        try
        {
            if(!($address = Address::find($address_uuid)))
            {
                throw new BadRequestException(sprintf('%s does not exist.', env('TITLE_ADDRESSES')));
            }

            $address->spam = 0;
            $address->save();

            // Update the unsubscribe state in the stats table
            DB::table('emailer_stats')->where('emailer_id', '=', $emailer_uuid)
                ->where('address_id', '=', $address_uuid)
                ->update(['unsubscribed' => 1]);

            return response()->json([], 200);
        }
        catch(Exception $e)
        {
            // TODO: Handle error
            // This might be a result of being unsubscribed already.
            // Maybe display error page?
            throw $e;
        }
    }  

    /**
     * Resubscribe an address
     * @param  Request $request      
     * @param  string  $emailer_uuid The Emailer uuid
     * @param  string  $address_uuid The Address uuid
     */
    public function resubscribe(Request $request, $emailer_uuid, $address_uuid)
    {
        try
        {
            if(!($address = Address::find($address_uuid)))
            {
                throw new BadRequestException(sprintf('%s does not exist.', env('TITLE_ADDRESSES')));
            }

            $address->spam = 1;
            $address->save();

            // Update the unsubscribe state in the stats table
            DB::table('emailer_stats')->where('emailer_id', '=', $emailer_uuid)
                ->where('address_id', '=', $address_uuid)
                ->update(['unsubscribed' => 0]);

            return view('resubscribe');
        }
        catch(Exception $e)
        {
            // TODO: Handle error
            // This might be a result of being unsubscribed already.
            // Maybe display error page?
            throw $e;
        }
    }

    /**
     * Create a list on Quadrant's system comprised of addresses from all lists on the emailer
     * @param  Emailer $emailer 
     * @return String          The uid of the list on Quadrant's system 
     */
    public static function createQuadrantList($emailer)
    {
        $addresses = [];
        foreach($emailer->lists as $list)
        {
            foreach($list->addresses as $address)
            {
                if(!$address->spam || !$address->active) continue;

                if(!empty($address->pivot->custom_data))
                {
                    $addresses[] = [
                        'email' => $address->email,
                        'data' => array_merge(
                            (array)json_decode($address->pivot->custom_data),
                            [
                                'uuid' => $address->id,
                                'first_name' => $address->first_name,
                                'last_name' => $address->last_name,
                                'list_name' => $list->name
                            ])
                    ];
                }
                else
                {
                    $addresses[] = [
                        'email' => $address->email,
                        'data' => [
                            'uuid' => $address->id,
                            'first_name' => $address->first_name,
                            'last_name' => $address->last_name,
                            'list_name' => $list->name
                        ]
                    ];
                }

            }
        }

        try
        {
            // POST the emailer to Quadrant's system
            $quadrant = new Quadrant(1);

            if($emailer->quadrant_list_uid)
            {
                $res = $quadrant->deleteList($emailer->quadrant_list_uid);
                // TODO: validate delete command
            }

            $res = $quadrant->postList($addresses);

            return $res->uid;
        }
        catch(Exception $e)
        {
            DB::rollback();
            throw new APIException($e->getMessage());
        }
    } 
}

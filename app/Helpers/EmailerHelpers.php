<?php

namespace App\Helpers;

use DB;
use App\Models\Emailer;

class EmailerHelpers {

    /**
     * Generate a tracking pixel to be used to keep track of opens
     * @param  string $emailer_uuid The Emailer uuid
     */
    public static function generateTrackingPixel($emailer_uuid, $address_uuid = null)
    {
        // If we don't have an emailer uuid (preview links), then
        // we don't want to track anything.
        if(($emailer = Emailer::find($emailer_uuid)) && empty($address_uuid))
        {
            $url = sprintf('http://%s/%s/%s/%s/%s/p.png',
                env('APP_SERVER_NAME'),
                'emails',
                $emailer_uuid,
                'o',
                '{{uuid}}'
            );
            return sprintf('<img src="%s" class="pixel" />', $url);
        }
        else
        {
            return '';
        }
        
    }

    /**
     * Generate a tracking pixel to be used to keep track of opens
     * @param  string $emailer_uuid The Emailer uuid
     */
    public static function generateOnlineLink($emailer_uuid, $address_uuid = null)
    {
        // If we don't have an emailer uuid (preview links), then
        // we don't want to track anything.
        if($emailer = Emailer::find($emailer_uuid))
        {
            // If we aren't supplying an address uuid then we are compiling
            // on Quadrant's server so need to include a placeholder
            if(empty($address_uuid))
            {
                $address_uuid = '{{uuid}}';
            }

            return sprintf('http://%s/emails/%s/o/%s',
                env('APP_SERVER_NAME'),
                $emailer->id,
                $address_uuid
            );
        } else {
            return '#';
        }
    }

    /**
     * Generate a tracking pixel to be used to keep track of opens
     * @param  string $emailer_uuid The Emailer uuid
     */
    public static function generateRedirectLink($emailer_uuid, $url, $address_uuid = null)
    {
        // If we don't have an emailer uuid (preview links), then
        // we don't want to track anything.
        if($emailer = Emailer::find($emailer_uuid))
        {
            // If we aren't supplying an address uuid then we are compiling
            // on Quadrant's server so need to include a placeholder
            if(empty($address_uuid))
            {
                $address_uuid = '{{uuid}}';
            }

            return sprintf('http://%s/%s/%s/%s/%s/%s',
                env('APP_SERVER_NAME'),
                'emails',
                $emailer_uuid,
                'c',
                self::base64url_encode($url),
                $address_uuid
            );
        }
        else
        {
            return $url;
        }
    }

    /**
     * Generate an unsubscribe link to allow a user to unsubscribe
     * @param  string $emailer_uuid The Emailer uuid
     */
    public static function generateUnsubscribeLink($emailer_uuid, $address_uuid = null)
    {
        // If we don't have an emailer uuid (preview links), then
        // we don't want to track anything.
        if($emailer = Emailer::find($emailer_uuid))
        {
            // If we aren't supplying an address uuid then we are compiling
            // on Quadrant's server so need to include a placeholder
            if(empty($address_uuid))
            {
                $address_uuid = '{{uuid}}';
            }

            return sprintf('http://%s/%s/%s/%s/%s',
                env('APP_SERVER_NAME'),
                'emails',
                $emailer_uuid,
                'u',
                $address_uuid
            );
        }
        else
        {
            return "#";
        }

    }

    /**
     * Encode a URL safe base64 encoded string
     * @param  mixed $data The information to encode
     */
    public static function base64url_encode($data)
    { 
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 

    /**
     * Decode a URL safe base64 encoded string
     * @param  string $data The base64 encoded string to decode
     */
    public static function base64url_decode($data)
    { 
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }   

}

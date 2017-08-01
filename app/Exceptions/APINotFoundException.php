<?php
namespace App\Exceptions;

class APINotFoundException extends APIException
{
    public function __construct($message = null, $code = 404, Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

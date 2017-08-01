<?php
namespace App\Exceptions;

class APINotAllowedException extends APIException
{
    public function __construct($message = null, $code = 401, Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

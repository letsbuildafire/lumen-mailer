<?php
namespace App\Exceptions;

class APIBadRequestException extends APIException
{
    public function __construct($message = null, $code = 400, Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

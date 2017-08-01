<?php
namespace App\Exceptions;

class QuadrantException extends \Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

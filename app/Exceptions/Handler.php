<?php

namespace App\Exceptions;

use Exception;

use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $code = $e->getCode() ?: 500;
        
        // Let Angular handle every route that doesn't match
        if($e instanceof NotFoundHttpException)
        {
            return view('default');
        }

        return response()->json(['message' => $e->getMessage()], $code); 
    }
}

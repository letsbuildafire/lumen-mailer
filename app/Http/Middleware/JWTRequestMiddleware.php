<?php

namespace App\Http\Middleware;

use Closure;
use \App\Models\User;
use \Firebase\JWT\JWT;

class JWTRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = array_slice(func_get_args(),2);
        $auth = ($request->header('Authorization'));

        if(empty($auth)) 
        {
            // Check if the token was passed as a GET param
            if($request->has('token')){
                $auth = 'Bearer ' . $request->input('token');
            }
            else
            {
                return response()->json([
                    'code' => 4010,
                    'message' => 'This request requires authorization'
                ], 401);
            }
        }

        list($jwt) = sscanf($auth, 'Bearer %s');


        try // validate the token
        {
            JWT::$leeway = 60;
            $secret = base64_decode(env('APP_SECRET'));
            $token = JWT::decode($jwt, $secret, array('HS512'));
            
            // validate that this token was made for us
            $user = User::find($token->data->id);

            if(!$user)
            {
                return response()->json([
                    'code' => 4011,
                    'message' => 'Invalid authorization token'
                ], 401);
            }
            

            // Ensure that the user has access to this route
            if(count($roles) && !in_array($user->role, $roles)){
                return response()->json([
                    'code' => 4012,
                    'message' => 'Insufficient permissions for this request'
                ], 401);
            }

            // Add the user's information to the request
            // so we can access it in the routes
            $request->merge(compact('user'));
        }
        catch(\Exception $e)
        {
            $code = $e->getCode() ?: 4000;
            if($e instanceof \Firebase\JWT\ExpiredException)
            {
                $code = 4013;
            }

             return response()->json([
                'code' => $code,
                'error'=> $e->getMessage()
            ], 401);
        }

        // Pass the request to the controller
        return $next($request);
    }
}

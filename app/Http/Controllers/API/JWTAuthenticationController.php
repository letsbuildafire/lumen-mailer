<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use Laravel\Lumen\Routing\Controller as BaseController;

class JWTAuthenticationController extends BaseController
{
    use APIController;

    const MODEL = 'App\Models\User';
    
    /**
     * Authenticate a user and generate an auth token
     * @param  Request $request 
     */
    public function authenticate(Request $request)
    {
        try
        {
            if($user = User::where('username', $request->input('username'))->first())
            {
                if(password_verify($request->input('password'), $user->password))
                {
                    $token_id = base64_encode(mcrypt_create_iv(32, MCRYPT_RAND));
                    $issued = time();
                    $from = $issued + 5;  // Add 5 seconds
                    $expires = $from + env('APP_AUTH_TIMEOUT');
                    $serverName = env('APP_SERVER_NAME');
                    
                    /*
                     * Create the token as an array
                     */
                    $token = [
                        'iat' => $issued, // When the token was generated
                        'jti' => $token_id, // Unique ID for the token
                        'iss' => $serverName, // Issuer
                        'nbf' => $from, // Valid after
                        'exp' => $expires, // Expires after
                        'data' => [ // User data
                            'id'   => $user->id,
                            'username' => $user->username,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'signature' => $user->signature,
                            'email' => $user->email,
                            'role' => $user->role
                        ]
                    ];
                    
                    $secure_token = JWT::encode(
                        $token,
                        base64_decode(env('APP_SECRET')),
                        'HS512'
                    );
                    
                    return response()->json([
                        'user' => $token['data'],
                        'expires' => $expires,
                        'token' => $secure_token
                    ], 200);
                }
                else
                {
                    throw new APINotAllowedException("Invalid login credentials");
                }
            }
            else
            {
                throw new APINotAllowedException("Invalid login credentials");
                
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
}

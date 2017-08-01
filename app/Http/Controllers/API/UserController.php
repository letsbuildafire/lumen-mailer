<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Exceptions\APIException;
use App\Exceptions\APIBadRequestException;
use App\Exceptions\APINotFoundException;
use App\Exceptions\APINotAllowedException;

use Illuminate\Http\Request;
use App\Models\User;

use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    use APIController {
        delete as apiDelete;
    }

    const MODEL = 'App\Models\User';
    
    /**
     * GET a user by uuid
     * @param  Request $request 
     * @param  string  $id      User uuid
     */
    public function get(Request $request, $id)
    {
        try
        {
            // users should only be able to edit themselves, unless they are an admin
            if($request->user->role != 'ADMIN' && $id != $request->user->id)
            {
                throw new APINotAllowedException('Access to resource denied');
            }

            if($user = User::find($id))
            {
                return response()->json(['data' => $user], 200);
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
     * POST a new user
     * @param  Request $request 
     */
    public function post(Request $request)
    {
        try
        {
            // username needs to remain unique
            if($exists = User::findByUsername($request->input('username')))
            {
                throw new APIBadRequestException('Cannot create user; username exists', 409);
            }

            $user = new User;
            $user->username = $request->input('username');
            $user->email = $request->input('email');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->signature = $request->input('signature');

            // default role is USER
            $user->role = $request->input('role', 'USER');
            $user->password = password_hash($request->input('password'), PASSWORD_BCRYPT);
            $user->save();

            return response()->json(['data' => $user], 201);
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }
    
    /**
     * PUT updates to a User by uuid
     * @param  Request $request 
     * @param  string  $id      User uuid
     */
    public function put(Request $request, $id)
    {
        try
        {
            // users should only be able to edit themselves, unless they are an admin
            if($request->user->role != "ADMIN" && $id != $request->user->id)
            {
                throw new APINotAllowedException('Access to resource denied');
            }

            if($user = User::find($id))
            {
                // username needs to remain unique
                if($exists = User::findByUsername($request->input('username')))
                {
                    throw new APIBadRequestException('Cannot update user; username exists', 409);
                }

                $user->username = $request->input('username', $user->username);
                $user->email = $request->input('email', $user->email);
                $user->first_name = $request->input('first_name', $user->first_name);
                $user->last_name = $request->input('last_name', $user->last_name);
                $user->signature = $request->input('signature', $user->signature);

                // prevent a non-admin user from escalating their own priviledges
                if($request->user->role == "ADMIN")
                {
                    $admin_count = User::where('role', 'ADMIN')->count();

                    // don't allow an admin user to demote themself if they are the only admin
                    if($admin_count == 1 && $user->role == 'ADMIN' && $request->input('role') != 'ADMIN')
                    {
                        throw new APIBadRequestException('There must be at least one admin user');
                    }

                    $user->role = $request->input('role', $user->role);
                }

                if($request->has('password'))
                {
                    $user->password = password_hash($request->input('password'), PASSWORD_BCRYPT);
                }
                $user->save();

                return response()->json(['data' => $user], 200);
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
     * Delete users from an array of uuids
     * @param  Request $request 
     */
    public function deleteMany(Request $request)
    {
        try
        {
            if(!$request->has('users'))
            {
                // we probably don't want to truncate the user table!
                // $this->truncate($request);
                throw new APIBadRequestException('Request invalid or missing user array', 422);
            }

            if(!is_array($users = $request->input('users')))
            {
                throw new APIBadRequestException('Request invalid or missing user array', 422);
            }

            if(in_array($request->user->id, $users))
            {
                throw new APIBadRequestException('Cannot remove currently authenticated user');
            }

            // make sure that we don't remove all admins
            $admin_to_remove = User::whereIn('id', $users)->where('role', 'ADMIN')->count();
            $admin_existing = User::where('role', 'ADMIN')->count();

            if($admin_to_remove < $admin_existing)
            {
                $count = User::destroy($users);
                return response()->json(['count' => $count], 202);
            }
            else
            {
                throw new APIBadRequestException('There must be at least one admin user');
            }
        }
        catch(Exception $e)
        {
            throw new APIException($e->getMessage());
        }
    }

    /**
     * Delete a user by uuid
     * @param  Request $request 
     * @param  string  $id      User uuid
     */
    public function delete(Request $request, $id)
    {
        // make sure we aren't deleting our own user or the last admin
        $this->apiDelete($request, $id, function($req, $user){
            $admin_count = User::where('role', 'ADMIN')->count();
            
            if($user->role == 'ADMIN' && $admin_count == 1)
            {
                throw new APIBadRequestException('There must be at least one admin user');
            }

            if($user->id == $req->user->id)
            {
                throw new APIBadRequestException('Cannot remove currently authenticated user');
            }
        });
    }
}

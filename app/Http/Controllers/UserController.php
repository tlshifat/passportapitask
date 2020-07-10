<?php

namespace App\Http\Controllers;

use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = auth()->user()->all();

        return response()->json([
                                    'success' => true,
                                    'data' => $users
                                ]);
    }

    public function show($id)
    {
        $user = auth()->user()->find($id);

        if (!$user) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'User with id ' . $id . ' not found'
                                    ], 400);
        }

        return response()->json([
                                    'success' => true,
                                    'data' => $user->toArray()
                                ], 400);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if( empty(auth()->user()->is_admin)){
            Log::error('Only admin can add user.');
            return response()->json([
                                        'success' => false,
                                        'message' => 'Only admin can add user'
                                    ], 500);

        }
        $user = User::create([
                                 'name' => $request->name,
                                 'email' => $request->email,
                                 'password' => bcrypt($request->password),
                                 'is_admin' => $request->is_admin
                             ]);

        $token = $user->createToken('inzerted')->accessToken;

        return response()->json(['success' => true], 200);
    }

    public function update(Request $request, $id)
    {
        if(auth()->user()->id != $id && empty(auth()->user()->is_admin)){
            Log::error('users can be edited by themselves or admins.');
            return response()->json([
                                        'success' => false,
                                        'message' => 'users can be edited by themselves or admins.'
                                    ], 400);
        }

        $user = auth()->user()->find($id);

        if (!$user) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'User with id ' . $id . ' not found'
                                    ], 400);
        }

        $this->validate($request, [
            'name' => 'min:3',
            'email' => 'email|unique:users',
            'password' => 'min:6',
        ]);
        $request['password']=bcrypt($request->password);

        $updated = $user->fill($request->all())->save();

        if ($updated)
            return response()->json([
                                        'success' => true
                                    ]);
        else

            return response()->json([
                                        'success' => false,
                                        'message' => 'Product could not be updated'
                                    ], 500);
    }

    public function destroy($id)
    {
        if(empty(auth()->user()->is_admin)){
            Log::error('users can be deleted by  admin only.');
            return response()->json([
                                        'success' => false,
                                        'message' => 'users can be deleted by  admin only.'
                                    ], 400);
        }
        $user = auth()->user()->find($id);

        if (!$user) {
            return response()->json([
                                        'success' => false,
                                        'message' => 'User with id ' . $id . ' not found'
                                    ], 400);
        }

        if ($user->delete()) {
            return response()->json([
                                        'success' => true
                                    ]);
        } else {
            Log::error('User could not be deleted');
            return response()->json([
                                        'success' => false,
                                        'message' => 'User could not be deleted'
                                    ], 500);
        }
    }
}

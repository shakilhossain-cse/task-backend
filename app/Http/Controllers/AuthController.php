<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
        //register user
        public function register(Request $request)
        {
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email:rfc|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);

            $token = $user->createToken(config('basic.apptoken'))->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }

        // login user
        public function login(Request $request)
        {
            $fields = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string'
            ]);

            // Check email
            $user = User::where('email', $fields['email'])->first();

            // Check password
            if (!$user || !Hash::check($fields['password'], $user->password)) {
                return response([
                    'message' => 'Bad creds'
                ], 401);
            }

            $token = $user->createToken(config('basic.apptoken'))->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 200);
        }



        // logout user
        public function logout()
        {
            auth()->user()->tokens()->delete();
            return [
                'message' => 'Logged out'
            ];
        }




}

<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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


    public  function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['email' => $email, 'token' => Hash::make($token), 'created_at' => now()]
        );

        Mail::to($email)->send(new ResetPasswordMail($token));

        return response()->json(['message' => 'Password reset link sent successfully']);
    }

    public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required|string',
        'password' => 'required|string|min:5',
    ]);

    $token = $request->token;
    $password = $request->password;

    $tokenRecord = DB::table('password_reset_tokens')
        ->where('token', Hash::make($token))
        ->first();

    if (!$tokenRecord) {
        return response()->json(['message' => 'Invalid token'], 422);
    }

    $userId = $tokenRecord->user_id;

    DB::table('users')
        ->where('id', $userId)
        ->update(['password' => Hash::make($password)]);

    DB::table('password_reset_tokens')
        ->where('token', Hash::make($token))
        ->delete();

    return response()->json(['message' => 'Password reset successfully']);
}

}

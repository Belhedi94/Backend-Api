<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function authenticate(Request $request) {

        $fields = $request->validate([
            'login' => 'required',
            'password' => 'required|string'
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';

        if (Auth::attempt([$loginType => $fields['login'], 'password' => $fields['password']])) {
            $user = User::where($loginType, $fields['login'])->first();
            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }

        return response([
            'message' => 'The provided credentials do not match our records.'
        ], 401);

    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ], 201);
    }
}

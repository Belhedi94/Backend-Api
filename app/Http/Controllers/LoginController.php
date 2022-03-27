<?php

namespace App\Http\Controllers;

use App\Http\ResponseMessages;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class LoginController extends Controller
{
    public function authenticate(Request $request) {

        $fields = $request->validate([
            'login' => 'required',
            'password' => 'required|string'
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';

        if (Auth::attempt([$loginType => $fields['login'], 'password' => $fields['password']], $request->remember)) {
            $user = User::where($loginType, $fields['login'])->first();
            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                'user' => new UserResource($user),
                'token' => $token
            ];

            return response($response, 201);
        }

        return response([
            'message' => ResponseMessages::BAD_REQUEST
        ], Response::HTTP_UNAUTHORIZED);

    }

    public function logout() {
        auth()->user()->tokens()->delete();

        if (auth()->user()->getRememberToken() != null) {
            $user = User::find(auth()->user()->getAuthIdentifier());
            $user->remember_token = null;
            $user->save();
        }

        return response()->json([
            'message' => ResponseMessages::LOGGED_OUT
        ], Response::HTTP_OK);
    }
}

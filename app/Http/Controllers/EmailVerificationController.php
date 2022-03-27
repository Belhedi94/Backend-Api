<?php

namespace App\Http\Controllers;

use App\Http\ResponseMessages;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;

class EmailVerificationController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => ResponseMessages::ALREADY_VERIFIED], Response::HTTP_OK);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json (['message' => ResponseMessages::SUCCESSFULLY_VERIFIED], Response::HTTP_OK);
    }
}

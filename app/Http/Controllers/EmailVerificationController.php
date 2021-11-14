<?php

namespace App\Http\Controllers;

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
            //return redirect(env('FRONT_URL') . '/email/verify/already-success');
            return response()->json(['message' => 'Email already verified']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        //return redirect(env('FRONT_URL') . '/email/verify/success');
        return response()->json (['message' => 'Email successfully verified']);
    }
}

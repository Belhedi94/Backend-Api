<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function createUser(Request $request) {
        $fields = $request->validate([
            'first_name' => 'required|alpha|max:15',
            'last_name' => 'required|alpha|max:15',
            'email' => 'required|unique:users,email|email',
            'password' => ['required', 'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()->uncompromised()],
            'username' => 'required|unique:users,username|regex:/^[a-zA-Z][a-z0-9_]*[a-z0-9]$/|max:15',
            'phone' => 'required|numeric',
            'birthdate' => 'required|date',
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'username' => $fields['username'],
            'photo' => 'no-image.png',
            'phone' => $fields['phone'],
            'birthdate' => $fields['birthdate'],
            'is_admin' => true,
            'is_active' => true
        ]);

        return new UserResource($user);

    }
}

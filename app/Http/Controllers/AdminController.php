<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    public function createUser(Request $request) {
        $fields = $request->validate([
            'first_name' => 'required|alpha|max:15',
            'last_name' => 'required|alpha|max:15',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|confirmed|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'username' => 'required|unique:users,username|regex:/^[a-zA-Z][a-z0-9_]*[a-z0-9]$/|max:15',
            'phone' => 'required|numeric',
            'age' => 'required|numeric|max:90',
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'username' => $fields['username'],
            'photo' => 'no-image.png',
            'phone' => $fields['phone'],
            'age' => $fields['age'],
            'is_admin' => true,
            'is_active' => true
        ]);

        return new UserResource($user);

    }
}

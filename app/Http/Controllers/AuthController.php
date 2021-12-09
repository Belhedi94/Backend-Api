<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    public function register(Request $request) {   
        $fields = $request->validate([
            'first_name' => 'required|alpha|max:15',
            'last_name' => 'required|alpha|max:15',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|confirmed|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'username' => 'required|unique:users,username|regex:/^[a-zA-Z][a-z0-9_]*[a-z0-9]$/|max:15',
            'photo' => 'image|mimes:jpg,jpeg,png',
            'phone' => 'required|numeric',
            'age' => 'required|numeric|max:90',
        ]);

        // Handle File Upload
        if($request->hasFile('photo')){
            // Get filename with the extension
            $filenameWithExt = $request->file('photo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('photo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('photo')->storeAs('public/photos', $fileNameToStore);


        } else {
            $fileNameToStore = 'no-image.jpg';
        }

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'username' => $fields['username'],
            'photo' => $fileNameToStore,
            'phone' => $fields['phone'],
            'age' => $fields['age']
        ]);

        event(new Registered($user));

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        //Check email
        $user = User::where('email', $fields['email'])->first();
        //Check Password
        if(!$user || !Hash::check($fields['password'], $user['password'])) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Successfully logged out'
        ];
    }

}

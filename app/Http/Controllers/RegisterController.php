<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use App\Rules\Username;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register(Request $request) {

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
            'username' => ['required','unique:users,username','min:5','max:15', new Username],
            'photo' => 'image|mimes:jpg,jpeg,png',
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'phone' => 'required|numeric',
            'birthdate' => 'required|date',
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
            'is_admin' => false,
            'is_super_admin' => false,
            'is_banned' => false,
            'sexe' => $fields['sexe'],
            'photo' => $fileNameToStore,
            'phone' => $fields['phone'],
            'birthdate' => $fields['birthdate'],
            'role_id' => 3
        ]);

        event(new Registered($user));

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use App\Rules\UsernameRule;
use App\Rules\CountryRule;
use Illuminate\Validation\Rule;


class RegisterController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'first_name' => 'required|alpha|max:15',
            'last_name' => 'required|alpha|max:15',
            'email' => 'required|unique:users,email|email',
            'username' => ['required','unique:users,username','min:5','max:15', new UsernameRule],
            'password' => ['required', 'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()->uncompromised()],
            'birthdate' => 'required|date',
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'phone' => 'required|numeric',
            'avatar' => 'image|mimes:jpg,jpeg,png',
            'country_id' => ['required', new CountryRule()]
        ]);

        // Handle File Upload
        if($request->hasFile('avatar')){
            // Get filename with the extension
            $filenameWithExt = $request->file('avatar')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('avatar')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('avatar')->storeAs('public/avatars', $fileNameToStore);


        } else {
            $fileNameToStore = 'no-image.jpg';
        }

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'birthdate' => $fields['birthdate'],
            'sexe' => $fields['sexe'],
            'phone' => $fields['phone'],
            'avatar' => $fileNameToStore,
            'is_admin' => false,
            'role_id' => 4,
            'country_id' => $fields['country_id']

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

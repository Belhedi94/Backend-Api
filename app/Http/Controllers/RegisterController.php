<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Helpers;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use App\Rules\UsernameRule;
use App\Rules\MobileNumberRule;
use App\Rules\CountryRule;
use Illuminate\Validation\Rule;


class RegisterController extends Controller
{
    public function register(Request $request) {
        $request['mobile_number'] = Helpers::normalizeMobileNumber($request->mobile_number);
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
            'birthday' => 'required|date',
            'sex' => ['required', Rule::in(['M', 'F'])],
            'mobile_number' => ['required', new MobileNumberRule, 'unique:users,mobile_number'],
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
        $role = 4;
        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'birthday' => $fields['birthday'],
            'sex' => $fields['sex'],
            'mobile_number' => $fields['mobile_number'],
            'avatar' => $fileNameToStore,
            'is_admin' => false,
            'role_id' => $role,
            'country_id' => $fields['country_id'],
            'is_active' => true

        ]);

        event(new Registered($user));

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => new UserResource($user),
            'token' => $token
        ];

        return response($response, 201);
    }

}

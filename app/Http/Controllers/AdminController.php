<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use App\Rules\Username;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function createUser(Request $request) {
        if (! Gate::allows('create-user')) {
            return response()->json([
                'code' => 403,
                'message' => 'You don\'t have permission to access this resource.'
            ])->setStatusCode(403);
        }
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
            'sexe' => ['required', Rule::in(['M', 'F'])],
            'phone' => 'required|numeric',
            'birthdate' => 'required|date',
            'role_id' => Rule::in([1, 2, 3, 4])

        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'username' => $fields['username'],
            'photo' => 'no-image.png',
            'sexe' => $fields['sexe'],
            'phone' => $fields['phone'],
            'birthdate' => $fields['birthdate'],
            'is_admin' => true,
            'role_id' => $fields['role_id']
        ]);

        return new UserResource($user);

    }

    public function getAdmins() {
        if (! Gate::allows('get-admins')) {
            return response()->json([
                'code' => 403,
                'message' => 'You don\'t have permission to access this resource'
            ]);
        }
        $admins = User::where('is_admin', 1)->get();

        return UserResource::collection($admins);
    }


}

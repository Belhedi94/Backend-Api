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
                'message' => 'You don\'t have permission to access this resource.'
            ], 403);
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
            'avatar' => 'no-image.png',
            'sexe' => $fields['sexe'],
            'phone' => $fields['phone'],
            'birthdate' => $fields['birthdate'],
            'is_admin' => true,
            'role_id' => $fields['role_id']
        ]);

        return (new UserResource($user))->response()->setStatusCode(200);

    }

    public function getAdmins() {
        if (! Gate::allows('get-admins')) {
            return response()->json([
                'message' => 'You don\'t have permission to access this resource'
            ], 403);
        }
        $admins = User::where('is_admin', 1)->get();

        return UserResource::collection($admins)->response()->setStatusCode(200);
    }

    public function banUser($id) {
        if (! Gate::allows('ban-user')) {
            return response()->json([
                'message' => 'You don\'t have permission to access this resource'
            ], 403);
        }
        $userController = new UserController();
        $result = $userController->doesUserExist($id);
        if (gettype($result) == 'boolean') {
            $user = User::findOrFail($id);
            $user->update([
                'is_banned' => 1
            ]);

            return response()->json([
                'message' => $user->username.' is successfully banned'
            ], 200);
        }

        return $result;

    }


}

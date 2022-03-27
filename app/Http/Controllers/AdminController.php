<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\ResponseMessages;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Helpers;
use Illuminate\Validation\Rules\Password;
use App\Rules\UsernameRule;
use App\Rules\CountryRule;
use App\Rules\MobileNumberRule;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function createUser(Request $request) {
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
            'role_id' => Rule::in([1, 2, 3, 4]),
            'country_id' => ['required', new CountryRule]
        ]);

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'email' => $fields['email'],
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'birthday' => $fields['birthday'],
            'sex' => $fields['sex'],
            'mobile_number' => $fields['mobile_number'],
            'avatar' => 'no-image.png',
            'is_admin' => true,
            'role_id' => $fields['role_id'],
            'country_id' => $fields['country_id']
        ]);

        return (new UserResource($user))->response()->setStatusCode(200);

    }

    public function getAdmins() {
        $admins = User::where('is_admin', 1)->get();

        return UserResource::collection($admins)->response()->setStatusCode(200);
    }

    public function banUser($id) {
        $user = Helpers::doesItExist(User::class, $id);
        if (isset($user)) {
            $user->update([
                'is_active' => 0
            ]);

            return response()->json([
                'message' => ResponseMessages::OK_BANNED
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => ResponseMessages::NOT_FOUND
        ], Response::HTTP_NOT_FOUND);

    }

    public function getBannedUsers() {
        $bannedUsers = User::where('is_active', 0)->get();

        return UserResource::collection($bannedUsers)->response()->setStatusCode(200);

    }

    public function getActiveUsers() {
        $activeUsers = User::where('is_active', 1)->get();

        return UserResource::collection($activeUsers)->response()->setStatusCode(200);

    }


}

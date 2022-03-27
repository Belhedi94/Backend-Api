<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\ResponseMessages;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use App\Rules\UsernameRule;
use App\Rules\MobileNumberRule;
use App\Rules\CountryRule;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all())->response()->setStatusCode(200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Helpers::doesItExist(User::class, $id);
        if ($user)
            return (new UserResource(User::findOrfail($id)))->response()->setStatusCode(200);

        return response()->json([
            'message' => ResponseMessages::NOT_FOUND
        ], Response::HTTP_NOT_FOUND);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Helpers::doesItExist(User::class, $id);
        if ($user) {
            if (! Gate::allows('update-user', $id)) {
                return response()->json([
                    'message' => ResponseMessages::FORBIDDEN
                ], Response::HTTP_FORBIDDEN);
            }

            $request['mobile_number'] = Helpers::normalizeMobileNumber($request->mobile_number);
            $fields = $request->validate([
                'first_name' => 'required|alpha|max:15',
                'last_name' => 'required|alpha|max:15',
                'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
                'username' => ['required','min:5','max:15', new UsernameRule,  Rule::unique('users')->ignore($id)],
                'password' => ['required', 'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()->uncompromised()],
                'birthday' => 'required|date',
                'sex' => ['required', Rule::in(['M', 'F'])],
                'mobile_number' => ['required', new MobileNumberRule, Rule::unique('users')->ignore($id)],
                'avatar' => 'image|mimes:jpg,jpeg,png',
                'role_id' => Rule::in([1, 2, 3, 4]),
                'country_id' => ['required', new CountryRule()]
            ]);

            $fields['password'] = bcrypt($fields['password']);
            if($request->hasFile('avatar')){
                $user = User::find($id);
                $oldAvatar = $user->avatar;
                $file = $request->file('avatar');
                $folderName = 'avatars';
                $fileNameToStore = Helpers::uploadImage($file, $folderName);

                if ($user->avatar != 'no-image.png') {
                    Storage::delete('public/avatars/'. $oldAvatar);
                }

                $fields['avatar'] = $fileNameToStore;
            }
            $user->update($fields);

            return (new UserResource($user))->response()->setStatusCode(200);
        }

        return response()->json([
            'message' => ResponseMessages::NOT_FOUND
        ], Response::HTTP_NOT_FOUND);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = Helpers::doesItExist(User::class, $id);
        if (isset($user)) {
            $fileName = User::find($id)->avatar;
            if ($fileName != 'no-image.png') {
                Storage::delete('public/avatars/'.$fileName);
            }
            User::destroy($id);
            return response()->json([
                'message' => ResponseMessages::SUCCESSFULLY_DELETED
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => ResponseMessages::FORBIDDEN
        ], Response::HTTP_FORBIDDEN);


    }

    public function sendSmsNotification()
    {
        $basic  = new \Nexmo\Client\Credentials\Basic('38abdc95', 'g9NpuespT3ztnYZv');
        $client = new \Nexmo\Client($basic);

        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS('21655570693', 'TemplateAPI', 'Test of sending a message through my Api.')
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
            return response()->json(['message' => ResponseMessages::SUCCESSFULLY_SENT], 200);
        } else {
            return response()->json(['message' => ResponseMessages::SENT_FAILED, $message->getStatus()]);
        }
    }

}

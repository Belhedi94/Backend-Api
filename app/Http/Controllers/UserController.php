<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('get-users')) {
            return response()->json([
                'message' => 'You don\'t have permission to access this resource'
            ], 403);
        }
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
            'message' => 'Page not Found!'
        ], 404);
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
                    'message' => 'You are not authorized to do this action.'
                ], 403);
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
            'message' => 'Page not Found!'
        ], 404);


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
            if (! Gate::allows('delete-user')) {
                return response()->json([
                    'message' => 'You don\'t have permission to access this resource'
                ], 403);
            }
            $fileName = User::find($id)->avatar;
            if ($fileName != 'no-image.png') {
                Storage::delete('public/avatars/'.$fileName);
            }
            User::destroy($id);
            return response()->json([
                'message' =>'User deleted successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Page not Found!'
        ], 404);

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
            return response()->json(['message' => 'The message was sent successfully'], 200);
        } else {
            return response()->json(['message' => 'The message failed with status:'.$message->getStatus()]);
        }
    }

}

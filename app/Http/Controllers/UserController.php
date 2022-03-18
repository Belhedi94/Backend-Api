<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use App\Rules\Username;

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
                'code' => 403,
                'message' => 'You don\'t have permission to access this resource'
            ]);
        }
        return UserResource::collection(User::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->doesUserExist($id);
        if (gettype($result) == 'boolean') {
            return new UserResource(User::findOrfail($id));
        }

        return $result;

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
        $result = $this->doesUserExist($id);
        if (gettype($result) == 'boolean') {
            if (! Gate::allows('update-user', $id)) {
                return response()->json([
                    'code' => 403,
                    'message' => 'You are not authorized to do this action.'
                ]);
            }
            $fields = $request->validate([
                'first_name' => 'required|alpha|max:15',
                'last_name' => 'required|alpha|max:15',
                'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
                'password' => ['required', 'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()->uncompromised()],
                'username' => ['required','min:5','max:15', new Username,  Rule::unique('users')->ignore($id)],
                'photo' => 'image|mimes:jpg,jpeg,png',
                'sexe' => ['required', Rule::in(['M', 'F'])],
                'phone' => 'required|numeric',
                'birthdate' => 'required|date',
                'role_id' => Rule::in([1, 2, 3, 4])
            ]);

            $fields['password'] = bcrypt($fields['password']);

            $user = User::find($id);
            $oldPhoto = $user->photo;
            $user->update($fields);

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

                if ($user->photo != 'no-image.png') {
                    Storage::delete('public/photos/'. $oldPhoto);
                }

                $user->update(['photo' => $fileNameToStore]);

            }

            return new UserResource($user);
        } else {
            return $result;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (! Gate::allows('delete-user')) {
            return response()->json([
                'code' => 403,
                'message' => 'You don\'t have permission to access this resource'
            ]);
        }
        $result = $this->doesUserExist($id);
        if (gettype($result) == 'boolean') {
            $fileName = User::find($id)->photo;
            if ($fileName != 'no-image.png') {
                Storage::delete('public/photos/'.$fileName);
            }
            User::destroy($id);
            return response()->json([
                'code' => 200,
                'message' =>'User deleted successfully'
            ]);
        }

        return $result;

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
            return response()->json(['message' => 'The message was sent successfully']);
        } else {
            return response()->json(['message' => 'The message failed with status:'.$message->getStatus()]);
        }
    }

    public function banUser($id) {
        if (! Gate::allows('ban-user')) {
            return response()->json([
                'code' => 403,
                'message' => 'You don\'t have permission to access this resource'
            ]);
        }
        $result = $this->doesUserExist($id);
        if (gettype($result) == 'boolean') {
            $user = User::findOrFail($id);
            $user->update([
                'is_banned' => 1
            ]);

            return response()->json([
                'code' => 200,
                'message' => $user->username.' is successfully banned'
            ]);
        }

        return $result;

    }

    public function doesUserExist($id) {

        $user = User::find($id);
        if(!isset($user)) {
            return response()->json([
                'code' =>404,
                'message' => 'Not Found'
            ]);
        }
        else
            return true;
    }
}

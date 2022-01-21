<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
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
        return new UserResource(User::find($id));
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
            'phone' => 'required|numeric',
            'birthdate' => 'required|date',
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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
}

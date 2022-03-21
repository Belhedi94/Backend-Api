<?php
/**
 * Created by PhpStorm.
 * User: rafaa
 * Date: 20/03/2022
 * Time: 10:38
 */

namespace App\Http;
use App\Models\User;
use Illuminate\Support\Facades\Gate;


class Helpers
{
    public static function normalizeMobileNumber($mobileNumber) {
        return str_replace([' ', '.', '-', '(', ')', '+'], '', $mobileNumber);
    }

    public static function doesUserExist($userID) {

        $user = User::find($userID);
        if(!isset($user)) {
            return response()->json([
                'message' => 'Page not Found'
            ], 404);
        }
        else
            return true;
    }

}
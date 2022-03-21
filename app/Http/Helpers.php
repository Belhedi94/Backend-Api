<?php
/**
 * Created by PhpStorm.
 * User: rafaa
 * Date: 20/03/2022
 * Time: 10:38
 */

namespace App\Http;


class Helpers
{
    public static function normalizeMobileNumber($mobileNumber) {
        return str_replace([' ', '.', '-', '(', ')', '+'], '', $mobileNumber);
    }

}
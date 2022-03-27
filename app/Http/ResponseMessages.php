<?php
/**
 * Created by PhpStorm.
 * User: rafaa
 * Date: 27/03/2022
 * Time: 09:25
 */

namespace App\Http;


class ResponseMessages
{
    public const FORBIDDEN = 'You don\'t have permission to access this resource.';
    public const OK_BANNED = 'The account is successfully banned.';
    public const ALREADY_VERIFIED = 'Email already verified.';
    public const SUCCESSFULLY_VERIFIED = 'Email successfully verified.';
    public const BAD_REQUEST = 'The provided credentials do not match our records.';
    public const LOGGED_OUT = 'Successfully logged out.';
    public const NOT_FOUND = 'Page not found.';
    public const SUCCESSFULLY_CREATED = 'Successfully created.';
    public const SUCCESSFULLY_DELETED = 'Successfully deleted.';
    public const SUCCESSFULLY_SENT = 'Successfully sent.';
    public const SENT_FAILED = 'Sent failed.';
    public const BANNED = 'Unauthorized, your account is banned.';

}
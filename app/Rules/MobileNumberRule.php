<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNumberRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $minDigits = 9;
        $maxDigits = 14;

        return (preg_match('/^[0-9]{'.$minDigits.','.$maxDigits.'}\z/', $value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is invalid.';
    }
}

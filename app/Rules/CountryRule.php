<?php

namespace App\Rules;
use App\Models\Country;

use Illuminate\Contracts\Validation\Rule;

class CountryRule implements Rule
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
        $countries = array();
        for ($i = 0; $i < 249; $i++) {
            $countries[$i] = ++$i;
        }
        return (in_array($value, $countries));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The country attribute is invalid.';
    }
}

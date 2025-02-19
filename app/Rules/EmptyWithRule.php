<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

class EmptyWithRule implements Rule
{
    public function passes($attribute, $value, $parameters = null, Validator $validator = null)
    {
        // code that would validate
        // attribute its the field under validation
        // values its the value of the field
        // parameters its the value that it will validate againts
        $parameterValue = Arr::get($validator->getData(), $parameters[0]);

        if (!is_null($value) && !is_null($parameterValue)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
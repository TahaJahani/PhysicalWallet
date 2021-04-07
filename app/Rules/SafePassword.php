<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SafePassword implements Rule
{

    private string $message = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strlen($value) < 8) {
            $this->message = 'the :attribute must be at least 8 characters';
            return false;
        }
        if (!preg_match('/.*[a-zA-Z]+.*/', $value)) {
            $this->message = 'the :attribute must contain at least one character';
            return false;
        }
        if (!preg_match('/.*[0-9].*/', $value)) {
            $this->message = 'the :attribute must contain at least one digit';
            return false;
        }
        $specialCharacters = ['!', '@', '&', '$', '#'];
        $foundCharacters = array_intersect($specialCharacters, str_split($value));
        if (empty($foundCharacters)){
            $this->message = 'the :attribute must contain at least one the !,@,&,$,# characters';
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
        return $this->message;
    }
}

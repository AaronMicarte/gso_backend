<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GmailOnly implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value && !preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $value)) {
            $fail('The ' . $attribute . ' field must be a valid Gmail address.');
        }
    }
}

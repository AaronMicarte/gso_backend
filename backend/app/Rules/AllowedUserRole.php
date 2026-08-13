<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Auth\UserRole;

class AllowedUserRole implements ValidationRule
{
    private array $allowedRoles = ['Admin', 'GSO Staff', 'Department Head'];

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
        $userRole = UserRole::find($value);

        if (!$userRole || !in_array($userRole->RoleName, $this->allowedRoles)) {
            $fail('The ' . $attribute . ' must be one of: ' . implode(', ', $this->allowedRoles) . '.');
        }
    }
}

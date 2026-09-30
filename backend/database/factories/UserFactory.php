<?php

namespace Database\Factories;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'Username'        => $this->faker->unique()->userName(),
            'Email'           => $this->faker->unique()->safeEmail(),
            'Password'        => bcrypt('Secret@123'),
            'IsActive'        => 1,
            'EmailVerifiedAt' => now(),
            'MFAEnabled'      => 0,
            'UserRoleID'      => 1,
            'DepartmentID'    => 1,
            'MustChangePassword' => 0,
        ];
    }
}
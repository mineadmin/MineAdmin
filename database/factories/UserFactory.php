<?php

namespace Database\Factories;

use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'password' => 'password',
            'user_type' => Type::System,
            'nickname' => fake()->name(),
            'phone' => fake()->numerify('1##########'),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => '',
            'signed' => '',
            'status' => Status::Normal,
            'login_ip' => fake()->ipv4(),
            'login_time' => now(),
            'backend_setting' => null,
            'created_by' => 0,
            'updated_by' => 0,
            'remark' => '',
        ];
    }
}

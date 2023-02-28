<?php

namespace Iotronlab\FilamentMultiGuard\Tests\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Iotronlab\FilamentMultiGuard\Tests\app\Models\User;


class UserFactory extends Factory
{

    protected $model = User::class;
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$uw8z.CGmMsblLoiFlhH9NOw6w.E0rS7WHEMOcw448m4kddUlqLAUa', // password
            'remember_token' => Str::random(10),
        ];
    }




}

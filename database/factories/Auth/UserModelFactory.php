<?php

namespace Database\Factories\Auth;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserModelFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_number' => fake()->phoneNumber(),
            'phone_verified_at' => now(),
            'address' => fake()->address(),
            'ktp_number' => fake()->randomNumber(10),
            'ktp_name' => fake()->name(),
            'ktp_address' => fake()->address(),
            'ktp_image' => fake()->image(),
            'ktp_verified' => false,
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'date_of_birth' => fake()->date(),
            'profile_photo_path' => fake()->image(),
            'profile_completed' => false,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function phoneNumber(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_number' => fake()->phoneNumber(),
        ]);
    }

    public function phoneUnverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => null,
        ]);
    }

    public function emailUnverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

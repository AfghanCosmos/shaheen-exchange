<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
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
            'uuid' => Str::uuid(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->unique()->phoneNumber,
            'password' => Hash::make('password'), // Default password
            'image' => $this->faker->imageUrl(100, 100),
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'facebook_id' => Str::random(10),
            'google_id' => Str::random(10),
            'pin_code' => rand(1000, 9999),
            'referral_code' => Str::random(8),
            'referred_by' => null,
            'status' => 'active',
            'commission_percentage' => $this->faker->randomFloat(2, 0, 10),
            'user_type' => $this->faker->randomElement(['admin', 'customer', 'vendor', 'agent']),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

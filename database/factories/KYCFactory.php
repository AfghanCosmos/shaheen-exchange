<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KYC>
 */
class KYCFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'govt_id_type' => $this->faker->randomElement(['National ID', 'Driver’s License', 'Passport']),
            'govt_id_number' => strtoupper($this->faker->bothify('##??####')),
            'govt_id_file' => 'uploads/kyc/' . $this->faker->uuid . '.jpg',
            'issue_date' => $this->faker->date(),
            'expire_date' => $this->faker->optional()->date(),
            'status' => $this->faker->randomElement(['pending', 'verified', 'rejected']),
            'rejection_reason' => $this->faker->optional()->sentence(),
            'third_party_response' => json_encode([
                'service' => 'KYC_VerifyCo',
                'status' => $this->faker->randomElement(['verified', 'rejected']),
                'verified_at' => now()->toDateTimeString(),
            ]),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Currency;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
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
            'bank_name' => $this->faker->company,
            'account_holder_name' => $this->faker->name,
            'account_number' => $this->faker->bankAccountNumber,
            'iban' => $this->faker->optional()->iban,
            'swift_code' => $this->faker->optional()->swiftBicNumber,
            'currency_id' => Currency::inRandomOrder()->value('id'),
            'is_primary' => $this->faker->boolean(30), // 30% chance it's primary
            'status' => $this->faker->randomElement(['active', 'inactive', 'closed']),
        ];
    }
}

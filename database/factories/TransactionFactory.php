<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Currency;
use App\Models\Wallet;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\BankAccount;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'wallet_id' => Wallet::inRandomOrder()->value('id') ?? Wallet::factory()->create()->id,
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency_id' => Currency::inRandomOrder()->value('id') ?? Currency::factory()->create()->id,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'payment_gateway' => $this->faker->randomElement(['Stripe', 'PayPal', 'Crypto']),
            'reference_id' => Str::random(10),
            'source' => $this->faker->randomElement(['manual', 'card', 'bank', 'crypto', 'referral']),
            'referral_id' => $this->faker->optional()->randomNumber(),
            'bank_account_id' => BankAccount::inRandomOrder()->value('id') ?? BankAccount::factory()->create()->id,
        ];
    }
}

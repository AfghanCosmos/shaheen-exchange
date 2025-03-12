<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Currency;
use App\Models\Wallet;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderWallet = Wallet::inRandomOrder()->value('id') ?? Wallet::factory()->create()->id;
        $receiverWallet = Wallet::where('id', '!=', $senderWallet)->inRandomOrder()->value('id')
                            ?? Wallet::factory()->create()->id;

        return [
            'uuid' => Str::uuid(),
            'sender_wallet_id' => $senderWallet,
            'receiver_wallet_id' => $receiverWallet,
            'amount' => $this->faker->randomFloat(2, 10, 2000),
            'fee' => $this->faker->randomFloat(2, 1, 50),
            'currency_id' => Currency::inRandomOrder()->value('id') ?? Currency::factory()->create()->id,
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}

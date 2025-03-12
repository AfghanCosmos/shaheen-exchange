<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OfflineTransfer;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfflineTransfer>
 */
class OfflineTransferFactory extends Factory
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
            'sender_wallet_id' => Wallet::inRandomOrder()->value('id') ?? Wallet::factory()->create()->id,
            'receiver_name' => $this->faker->name,
            'receiver_email' => $this->faker->optional()->email,
            'receiver_phone_number' => $this->faker->optional()->phoneNumber,
            'receiver_address' => $this->faker->optional()->address,
            'amount' => $this->faker->randomFloat(2, 10, 5000),
            'currency_id' => Currency::inRandomOrder()->value('id') ?? Currency::factory()->create()->id,
            'status' => $this->faker->randomElement([
                'pending',
                'waiting_for_receiver',
                'store_requested',
                'admin_approved',
                'completed',
                'failed'
            ]),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Store;
use App\Models\OfflineTransfer;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WithdrawalRequest>
 */
class WithdrawalRequestFactory extends Factory
{

    protected $model = WithdrawalRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'uuid' => Str::uuid(),
            'store_id' => Store::inRandomOrder()->value('id'),
            'offline_transfer_id' => OfflineTransfer::inRandomOrder()->value('id'),
            'receiver_wallet_id' => Wallet::inRandomOrder()->value('id'),
            'receiver_name' => $this->faker->name,
            'receiver_verification_id' => $this->faker->randomNumber(8),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'currency_id' => Currency::inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed', 'failed']),
            'verified_by_store' => $this->faker->boolean(30),
            'admin_id' => User::inRandomOrder()->value('id'),
            'commission_amount' => $this->faker->randomFloat(2, 5, 500)
        ];
    }
}

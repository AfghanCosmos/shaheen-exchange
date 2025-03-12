<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FundRequest;
use App\Models\OfflineTransfer;
use App\Models\Store;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FundRequest>
 */
class FundRequestFactory extends Factory
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
            'offline_transfer_id' => OfflineTransfer::inRandomOrder()->value('id') ?? OfflineTransfer::factory()->create()->id,
            'store_id' => Store::inRandomOrder()->value('id') ?? Store::factory()->create()->id,
            'amount_requested' => $this->faker->randomFloat(2, 100, 5000),
            'currency_id' => Currency::inRandomOrder()->value('id') ?? Currency::factory()->create()->id,
            'status' => $this->faker->randomElement(['pending_admin_approval', 'approved', 'rejected']),
            'admin_id' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'approved_at' => $this->faker->optional()->dateTime(),
        ];
    }
}

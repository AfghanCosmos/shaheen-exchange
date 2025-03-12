<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Currency;
use App\Models\Store;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ownerType = $this->faker->randomElement([User::class, Store::class]);
        $ownerId = $ownerType::inRandomOrder()->value('id') ?? $ownerType::factory()->create()->id;

        return [
            'uuid' => Str::uuid(),
            'owner_id' => $ownerId,
            'owner_type' => $ownerType,
            'currency_id' => Currency::inRandomOrder()->value('id') ?? Currency::factory()->create()->id,
            'balance' => $this->faker->randomFloat(2, 0, 5000),
            'status' => $this->faker->randomElement(['active', 'suspended', 'closed']),
        ];

    }
}

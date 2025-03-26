<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Province;
use App\Models\Country;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
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
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory()->create()->id,
            'province_id' => Province::inRandomOrder()->value('id') ?? 1,
            'country_id' => Country::inRandomOrder()->value('id') ?? 1,
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'latitude' => $this->faker->optional()->latitude(-90, 90),
            'longitude' => $this->faker->optional()->longitude(-180, 180),
            'open_time' => $this->faker->time('H:i:s'),
            'close_time' => $this->faker->time('H:i:s'),
            'is_closed' => $this->faker->boolean(20),
            'status' => $this->faker->randomElement(['active', 'inactive', 'closed']),
        ];
    }
}

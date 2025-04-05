<?php

namespace Database\Factories;

use App\Models\Hawla;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HawlaFactory extends Factory
{
    protected $model = Hawla::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'hawla_type_id' => rand(1, 3), // make sure these IDs exist
            'sender_name' => $this->faker->name,
            'sender_phone' => $this->faker->phoneNumber,
            'receiver_name' => $this->faker->name,
            'receiver_father' => $this->faker->name,
            'sender_store_id' => rand(1, 5), // update according to your existing store data
            'given_amount' => $this->faker->randomFloat(2, 100, 10000),
            'given_amount_currency_id' => rand(1, 3),
            'receiving_amount_currency_id' => rand(1, 3),
            'receiving_amount' => $this->faker->randomFloat(2, 100, 10000),
            'exchange_rate' => $this->faker->randomFloat(2, 70, 100),
            'commission' => $this->faker->randomFloat(2, 10, 200),
            'commission_taken_by' => $this->faker->randomElement(['sender_store', 'receiver_store']),
            'receiver_phone_number' => $this->faker->phoneNumber,
            'receiver_address' => $this->faker->address,
            'receiver_store_id' => rand(1, 5),
            'note' => $this->faker->sentence,
            'created_by' => 1, // you can randomize this or use an existing user
            'receiver_verification_document' => null,
            'status' => rand(1, 4),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

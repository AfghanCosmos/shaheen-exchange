<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('currencies')->insert([
            ['code' => 'AFN', 'name' => 'Afghani'],
            ['code' => 'USD', 'name' => 'US Dollar'],
        ]);

        DB::table('provinces')->insert([
            ['name' => 'Ontario', 'country_id' =>1 ],
            ['name' => 'Quebec', 'country_id' =>1 ],
            ['name' => 'British Columbia', 'country_id' =>1 ],
            ['name' => 'Alberta', 'country_id' =>1 ],
        ]);

        // User::factory()->create([
        //     'uuid' => '0011',
        //     'name' => 'khan',
        //     'email' => 'khan@gmail.com',
        //     'phone_number' => '1234567890',
        //     'password' => Hash::make('password'),
        //  ]);

        User::factory(20)->create();




    }
}

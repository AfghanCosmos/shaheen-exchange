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

        // DB::table('provinces')->insert([
        //     ['name' => 'Ontario'],
        //     ['name' => 'Quebec'],
        //     ['name' => 'British Columbia'],
        //     ['name' => 'Alberta'],
        // ]);

        // User::factory()->create([
        //     'uuid' => '001',
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'phone_number' => '1234567890',
        //     'password' => Hash::make('password'),
        // ]);

        // User::factory(20)->create();


    }
}

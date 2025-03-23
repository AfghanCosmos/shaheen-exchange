<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BankAccount;
class BankAccountSeeder extends Seeder
{
    /**
    
     * Run the database seeds.
     */
    public function run(): void
    {
        BankAccount::factory()->count(10)->create();
    }
}

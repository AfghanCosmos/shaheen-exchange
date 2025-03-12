<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KYC;
class KYCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KYC::factory()->count(10)->create();
    }
}

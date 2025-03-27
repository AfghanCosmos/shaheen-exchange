<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hawla;

class HawlaSeeder extends Seeder
{
    public function run(): void
    {
        Hawla::factory()->count(50)->create();
    }
}

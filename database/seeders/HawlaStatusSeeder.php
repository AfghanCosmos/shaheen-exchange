<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HawlaStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hawla_statuses')->insert([
            ['name' => 'Pending'],
            ['name' => 'Approved'],
            ['name' => 'Rejected'],
            ['name' => 'Completed'],
        ]);
    }
}

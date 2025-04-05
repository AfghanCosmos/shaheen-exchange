<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HawlaTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hawla_types')->insert([
            ['name' => 'Personal'],
            ['name' => 'Business'],
            ['name' => 'Loan'],
        ]);
    }
}

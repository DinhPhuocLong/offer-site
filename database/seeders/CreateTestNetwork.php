<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateTestNetwork extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('networks')->insert([
            'name' => 'TestNetwork',
            'aff_sub' => '{sub_1}',
            'payout' => '{sum}',
            'is_unique_lead' => 1,
            'is_unique_click' => 1,
            'is_hidden' => 0
        ]);
    }
}

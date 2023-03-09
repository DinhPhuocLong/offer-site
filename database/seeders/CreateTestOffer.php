<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateTestOffer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('offers')->insert([
            'name' => 'Test offer',
            'network_id' => 1,
            'offer_type' => 1,
            'offer_link' => 'https://google.com/',
            'country_allowed' => 'VN, DE, GB',
            'offer_payout' => 1,
            'is_hidden' => 1
        ]);
    }
}

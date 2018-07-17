<?php

use Illuminate\Database\Seeder;

class HowAcquiredOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $acquiredOptionData = [
            [
                'how_acquired_option_name' => 'Fannie Mae'
            ],
            [
                'how_acquired_option_name' => 'Forfeited Land Sale'
            ],
            [
                'how_acquired_option_name' => 'Freddy'
            ],
            [
                'how_acquired_option_name' => 'HUD'
            ],
            [
                'how_acquired_option_name' => 'Other'
            ],
            [
                'how_acquired_option_name' => 'Private/Other Donation'
            ],
            [
                'how_acquired_option_name' => 'Quit Claim'
            ],
            [
                'how_acquired_option_name' => 'Sheriff Transfer'
            ],
            [
                'how_acquired_option_name' => 'Tax Foreclosure'
            ],
            [
                'how_acquired_option_name' => 'Undefined'
            ]
        ];

        DB::table('how_acquired_options')->insert($acquiredOptionData);
    }
}

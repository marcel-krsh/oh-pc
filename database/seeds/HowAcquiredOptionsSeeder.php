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
        $acquiredOptionData = array(
            array(
                'how_acquired_option_name' => 'Fannie Mae'
            ),
            array(
                'how_acquired_option_name' => 'Forfeited Land Sale'
            ),
            array(
                'how_acquired_option_name' => 'Freddy'
            ),
            array(
                'how_acquired_option_name' => 'HUD'
            ),
            array(
                'how_acquired_option_name' => 'Other'
            ),
            array(
                'how_acquired_option_name' => 'Private/Other Donation'
            ),
            array(
                'how_acquired_option_name' => 'Quit Claim'
            ),
            array(
                'how_acquired_option_name' => 'Sheriff Transfer'
            ),
            array(
                'how_acquired_option_name' => 'Tax Foreclosure'
            ),
            array(
                'how_acquired_option_name' => 'Undefined'
            )
        );

        DB::table('how_acquired_options')->insert($acquiredOptionData);
    }
}

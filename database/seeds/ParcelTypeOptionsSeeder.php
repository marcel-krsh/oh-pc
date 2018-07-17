<?php

use Illuminate\Database\Seeder;

class ParcelTypeOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PUT IN PARCEL SUPPORTING DROP DOWN DATA
        $parcelTypeOptionsData = [
            [
                'parcel_type_option_name'=>'1-4 Units',
                'active'=> 1
            ],
            [
                'parcel_type_option_name'=>'5-8 Units',
                'active'=> 1
            ],
            [
                'parcel_type_option_name'=>'9-12 Units',
                'active'=> 1
            ],
            [
                'parcel_type_option_name'=>'13-16 Units',
                'active'=> 1

            ],
            [
                'parcel_type_option_name'=>'17+ Units',
                'active'=> 1
            ]
        ];

        DB::table('parcel_type_options')->insert($parcelTypeOptionsData);
    }
}

<?php

use Illuminate\Database\Seeder;

class DispositionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispositionTypeData = array(
            array(
                'disposition_type_name'=>'Bus/Res Dev',
                'active'=> 1
            ),
            array(
                'disposition_type_name'=>'Non-Profit',
                'active'=> 1
            ),
            array(
                'disposition_type_name'=>'Other',
                'active'=> 1
            ),
            array(
                'disposition_type_name'=>'Public Use',
                'active'=> 1
            ),
            array(
                'disposition_type_name'=>'Side Lot',
                'active'=> 1
            )
        );

        \Illuminate\Support\Facades\DB::table('disposition_types')->insert($dispositionTypeData);
    }
}

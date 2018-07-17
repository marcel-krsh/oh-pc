<?php

use Illuminate\Database\Seeder;

class AccountTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accountTypeData = [
            [
                'type'=>'Grant',
                'active'=> 1
            ],
            [
                'type'=>'Award',
                'active'=> 1
            ],
            [
                'type'=>'Line of Credit',
                'active'=> 1
            ]
        ];

        \Illuminate\Support\Facades\DB::table('account_types')->insert($accountTypeData);
    }
}

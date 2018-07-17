<?php

use Illuminate\Database\Seeder;

class TransactionStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactionStatusData = [
            [
                'status_name'=>'Pending',
                'active'=> 1
            ],
            [
                'status_name'=>'Cleared',
                'active'=> 1
            ],
            [
                'status_name'=>'Insufficient',
                'active'=> 1
            ]
        ];

        \Illuminate\Support\Facades\DB::table('transaction_statuses')->insert($transactionStatusData);
    }
}

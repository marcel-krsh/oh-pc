<?php

use Illuminate\Database\Seeder;

class TransactionCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Categories describe the transaction
        $transactionCategoriesData = [
            [
                'category_name'=>'Deposit',
                'active'=>1
            ],
            [
                'category_name'=>'Recapture',
                'active'=>1
            ],
            [
                'category_name'=>'Reimbursement',
                'active'=>1
            ],
            [
                'category_name'=>'Transfer',
                'active'=>1
            ],
            [
                'category_name'=>'Line of Credit',
                'active'=>1
            ],
            [
                'category_name'=>'Disposition Recapture',
                'active'=>1
            ]

        ];

        \Illuminate\Support\Facades\DB::table('transaction_categories')->insert($transactionCategoriesData);
    }
}

<?php

use Illuminate\Database\Seeder;

class TransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Types link to the the cause/source of the transaction
        $transactionTypesData = array(
            array(
                'type_name'=>'Reimbursement Invoice',
                'active'=>1
            ),
            array(
                'type_name'=>'Disposition Invoice',
                'active'=>1
            ),
            array(
                'type_name'=>'Deposit',
                'active'=>1
            ),
            array(
                'type_name'=>'Line of Credit',
                'active'=>1
            ),
            array(
                'type_name'=>'Transfer',
                'active'=>1
            ),
            array(
                'type_name'=>'Recapture Invoice',
                'active'=>1
            )
        );

        \Illuminate\Support\Facades\DB::table('transaction_types')->insert($transactionTypesData);
    }
}

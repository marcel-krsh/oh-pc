<?php

use Illuminate\Database\Seeder;

class InvoiceStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invoiceStatusData = array(
            array(
                'invoice_status_name'=>'Draft'
            ),
            array(
                'invoice_status_name'=>'Pending Land Bank Approval'
            ),
            array(
                'invoice_status_name'=>'Pending HFA Approval'
            ),
            array(
                'invoice_status_name'=>'Pending Payment'
            ),
            array(
                'invoice_status_name'=>'Declined'
            ),
            array(
                'invoice_status_name'=>'Paid'
            )
        );
        \Illuminate\Support\Facades\DB::table('invoice_statuses')->insert($invoiceStatusData);
    }
}

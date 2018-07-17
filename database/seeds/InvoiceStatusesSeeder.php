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
        $invoiceStatusData = [
            [
                'invoice_status_name'=>'Draft'
            ],
            [
                'invoice_status_name'=>'Pending Land Bank Approval'
            ],
            [
                'invoice_status_name'=>'Pending HFA Approval'
            ],
            [
                'invoice_status_name'=>'Pending Payment'
            ],
            [
                'invoice_status_name'=>'Declined'
            ],
            [
                'invoice_status_name'=>'Paid'
            ]
        ];
        \Illuminate\Support\Facades\DB::table('invoice_statuses')->insert($invoiceStatusData);
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoiceStatusFiscalAgent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('invoice_statuses')->truncate();
        
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
            ],
            [
            'invoice_status_name'=>'Approved'
            ],
            [
            'invoice_status_name'=>'Submitted to Fiscal Agent'
            ]
        ];
        DB::table('invoice_statuses')->insert($invoiceStatusData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

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
            ),
            array(
            'invoice_status_name'=>'Approved'
            ),
            array(
            'invoice_status_name'=>'Submitted to Fiscal Agent'
            )
        );
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

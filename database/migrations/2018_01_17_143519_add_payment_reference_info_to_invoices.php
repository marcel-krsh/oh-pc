<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentReferenceInfoToInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('reimbursement_invoices', function (Blueprint $table) {
            $table->float('reimbursement_total_amount')->nullable();
            $table->float('reimbursement_total_paid')->nullable();
            $table->float('reimbursement_balance')->nullable();
            $table->date('reimbursement_last_payment_cleared_date')->nullable();
        });
        Schema::table('disposition_invoices', function (Blueprint $table) {
            $table->float('disposition_total_amount')->nullable();
            $table->float('disposition_total_paid')->nullable();
            $table->float('disposition_balance')->nullable();
            $table->date('disposition_last_payment_cleared_date')->nullable();
        });
        Schema::table('recapture_invoices', function (Blueprint $table) {
            $table->float('recapture_total_amount')->nullable();
            $table->float('recapture_total_paid')->nullable();
            $table->float('recapture_balance')->nullable();
            $table->date('recapture_last_payment_cleared_date')->nullable();
        });
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

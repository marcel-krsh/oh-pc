<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sf_batch_id')->nullable();
            $table->integer('entity_id');
            $table->integer('program_id');
            $table->integer('account_id');
            $table->integer('status_id')->default('1');
            $table->integer('po_id');
            $table->tinyInteger('active');
            $table->timestamps();
            $table->index('entity_id');
            $table->index('program_id');
            $table->index('account_id');
            $table->index('status_id');
            $table->index('po_id');
        });

        Schema::create('parcels_to_reimbursement_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parcel_id')->nullable();
            $table->integer('reimbursement_invoice_id');
            $table->index('parcel_id');
            $table->index('reimbursement_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reimbursement_invoices');
        Schema::dropIfExists('parcels_to_reimbursement_invoices');
    }
}

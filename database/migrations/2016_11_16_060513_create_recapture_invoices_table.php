<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecaptureInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recapture_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id');
            $table->integer('program_id');
            $table->integer('account_id');
            $table->integer('status_id')->default(1);
            $table->time('recapture_due_date')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
            $table->index('entity_id');
            $table->index('program_id');
            $table->index('account_id');
            $table->index('status_id');
        });

        Schema::create('parcels_to_recapture_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parcel_id')->nullable();
            $table->integer('recapture_invoice_id');
            $table->index('parcel_id');
            $table->index('recapture_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recapture_invoices');
        Schema::dropIfExists('parcels_to_recapture_invoices');
    }
}

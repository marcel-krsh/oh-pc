<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispositionInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('disposition_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id');
            $table->integer('program_id');
            $table->integer('account_id');
            $table->integer('status_id')->default(1);
            $table->tinyInteger('active')->default(1);
            $table->string('disposition_invoice_due')->nullable();
            $table->timestamps();
            $table->index('entity_id');
            $table->index('program_id');
            $table->index('account_id');
            $table->index('status_id');
        });

            Schema::create('dispositions_to_invoices', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('disposition_id')->nullable();
                $table->integer('disposition_invoice_id');
                $table->index('disposition_id');
                $table->index('disposition_invoice_id');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disposition_invoices');
        Schema::dropIfExists('dispositions_to_invoices');
    }
}

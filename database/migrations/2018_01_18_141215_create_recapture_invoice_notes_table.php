<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecaptureInvoiceNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recapture_invoice_notes', function (Blueprint $table) {
           
            $table->increments('id');
            $table->timestamps();
            $table->integer('recapture_invoice_id')->unsigned()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type')->default('user');
            $table->text('note')->nullable();
            $table->foreign('recapture_invoice_id')->references('id')->on('recapture_invoices');
            $table->foreign('owner_id')->references('id')->on('users');    
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recapture_invoice_notes');
    }
}

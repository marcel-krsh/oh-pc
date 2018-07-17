<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispositionInvoiceNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('disposition_invoice_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('disposition_invoice_id')->unsigned()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type')->default('user');
            $table->text('note')->nullable();
            $table->foreign('disposition_invoice_id')->references('id')->on('disposition_invoices');
            $table->foreign('owner_id')->references('id')->on('users');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('disposition_invoice_notes');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('po_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('purchase_order_id')->unsigned()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type')->default('user');
            $table->text('note')->nullable();
            $table->foreign('purchase_order_id')->references('id')->on('reimbursement_purchase_orders');
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
        Schema::dropIfExists('po_notes');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

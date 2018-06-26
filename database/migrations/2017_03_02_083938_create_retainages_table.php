<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetainagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retainages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id');
            $table->integer('expense_category_id');
            $table->integer('parcel_id');
            $table->integer('cost_item_id');
            $table->float('retainage_amount',10,2);
            $table->boolean('paid');
            $table->dateTime('date_paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retainages');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakout_type')->default(1);
            $table->integer('po_id');
            $table->integer('parcel_id');
            $table->integer('program_id');
            $table->integer('entity_id');
            $table->integer('account_id');
            $table->integer('expense_category_id')->default(1);
            $table->float('amount', 10, 2)->nullable();
            $table->integer('vendor_id')->default(1);
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->integer('ref_id')->nullable();
            $table->integer('breakout_item_status_id')->default(1);
            $table->timestamps();
            $table->index('breakout_type');
            $table->index('po_id');
            $table->index('parcel_id');
            $table->index('program_id');
            $table->index('entity_id');
            $table->index('account_id');
            $table->index('expense_category_id');
            $table->index('vendor_id');
            $table->index('breakout_item_status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_items');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VendorsToEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('vendors_to_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id')->unsigned()->nullable();
            $table->foreign('vendor_id')
                    ->references('id')
                    ->on('vendors')
                    ->onDelete('cascade');
            $table->integer('entity_id')->unsigned()->nullable();
            $table->foreign('entity_id')
                    ->references('id')
                    ->on('entities')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('vendors_to_entities');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

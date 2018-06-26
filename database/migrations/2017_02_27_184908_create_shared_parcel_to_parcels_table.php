<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharedParcelToParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('shared_parcel', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('program_id')->references('id')->on('programs');
            $table->timestamps();
        });
        Schema::create('shared_parcel_to_parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('shared_parcel_id')->references('id')->on('shared_parcels');
            $table->string('reference_letter')->default('NA');
            $table->integer('parcel_id')->references('id')->on('parcels');
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
        Schema::dropIfExists('shared_parcel');
        Schema::dropIfExists('shared_parcel_to_parcels');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdoParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sdo_parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('File Number');
            $table->string('Property Address Number')->nullable();
            $table->string('Property Address Street Name')->nullable();
            $table->string('Property Address Street Suffix')->nullable();
            $table->string('Property City')->nullable();
            $table->string('Property State')->nullable();
            $table->string('Property Zip')->nullable();
            $table->string('Property County')->nullable();
            $table->string('First Payment Date')->nullable();
            $table->float('latitude', 10, 5)->nullable();
            $table->float('longitude', 10, 5)->nullable();
            $table->integer('us_house_district')->nullable();
            $table->integer('oh_house_district')->nullable();
            $table->integer('oh_senate_district')->nullable();
            $table->string('google_map_link')->nullable();
            $table->string('Status')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sdo_parcels');
    }
}

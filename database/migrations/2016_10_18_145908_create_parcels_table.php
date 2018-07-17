<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('state_acronym');
            $table->string('state_name');
        });
        Schema::create('counties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('state_id')->unsigned()->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('SET NULL');
            $table->string('county_name');
        });
        Schema::create('target_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('county_id')->unsigned()->nullable();
            $table->string('target_area_name');
            $table->tinyInteger('active')->default(1);
        });
         Schema::create('how_acquired_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('how_acquired_option_name');
            $table->tinyInteger('active')->default(1);
         });
         
        Schema::create('property_status_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('option_name');
            $table->string('for')->default('hfa');
            $table->integer('order')->default(1);
            $table->tinyInteger('protected')->default(1);
            $table->tinyInteger('active')->default(1);
        });
        Schema::create('parcel_type_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parcel_type_option_name');
            $table->tinyInteger('active')->default(1);
        });

        Schema::create('parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('parcel_id')->nullable();
            $table->integer('program_id')->unsigned()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->integer('entity_id')->unsigned()->nullable();
            $table->integer('account_id')->unsigned()->nullable();
            $table->string('owner_type')->default('program');
            $table->string('street_address');
            $table->string('city');
            $table->integer('state_id')->unsigned()->nullable();
            $table->string('zip');
            $table->integer('county_id')->unsigned()->nullable();
            $table->integer('target_area_id')->unsigned()->default(1);
            $table->string('oh_house_district')->nullable();
            $table->string('oh_senate_district')->nullable();
            $table->string('us_house_district')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('google_map_link')->nullable();
            $table->date('withdrawn_date')->nullable();
            $table->integer('sale_price')->default(0);
            $table->integer('how_acquired_id')->unsigned()->default(10);
            $table->text('how_acquired_explanation')->nullable();
            $table->integer('hfa_property_status_id')->unsigned()->default(39);
            $table->integer('landbank_property_status_id')->unsigned()->default(5);
            $table->text('status_explanation')->nullable();
            $table->boolean('historic_waiver_approved')->default(0);
            $table->boolean('historic_significance_or_district')->default(0);
            $table->boolean('ugly_house')->default(0);
            $table->boolean('pretty_lot')->default(0);
            $table->integer('parcel_type_id')->unsigned()->default(1);
            $table->float('retainage', 10, 2)->nullable();
            $table->boolean('retainage_paid')->default(0);
            $table->timestamps();
            $table->foreign('target_area_id')->references('id')->on('target_areas');
            $table->foreign('parcel_type_id')->references('id')->on('parcel_type_options');
            $table->foreign('how_acquired_id')->references('id')->on('how_acquired_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcels');
        Schema::dropIfExists('parcel_type_options');
        Schema::dropIfExists('property_status_options');
        Schema::dropIfExists('how_acquired_options');
        Schema::dropIfExists('target_areas');
        Schema::dropIfExists('counties');
        Schema::dropIfExists('states');
    }
}

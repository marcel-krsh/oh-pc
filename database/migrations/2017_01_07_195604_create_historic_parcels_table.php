<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoricParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historic_parcels', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('Property Address Number')->nullable();
            $table->string('Property Address Street Name')->nullable();
            $table->string('Property Address Street Suffix')->nullable();
            $table->string('Property City')->nullable();
            $table->string('Property State')->nullable();
            $table->string('Property Zip')->nullable();
            $table->string('Property County')->nullable();
            $table->float('latitude',10,5)->nullable();
            $table->float('longitude',10,5)->nullable();
            $table->integer('entity_id')->default(46);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historic_parcels');
    }
}

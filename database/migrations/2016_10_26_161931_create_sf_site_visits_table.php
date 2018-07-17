<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfSiteVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_site_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->text('VisitDate')->nullable();
            $table->text('CreatedDate')->nullable();
            $table->text('InspectorName')->nullable();
            $table->text('PropertyID')->collate('utf8_bin')->nullable();
            $table->text('Partner')->nullable();
            $table->text('AllStructuresRemoved')->nullable();
            $table->text('ConstructionDebrisRemoved')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_site_visits');
    }
}

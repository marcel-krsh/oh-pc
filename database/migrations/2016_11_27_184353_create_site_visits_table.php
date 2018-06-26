<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->timeStamp('visit_date')->nullable();
            $table->integer('inspector_id')->nullable();
            $table->text('sf_parcel_id');
            $table->integer('parcel_id');
            $table->integer('entity_id');
            $table->tinyInteger('all_structures_removed')->nullable();
            $table->tinyInteger('construction_debris_removed')->nullable();
            $table->timestamps();
            $table->index('inspector_id');
            $table->index('parcel_id');
            $table->index('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_visits');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sync_buildings')) {
            Schema::create('sync_buildings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('allita_id')->nullable();
                $table->integer('building_key')->nullable();
                $table->integer('building_status_key')->nullable();
                $table->string('building_name', 255)->nullable();
                $table->integer('physical_address_key')->nullable();
                $table->timestamp('in_service_date')->nullable();
                $table->string('applicable_fraction', 255)->nullable();
                $table->timestamp('last_edited', 3)->nullable();
                $table->tinyInteger('owner_paid_utilities')->nullable();
                $table->timestamp('acquisition_date')->nullable();
                $table->timestamp('building_built_date')->nullable();
                $table->nullableTimestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_buildings');
    }
}

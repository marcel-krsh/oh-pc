<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncBuildingStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sync_building_statuses')) {
            Schema::create('sync_building_statuses', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('allita_id')->nullable();
                $table->integer('building_status_key')->nullable();
                $table->string('building_status_name', 11)->nullable();
                $table->timestamp('last_edited', 3)->nullable();
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
        Schema::dropIfExists('sync_building_statuses');
    }
}

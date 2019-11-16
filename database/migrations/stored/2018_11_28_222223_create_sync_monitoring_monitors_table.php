<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncMonitoringMonitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('sync_monitoring_monitors')) {
            Schema::create('sync_monitoring_monitors', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('allita_id')->nullable();
                $table->integer('monitoring_key')->nullable();
                $table->integer('monitoring_monitor_key')->nullable();
                $table->integer('user_key')->nullable()->comment('assisting analyst');
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
        Schema::dropIfExists('sync_monitoring_monitors');
    }
}

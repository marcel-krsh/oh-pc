<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sync_monitorings')) {
            Schema::create('sync_monitorings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('allita_id')->nullable();
                $table->integer('monitoring_key')->nullable();
                $table->integer('development_key')->nullable();
                $table->integer('development_program_key')->nullable();
                $table->integer('monitoring_type_key')->nullable();
                $table->timestamp('start_date')->nullable()->comment('Date notification sent');
                $table->timestamp('completed_date')->nullable()->comment('monitor start date');
                $table->integer('contact_person_key')->nullable();
                $table->string('contact_title', 255)->nullable();
                $table->timestamp('confirmed_date')->nullable()->comment('date review closed');
                $table->integer('monitoring_status_type_key')->nullable();
                $table->string('comment')->nullable();
                $table->integer('entered_by_user_key')->nullable();
                $table->integer('user_key')->nullable()->comment('lead analyst');
                $table->timestamp('on_site_monitoring_end_date')->nullable()->comment('monitor end date');
                $table->string('status_results')->nullable();
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
        Schema::dropIfExists('sync_monitorings');
    }
}

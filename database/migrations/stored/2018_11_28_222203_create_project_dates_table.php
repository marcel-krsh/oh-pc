<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateProjectDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('project_dates')) {
            Schema::create('project_dates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('project_date_key')->nullable();
                $table->integer('project_key')->nullable();
                $table->unsignedInteger('project_id')->nullable();
                $table->unsignedInteger('project_program_id')->nullable();
                $table->integer('project_program_key')->nullable();
                $table->unsignedInteger('program_date_type_id')->nullable();
                $table->integer('program_date_type_key')->nullable();
                $table->string('comment', 255)->nullable();
                $table->timestamp('event_date')->nullable();
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
        Schema::dropIfExists('project_dates');
    }
}

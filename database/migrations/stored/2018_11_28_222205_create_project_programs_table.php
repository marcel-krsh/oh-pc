<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateProjectProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('project_programs')) {
            Schema::create('project_programs', function (Blueprint $table) {
                $table->integer('id')->nullable();
                $table->integer('project_program_key')->nullable();
                $table->unsignedInteger('project_id')->nullable();
                $table->integer('project_key')->nullable();
                $table->integer('program_key')->nullable();
                $table->unsignedInteger('program_id')->nullable();
                $table->integer('project_program_status_type_key')->nullable();
                $table->unsignedInteger('program_status_type_id')->nullable();
                $table->string('award_number', 255)->nullable();
                $table->string('application_number', 255)->nullable();
                $table->integer('assisted_units_anticipated')->nullable();
                $table->integer('assisted_units_actual')->nullable();
                $table->integer('floating_units')->nullable();
                $table->integer('total_building_count')->nullable();
                $table->integer('total_program_unit_count')->nullable();
                $table->string('first_year_award_claimed', 255)->nullable();
                $table->integer('federal_minimum_set_aside_key')->nullable();
                $table->integer('special_needs_units')->nullable();
                $table->integer('non_special_needs_units')->nullable();
                $table->integer('multiple_building_election_key')->nullable();
                $table->unsignedInteger('multiple_building_election_id')->nullable();
                $table->integer('employee_unit_count')->nullable();
                $table->string('guide_l_year', 255)->nullable();
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
        Schema::dropIfExists('project_programs');
    }
}

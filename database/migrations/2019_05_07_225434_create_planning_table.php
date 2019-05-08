<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('development_key');
            $table->integer('project_id');
            $table->string('project_number');
            $table->string('project_name');
            $table->boolean('failed_run')->default(false);
            $table->boolean('running')->default(false);
            $table->boolean('run')->default(false);
            $table->integer('total_building_count')->nullable();
            $table->integer('total_unit_count')->nullable();
            $table->integer('total_program_unit_count')->nullable();
            $table->integer('total_market_rate_unit_count')->nullable();
            $table->string('program_1_name')->nullable();
            $table->string('program_1_multiple_building_election')->nullable();
            $table->string('program_1_project_program_status')->nullable();
            $table->string('program_1_project_program_award_number')->nullable();
            $table->string('program_1_project_program_guide_year')->nullable();
            $table->integer('program_1_project_program_key')->nullable();
            $table->integer('program_1_funding_program_key')->nullable();
            $table->integer('program_1_project_program_id')->nullable();
            $table->integer('program_1_keyed_in_count')->nullable();
            $table->integer('program_1_calculated_count')->nullable();
            $table->boolean('program_1_first_year_award_claimed')->nullable();
            $table->integer('program_1_site_count')->nullable();
            $table->integer('program_1_file_count')->nullable();
            $table->string('program_2_name')->nullable();
            $table->string('program_2_multiple_building_election')->nullable();
            $table->string('program_2_project_program_status')->nullable();
            $table->string('program_2_project_program_award_number')->nullable();
            $table->string('program_2_project_program_guide_year')->nullable();
            $table->integer('program_2_project_program_key')->nullable();
            $table->integer('program_2_funding_program_key')->nullable();
            $table->integer('program_2_project_program_id')->nullable();
            $table->integer('program_2_keyed_in_count')->nullable();
            $table->integer('program_2_calculated_count')->nullable();
            $table->boolean('program_2_first_year_award_claimed')->nullable();
            $table->integer('program_2_site_count')->nullable();
            $table->integer('program_2_file_count')->nullable();
            $table->string('program_3_name')->nullable();
            $table->string('program_3_multiple_building_election')->nullable();
            $table->string('program_3_project_program_status')->nullable();
            $table->string('program_3_project_program_award_number')->nullable();
            $table->string('program_3_project_program_guide_year')->nullable();
            $table->integer('program_3_project_program_key')->nullable();
            $table->integer('program_3_funding_program_key')->nullable();
            $table->integer('program_3_project_program_id')->nullable();
            $table->integer('program_3_keyed_in_count')->nullable();
            $table->integer('program_3_calculated_count')->nullable();
            $table->boolean('program_3_first_year_award_claimed')->nullable();
            $table->integer('program_3_site_count')->nullable();
            $table->integer('program_3_file_count')->nullable();
            $table->integer('total_site_count')->nullable();
            $table->integer('total_file_count')->nullable();
            $table->integer('optimized_site_count')->nullable();
            $table->integer('optimized_file_count')->nullable();
            $table->integer('projection_year')->nullable();
            $table->integer('audit_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plannings');
    }
}

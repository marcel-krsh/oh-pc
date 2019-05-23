<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projections', function (Blueprint $table) {
            $table->increments('id');
           
            
            $table->string('project_number')->nullable();
            $table->string('project_name')->nullable();
            $table->boolean('failed_run')->default(false);
            $table->boolean('running')->default(false);
            $table->boolean('run')->default(false);
            
             
            
            $table->integer('development_key');
            $table->integer('project_id')->nullable(); 

            $table->integer('total_building_count')->nullable();
            $table->integer('total_unit_count')->nullable();
            $table->integer('total_program_unit_count')->nullable();
            $table->integer('total_market_rate_unit_count')->nullable();
            
            // KEYS FOR DEVCO 
            
            $table->integer('program_1_project_program_key')->nullable();
            $table->integer('program_1_funding_program_key')->nullable();
            $table->integer('program_1_program_key')->nullable();
           
            $table->integer('program_1_project_program_id')->nullable();
            $table->integer('program_1_program_id')->nullable();

            $table->string('program_1_name')->nullable();
            $table->string('program_1_multiple_building_election')->nullable();
            $table->string('program_1_project_program_status')->nullable();
            $table->string('program_1_project_program_award_number')->nullable();
            $table->string('program_1_project_program_guide_year')->nullable();
            $table->string('program_1_first_year_award_claimed')->nullable();

            $table->integer('program_1_keyed_in_unit_count')->nullable();
            $table->integer('program_1_calculated_unit_count')->nullable();
            $table->string('program_1_2016_percentage_used')->nullable();
            
            $table->integer('program_1_2016_site_count')->nullable();
            $table->integer('program_1_2019_site_count')->nullable();
            $table->string('program_1_2019_site_difference_percent')->nullable();

            $table->integer('program_1_2016_file_count')->nullable();
            $table->integer('program_1_2019_file_count')->nullable();
            $table->string('program_1_2019_file_difference_percent')->nullable();

            /////-------------------------------------------------------------------------////

            $table->integer('program_2_project_program_key')->nullable();
            $table->integer('program_2_funding_program_key')->nullable();
            $table->integer('program_2_program_key')->nullable();
           
            $table->integer('program_2_project_program_id')->nullable();
            $table->integer('program_2_program_id')->nullable();

            $table->string('program_2_name')->nullable();
            $table->string('program_2_multiple_building_election')->nullable();
            $table->string('program_2_project_program_status')->nullable();
            $table->string('program_2_project_program_award_number')->nullable();
            $table->string('program_2_project_program_guide_year')->nullable();
            $table->string('program_2_first_year_award_claimed')->nullable();

            $table->integer('program_2_keyed_in_unit_count')->nullable();
            $table->integer('program_2_calculated_unit_count')->nullable();
            $table->string('program_2_2016_percentage_used')->nullable();
            
            $table->integer('program_2_2016_site_count')->nullable();
            $table->integer('program_2_2019_site_count')->nullable();
            $table->string('program_2_2019_site_difference_percent')->nullable();

            $table->integer('program_2_2016_file_count')->nullable();
            $table->integer('program_2_2019_file_count')->nullable();
            $table->string('program_2_2019_file_difference_percent')->nullable();

            /////-------------------------------------------------------------------------////

            $table->integer('program_3_project_program_key')->nullable();
            $table->integer('program_3_funding_program_key')->nullable();
            $table->integer('program_3_program_key')->nullable();
           
            $table->integer('program_3_project_program_id')->nullable();
            $table->integer('program_3_program_id')->nullable();

            $table->string('program_3_name')->nullable();
            $table->string('program_3_multiple_building_election')->nullable();
            $table->string('program_3_project_program_status')->nullable();
            $table->string('program_3_project_program_award_number')->nullable();
            $table->string('program_3_project_program_guide_year')->nullable();
            $table->string('program_3_first_year_award_claimed')->nullable();

            $table->integer('program_3_keyed_in_unit_count')->nullable();
            $table->integer('program_3_calculated_unit_count')->nullable();
            $table->string('program_3_2016_percentage_used')->nullable();
            
            $table->integer('program_3_2016_site_count')->nullable();
            $table->integer('program_3_2019_site_count')->nullable();
            $table->string('program_3_2019_site_difference_percent')->nullable();

            $table->integer('program_3_2016_file_count')->nullable();
            $table->integer('program_3_2019_file_count')->nullable();
            $table->string('program_3_2019_file_difference_percent')->nullable();

            /////-------------------------------------------------------------------------////

            $table->integer('program_4_project_program_key')->nullable();
            $table->integer('program_4_funding_program_key')->nullable();
            $table->integer('program_4_program_key')->nullable();
           
            $table->integer('program_4_project_program_id')->nullable();
            $table->integer('program_4_program_id')->nullable();

            $table->string('program_4_name')->nullable();
            $table->string('program_4_multiple_building_election')->nullable();
            $table->string('program_4_project_program_status')->nullable();
            $table->string('program_4_project_program_award_number')->nullable();
            $table->string('program_4_project_program_guide_year')->nullable();
            $table->string('program_4_first_year_award_claimed')->nullable();

            $table->integer('program_4_keyed_in_unit_count')->nullable();
            $table->integer('program_4_calculated_unit_count')->nullable();
            $table->string('program_4_2016_percentage_used')->nullable();
            
            $table->integer('program_4_2016_site_count')->nullable();
            $table->integer('program_4_2019_site_count')->nullable();
            $table->string('program_4_2019_site_difference_percent')->nullable();

            $table->integer('program_4_2016_file_count')->nullable();
            $table->integer('program_4_2019_file_count')->nullable();
            $table->string('program_4_2019_file_difference_percent')->nullable();

            /////-------------------------------------------------------------------------////

            

            
            

            $table->integer('total_2016_site_count')->nullable();
            $table->integer('total_2019_site_count')->nullable();

            $table->integer('total_2016_file_count')->nullable();
            $table->integer('total_2019_file_count')->nullable();

            $table->integer('optimized_2019_site_count')->nullable();
            $table->integer('optimized_2019_file_count')->nullable();


            $table->integer('2019_buildings_with_unit_inspections')->nullable();

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

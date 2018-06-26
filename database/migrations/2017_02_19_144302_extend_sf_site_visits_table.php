<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtendSfSiteVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sf_site_visits', function (Blueprint $table) {
            //
            $table->text('other_notes')->nullable();
            $table->text('corrective_action_required')->nullable();
            $table->string('X10_Retainage_released_to_contractor__c')->nullable();
            $table->string('X11_Is_a_recap_of_maint_funds_required__c')->nullable();
            $table->string('X12_Amount_of_maint_recapture_due__c')->nullable();
            $table->string('X3_Was_the_property_graded_and_seeded__c')->nullable();
            $table->string('X4_Is_there_any_signage__c')->nullable();
            $table->string('X5_Is_grass_growing_consistently_across__c')->nullable();
            $table->string('X6_Is_grass_mowed_weeded__c')->nullable();
            $table->string('X7_Was_the_property_landscaped__c')->nullable();
            $table->string('X8_Nuisance_Elements_or_Code_Violations__c')->nullable();
            $table->string('X9_Are_there_Environmental_Conditions__c')->nullable();

           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sf_site_visists', function (Blueprint $table) {
            //
        });
    }
}

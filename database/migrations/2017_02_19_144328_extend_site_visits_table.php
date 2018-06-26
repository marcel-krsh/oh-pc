<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtendSiteVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_visits', function (Blueprint $table) {
            //
            $table->text('other_notes')->nullable();
            $table->text('corrective_action_required')->nullable();
            $table->boolean('retainage_released_to_contractor')->nullable();
            $table->boolean('is_a_recap_of_maint_funds_required')->nullable();
            $table->boolean('amount_of_maint_recapture_due')->nullable();
            $table->boolean('was_the_property_graded_and_seeded')->nullable();
            $table->boolean('is_there_any_signage')->nullable();
            $table->boolean('is_grass_growing_consistently_across')->nullable();
            $table->boolean('is_grass_mowed_weeded')->nullable();
            $table->boolean('was_the_property_landscaped')->nullable();
            $table->boolean('nuisance_elements_or_code_violations')->nullable();
            $table->boolean('are_there_environmental_conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_visits', function (Blueprint $table) {
            //
        });
    }
}

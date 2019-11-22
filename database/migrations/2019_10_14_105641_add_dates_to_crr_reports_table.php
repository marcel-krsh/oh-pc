<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToCrrReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crr_reports', function (Blueprint $table) {
            //
            $table->dateTime('viewed_by_property_date')->nullable();
            $table->dateTime('all_ehs_resolved_date')->nullable();
            $table->dateTime('all_findings_resolved_date')->nullable();
            $table->dateTime('date_ehs_resolutions_due')->nullable();
            $table->dateTime('date_all_resolutions_due')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crr_reports', function (Blueprint $table) {
            //
        });
    }
}

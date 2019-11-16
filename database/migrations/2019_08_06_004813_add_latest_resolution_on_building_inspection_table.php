<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatestResolutionOnBuildingInspectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('building_inspections', function (Blueprint $table) {
            $table->dateTime('latest_resolution')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_inspections', function (Blueprint $table) {
            $table->dropColumn('latest_resolution');
        });
    }
}

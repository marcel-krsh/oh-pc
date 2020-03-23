<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateOfInspectionToUnitInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_inspections', function (Blueprint $table) {
            //
            $table->date('date_of_inspection')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_inspections', function (Blueprint $table) {
            //
            $table->dropeColumn('date_of_inspection');
        });
    }
}

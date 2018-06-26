<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDispositionCalculations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->integer('hfa_calc_months_prepaid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropColumn('hfa_calc_months_prepaid');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeValidationDatesNullOnParcels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            //
            $table->dateTime('date_lb_validated')->nullable()->change();
            $table->dateTime('date_hfa_validated')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            //
        });
    }
}

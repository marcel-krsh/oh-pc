<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orderingamenityinspection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordering_building', function (Blueprint $table) {
            //if (!Schema::hasColumn('amenity_inspection_id')){
                $table->unsignedInteger('amenity_inspection_id')->nullable();
            //}
        });
        Schema::table('ordering_unit', function (Blueprint $table) {
            //if (!Schema::hasColumn('amenity_inspection_id')){
                $table->unsignedInteger('amenity_inspection_id')->nullable();
            //}
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordering_building', function (Blueprint $table) {
            $table->dropColumn('amenity_inspection_id');
        });
        Schema::table('ordering_unit', function (Blueprint $table) {
            $table->dropColumn('amenity_inspection_id');
        });
    }
}

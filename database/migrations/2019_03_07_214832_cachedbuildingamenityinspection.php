<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Cachedbuildingamenityinspection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cached_buildings', function (Blueprint $table) {
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
        Schema::table('cached_buildings', function (Blueprint $table) {
            $table->dropColumn('amenity_inspection_id');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Parcelnextstep extends Migration
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
            $table->integer('next_step')->unsigned()->nullable();
            $table->foreign('next_step')->references('id')->on('guide_steps');
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
            $table->dropForeign(['next_step']);
        });

        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn('next_step');
        });
    }
}

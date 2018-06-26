<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParcelGroupMaxToProgramRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_rules', function (Blueprint $table) {
            //
            $table->float('parcel_group_max',10,2)->default(75000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_rules', function (Blueprint $table) {
            //
        });
    }
}

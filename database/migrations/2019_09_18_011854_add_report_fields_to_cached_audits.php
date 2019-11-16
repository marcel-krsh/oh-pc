<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportFieldsToCachedAudits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cached_audits', function (Blueprint $table) {
            $table->string('car_icon')->nullable();
            $table->string('car_status')->nullable();
            $table->integer('car_id')->nullable();
            $table->string('car_status_text')->nullable();
            $table->string('ehs_icon')->nullable();
            $table->string('ehs_status')->nullable();
            $table->string('ehs_status_text')->nullable();
            $table->integer('ehs_id')->nullable();
            $table->string('_8823_icon')->nullable();
            $table->string('_8823_status')->nullable();
            $table->integer('_8823_id')->nullable();
            $table->string('_8823_status_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cached_audits', function (Blueprint $table) {
            //

            $table->dropColumn('car_icon');
            $table->dropColumn('car_status');
            $table->dropColumn('car_id');
            $table->dropColumn('car_status_text');
            $table->dropColumn('ehs_icon');
            $table->dropColumn('ehs_status');
            $table->dropColumn('ehs_status_text');
            $table->dropColumn('ehs_id');
            $table->dropColumn('_8823_icon');
            $table->dropColumn('_8823_status');
            $table->dropColumn('_8823_id');
            $table->dropColumn('_8823_status_text');
        });
    }
}

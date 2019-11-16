<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByAndUpdatedByToCrrReportsTable extends Migration
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
            $table->integer('last_updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->json('report_history')->nullable();
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

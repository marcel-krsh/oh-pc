<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultAndDevcoFieldsToReportAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_access', function (Blueprint $table) {
            $table->boolean('default')->default(0)->nullable();
            $table->boolean('devco')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_access', function (Blueprint $table) {
            $table->dropColumn('default');
            $table->dropColumn('devco');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignatureVersionColumsToCrrReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crr_reports', function (Blueprint $table) {
            
            $table->integer('signed_version')->nullable();
            $table->timeStamp('date_signed')->nullable();
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
            
            $table->dropColumn('signed_version');
            $table->dropColumn('date_signed');
        });
    }
}

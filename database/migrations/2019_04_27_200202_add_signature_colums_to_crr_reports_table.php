<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignatureColumsToCrrReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crr_reports', function (Blueprint $table) {
            $table->string('signed_by')->nullable();
            $table->text('signature')->nullable();
            $table->integer('signed_by_id')->nullable();
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
            $table->dropColumn('signed_by');
            $table->dropColumn('signature');
            $table->dropColumn('signed_by_id');
        });
    }
}

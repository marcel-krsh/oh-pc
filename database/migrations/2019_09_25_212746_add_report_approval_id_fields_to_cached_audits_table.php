<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportApprovalIdFieldsToCachedAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cached_audits', function (Blueprint $table) {
          	$table->integer('car_approval_type_id')->nullable();
          	$table->integer('ehs_approval_type_id')->nullable();
          	$table->integer('_8823_approval_type_id')->nullable();
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
           $table->dropColumn('car_approval_type_id');
           $table->dropColumn('ehs_approval_type_id');
           $table->dropColumn('_8823_approval_type_id');
        });
    }
}

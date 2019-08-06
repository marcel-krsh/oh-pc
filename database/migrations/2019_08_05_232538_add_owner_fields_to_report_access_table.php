<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerFieldsToReportAccessTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('report_access', function (Blueprint $table) {
      $table->integer('owner_id')->nullable();
      $table->boolean('owner_default')->default(0)->nullable();
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
      $table->dropColumn('owner_id');
      $table->dropColumn('owner_default');
    });
  }
}

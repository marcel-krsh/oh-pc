<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerDefaultFieldToUserPhoneNumbersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('user_phone_numbers', function (Blueprint $table) {
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
    Schema::table('user_phone_numbers', function (Blueprint $table) {
      $table->dropColumn('owner_default');
    });
  }
}

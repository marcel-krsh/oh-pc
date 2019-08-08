<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_addresses', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id');
      $table->integer('address_id');
      $table->integer('project_id')->nullable();
      $table->boolean('default')->default(0)->nullable();
      $table->boolean('devco')->default(0)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('user_addresses', function (Blueprint $table) {
      Schema::dropIfExists('user_addresses');
    });
  }
}

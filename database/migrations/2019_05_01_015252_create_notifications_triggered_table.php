<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTriggeredTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('notifications_triggered', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->integer('from_id')->nullable();
      $table->integer('to_id');
      $table->text('data');
      $table->smallInteger('type_id');
      $table->boolean('active')->nullable()->default(1);
      $table->timestamp('deliver_time')->nullable();
      $table->text('token')->nullable();
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
    Schema::dropIfExists('notifications_triggered');
  }
}

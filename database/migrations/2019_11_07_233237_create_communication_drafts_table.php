<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunicationDraftsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('communication_drafts', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('project_id')->nullable();
      $table->integer('audit_id')->nullable();
      $table->integer('report_id')->nullable();
      $table->integer('finding_id')->nullable();
      $table->integer('owner_id');
      $table->text('subject')->nullable();
      $table->text('message')->nullable();
      $table->text('finding_ids')->nullable();
      $table->text('recipients')->nullable();
      $table->text('documents')->nullable();
      // doc category
      // document name
      // saved path
      // doc comment
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
    Schema::dropIfExists('communication_drafts');
  }
}

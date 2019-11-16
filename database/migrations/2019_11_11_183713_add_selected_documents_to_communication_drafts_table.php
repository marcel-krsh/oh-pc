<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectedDocumentsToCommunicationDraftsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('communication_drafts', function (Blueprint $table) {
      $table->text('selected_documents')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('communication_drafts', function (Blueprint $table) {
      $table->dropColumn('selected_documents');
    });
  }
}

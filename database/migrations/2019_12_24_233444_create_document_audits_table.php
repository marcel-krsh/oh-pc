<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentAuditsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document_audits', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('audit_id')->nullable();
			$table->integer('document_id')->nullable();
			$table->integer('sync_docuware_id')->nullable();
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
		Schema::table('document_audits', function (Blueprint $table) {
			Schema::dropIfExists('document_audits');
		});
	}
}

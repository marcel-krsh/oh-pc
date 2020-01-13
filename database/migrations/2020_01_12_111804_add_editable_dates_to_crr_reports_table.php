<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditableDatesToCrrReportsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('crr_reports', function (Blueprint $table) {
			$table->dateTime('review_date')->nullable();
			$table->dateTime('letter_date')->nullable();
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
			$table->dropColumn('review_date');
			$table->dropColumn('letter_date');
		});
	}
}

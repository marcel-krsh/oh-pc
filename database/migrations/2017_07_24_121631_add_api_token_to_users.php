<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiTokenToUsers extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 60)->nullable();
        });
    }

    /**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
    public function down()
    {
		if (Schema::hasColumn('users', 'api_token'))
		{
			Schema::table('users', function (Blueprint $table) {
				$table->dropColumn('api_token');
			});
		}
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateUsersAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users_audits')) {
            
            Schema::create('users_audits', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('audit_id');
                $table->unsignedInteger('user_id');
                $table->tinyInteger('is_lead')->nullable()->default(0);
                $table->nullableTimestamps();
            });

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_audits');
    }
}

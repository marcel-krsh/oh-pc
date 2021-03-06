<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateAuditAuditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('audit_auditors')) {
            Schema::create('audit_auditors', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('monitoring_monitor_key')->nullable();
                $table->integer('monitoring_key')->nullable();
                $table->unsignedInteger('audit_id')->nullable();
                $table->integer('user_key')->nullable()->comment('assisting analyst');
                $table->unsignedInteger('user_id')->nullable();
                $table->timestamp('last_edited', 3)->nullable();
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
        Schema::dropIfExists('audit_auditors');
    }
}

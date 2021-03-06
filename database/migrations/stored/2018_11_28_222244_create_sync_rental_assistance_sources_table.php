<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncRentalAssistanceSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sync_rental_assistance_sources')) {
            Schema::create('sync_rental_assistance_sources', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('allita_id')->nullable();
                $table->integer('rental_assistance_source_key')->nullable();
                $table->string('rental_assistance_source_name', 255)->nullable();
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
        Schema::dropIfExists('sync_rental_assistance_sources');
    }
}

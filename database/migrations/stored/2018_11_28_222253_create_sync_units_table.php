<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sync_units')) {
            Schema::create('sync_units', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('allita_id')->nullable();
                $table->integer('unit_key')->nullable();
                $table->integer('unit_bedroom_key')->nullable();
                $table->float('unit_square_feet')->nullable();
                $table->integer('unit_status_key')->nullable();
                $table->integer('ami_percentage_key')->nullable();
                $table->string('unit_name', 255)->nullable();
                $table->integer('unit_identity_key')->nullable();
                $table->timestamp('status_date')->nullable();
                $table->tinyInteger('is_unit_handicap_accessible')->nullable();
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
        Schema::dropIfExists('sync_units');
    }
}

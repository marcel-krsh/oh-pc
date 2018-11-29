<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSyncAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sync_addresses')) {
            Schema::create('sync_addresses', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('address_key')->nullable();
                $table->unsignedInteger('allita_id')->nullable();
                $table->string('line_1', 255)->nullable();
                $table->string('line_2', 255)->nullable();
                $table->string('city', 255)->nullable();
                $table->unsignedInteger('state_id')->nullable();
                $table->string('state', 50)->nullable();
                $table->string('zip', 5)->nullable();
                $table->string('zip_4', 4)->nullable();
                $table->string('longitude', 255)->nullable();
                $table->string('latitude', 255)->nullable();
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
        Schema::dropIfExists('sync_addresses');
    }
}

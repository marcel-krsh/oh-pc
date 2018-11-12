<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateCachedBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_buildings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('audit_id');
            $table->string('status', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('city', 250)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip', 100)->nullable();
            $table->json('auditors_json')->nullable();
            $table->json('areas_json')->nullable();
            $table->nullableTimestamps();

            

            

        });

        

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_buildings');
    }
}

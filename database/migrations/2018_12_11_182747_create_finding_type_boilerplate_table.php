<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateFindingTypeBoilerplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('default_followups')) {
            Schema::create('default_followups', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('finding_type_id')->nullable();
                $table->unsignedInteger('boilerplate_id')->nullable();
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
        Schema::dropIfExists('finding_type_boilerplate');
    }
}

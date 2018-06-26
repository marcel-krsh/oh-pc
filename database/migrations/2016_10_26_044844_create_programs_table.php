<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->string('sf_id'); // id used in sales force - used to link up legacy files other items.
            $table->string('owner_type')->default('user');
            $table->integer('owner_id')->unsigned()->default(2); // defaults to Holly
            $table->integer('entity_id')->unsigned();
            $table->string('program_name');
            $table->integer('county_id')->unsigned();
            $table->tinyInteger('active')->default(1);
            $table->integer('default_program_rules_id')->default(1);
            // Program options should go here:
            $table->integer('program_options')->default(1);
            $table->timestamps();
            $table->index('owner_id');
            $table->index('county_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('import_rows', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('import_id')->unsigned();
            $table->string('table_name');
            $table->integer('row_id')->unsigned();

            $table->boolean('row_updated')->nullable();

            $table->foreign('import_id')->references('id')->on('imports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_rows');
        Schema::dropIfExists('imports');
    }
}

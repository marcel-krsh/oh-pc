<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntityToImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imports', function (Blueprint $table) {
             $table->integer('entity_id')->unsigned();
             $table->integer('program_id')->unsigned();
             $table->integer('account_id')->unsigned();
             $table->string('original_file');
             $table->tinyInteger('validated')->default(0);
             $table->foreign('entity_id')->references('id')->on('entities');
             $table->foreign('program_id')->references('id')->on('programs');
             $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            //
        });
    }
}

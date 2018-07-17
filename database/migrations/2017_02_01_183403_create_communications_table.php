<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('communications', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('parcel_id')->unsigned()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type')->default('program');
            $table->text('message')->nullable();

            $table->foreign('parent_id')->references('id')->on('communications');
            $table->foreign('parcel_id')->references('id')->on('parcels');
            $table->foreign('owner_id')->references('id')->on('users');
        });

        Schema::create('communication_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('communication_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->tinyInteger('seen')->default(0);
              
            $table->foreign('communication_id')->references('id')->on('communications');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('communication_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('communication_id')->unsigned()->nullable();
            $table->integer('document_id')->unsigned()->nullable();
              
            $table->foreign('communication_id')->references('id')->on('communications');
            $table->foreign('document_id')->references('id')->on('documents');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('communications');
        Schema::dropIfExists('communication_recipients');
        Schema::dropIfExists('communication_documents');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

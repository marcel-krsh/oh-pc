<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
        });


        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('document_type_id')->unsigned()->nullable();
            $table->integer('parcel_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->string('file_path')->nullable();
            $table->string('ohfa_file_path')->nullable();

            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('parcel_id')->references('id')->on('parcels');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunicationDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('communication_id');
            $table->unsignedInteger('document_id')->nullable();
            $table->unsignedInteger('sync_docuware_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communication_documents');
    }
}

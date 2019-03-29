<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalDocumentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_document_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id');
            $table->unsignedInteger('document_category_id');
            $table->unsignedInteger('project_id');
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
        Schema::dropIfExists('local_document_categories');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditRequiredDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_required_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('audit_id');
            $table->bigInteger('document_category_id');
            $table->boolean('site_level');
            $table->boolean('bin_level');
            $table->boolean('unit_level');
            $table->json('unit_ids')->nullable();
            $table->json('building_ids')->nullable();
            $table->bigInteger('program_id')->nullable();
            $table->string('description',255);
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
        Schema::dropIfExists('audit_required_documents');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequirementsToDocumentCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_categories', function (Blueprint $table) {
            //
            $table->boolean('required_for_site');
            $table->boolean('required_for_bin');
            $table->boolean('required_for_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_categories', function (Blueprint $table) {
            //
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDispositionTableCheckboxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->boolean('legal_description_in_documents')->nullable()->default('0');
            $table->boolean('description_use_in_documents')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropColumn('legal_description_in_documents');
            $table->dropColumn('description_use_in_documents');
        });
    }
}

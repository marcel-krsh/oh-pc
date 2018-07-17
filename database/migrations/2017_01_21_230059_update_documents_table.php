<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // remove document_types table, unused
        Schema::dropIfExists('document_types');

        // remove foreign key to dropped table and add new one
        Schema::table('documents', function (Blueprint $table) {

            $table->string('comment')->nullable();
            $table->text('categories')->nullable();

            $table->dropForeign(['document_type_id']);
            $table->dropColumn('document_type_id');
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
        Schema::dropIfExists('documents');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

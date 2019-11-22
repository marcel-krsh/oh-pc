<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('document_approver_id')->nullable();
            $table->integer('document_decliner_id')->nullable();
            $table->dropForeign('documents_parcel_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('document_approver_id');
            $table->dropColumn('document_decliner_id');
            $table->foreign('audit_id')->references('id')->on('parcels');
        });
    }
}

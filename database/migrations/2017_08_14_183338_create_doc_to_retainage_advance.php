<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocToRetainageAdvance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_to_retainage', function (Blueprint $table) {
            $table->integer('document_id')->unsigned()->index();
            $table->integer('retainage_id')->unsigned()->index();
            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade');
            $table->foreign('retainage_id')
                ->references('id')
                ->on('retainages')
                ->onDelete('cascade');
            $table->primary(['document_id','retainage_id']);
        });

        Schema::create('document_to_advance', function (Blueprint $table) {
            $table->integer('document_id')->unsigned()->index();
            $table->integer('cost_item_id')->unsigned()->index();
            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade');
            $table->foreign('cost_item_id')
                ->references('id')
                ->on('cost_items')
                ->onDelete('cascade');
            $table->primary(['document_id','cost_item_id']);
        });

        Schema::table('cost_items', function (Blueprint $table) {
            $table->tinyInteger('advance_paid')->nullable(); 
            $table->date('advance_paid_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_to_retainage');
        Schema::dropIfExists('document_to_advance');
        Schema::table('cost_items', function (Blueprint $table) {
            $table->dropColumn('advance_paid');
            $table->dropColumn('advance_paid_date');
        });
    }
}

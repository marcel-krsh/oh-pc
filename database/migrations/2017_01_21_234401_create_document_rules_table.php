<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->float('amount',10,2)->default(25000.01);
            $table->unsignedInteger('program_rules_id')->default(1);
            $table->unsignedInteger('expense_category_id');
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
        Schema::dropIfExists('document_rules');
    }
}

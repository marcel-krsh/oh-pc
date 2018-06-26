<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpenseCategoryRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_category_rules', function (Blueprint $table) {
            $table->integer('expense_category_id')->references('id')->on('expense_categories');
            $table->integer('rule_id')->references('id')->on('program_rules');
            $table->boolean('allow_advance')->nullable()->default('0');
            $table->string('advance_rule_type')->default('amount');
            $table->float('max_advance_amount',12,2)->nullable();
            $table->float('min_advance_amount',12,2)->nullable();
            $table->string('categories_for_max')->nullable();
            $table->string('categories_for_min')->nullable();
            $table->float('require_advance_documents_at',12,2)->nullable();
            $table->string('advance_document_ids')->nullable();
            $table->string('rule_type')->default('amount');
            $table->float('max_amount',12,2)->nullable();
            $table->float('min_amount',12,2)->nullable();
            // $table->string('categories_for_max')->nullable();
            // $table->string('categories_for_min')->nullable();
            $table->float('require_documents_at',12,2)->nullable();
            $table->string('documents_ids')->nullable();
            $table->increments('id');
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
        Schema::dropIfExists('expense_category_rules');
    }
}

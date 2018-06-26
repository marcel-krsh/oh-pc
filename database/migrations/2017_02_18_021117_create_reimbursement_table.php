<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('minimum_units');
            $table->integer('maximum_units')->nullable();
            $table->integer('maximum_reimbursement');
            $table->unsignedInteger('program_rules_id');
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
        Schema::dropIfExists('reimbursement_rules');
    }
}

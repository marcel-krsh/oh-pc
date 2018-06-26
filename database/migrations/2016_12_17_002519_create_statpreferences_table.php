<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatpreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statpreferences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->tinyInteger('acquisition_average_zeros')->default(1);
            $table->tinyInteger('acquisition_median_zeros')->default(1);
            $table->tinyInteger('pre_demo_average_zeros')->default(1);
            $table->tinyInteger('pre_demo_median_zeros')->default(1);
            $table->tinyInteger('demolition_average_zeros')->default(1);
            $table->tinyInteger('demolition_median_zeros')->default(1);
            $table->tinyInteger('administration_average_zeros')->default(1);
            $table->tinyInteger('administration_median_zeros')->default(1);
            $table->tinyInteger('greening_average_zeros')->default(0);
            $table->tinyInteger('greening_median_zeros')->default(0);
            $table->tinyInteger('maintenance_average_zeros')->default(1);
            $table->tinyInteger('maintenance_median_zeros')->default(1);
            $table->tinyInteger('other_average_zeros')->default(1);
            $table->tinyInteger('other_median_zeros')->default(1);
            $table->tinyInteger('nip_loan_payoff_average_zeros')->default(1);
            $table->tinyInteger('nip_loan_payoff_median_zeros')->default(1);
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
        Schema::dropIfExists('statpreferences');
    }
}

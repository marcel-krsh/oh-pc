<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->string('rules_name')->default('Legacy Rules');
            $table->tinyInteger('acquisition_advance')->default(0);
            $table->float('acquisition_max_advance',10,2)->default(0.00);
            $table->tinyInteger('pre_demo_advance')->default(0);
            $table->float('pre_demo_max_advance',10,2)->default(0.00);
            $table->tinyInteger('demolition_advance')->default(0);
            $table->float('demolition_max_advance',10,2)->default(0.00);
            $table->tinyInteger('greening_advance')->default(1);
            $table->float('greening_max_advance',10,2)->default(1500.00);
            $table->tinyInteger('maintenance_advance')->default(1);
            $table->float('maintenance_max_advance',10,2)->default(1200.00);
            $table->tinyInteger('administration_advance')->default(0);
            $table->float('administration_max_advance',10,2)->default(1000.00);
            $table->tinyInteger('other_advance')->default(0);
            $table->float('other_max_advance',10,2)->default(0.00);
            $table->tinyInteger('nip_loan_payoff_advance')->default(1);
            $table->float('nip_loan_payoff_max_advance',10,2)->default(0.00);
            /// 0 means balance of what is left
            $table->float('acquisition_max',10,2)->default(100);
            $table->float('pre_demo_max',10,2)->default(0);
            $table->float('demolition_max',10,2)->default(0);
            $table->float('greening_max',10,2)->default(6000);
            $table->float('maintenance_max',10,2)->default(1200);
            $table->float('admin_max_percent',10,2)->default(0.1);
            $table->float('other_max',10,2)->default(0);
            $table->float('nip_loan_payoff_max',10,2)->default(100);

            $table->float('acquisition_min',10,2)->default(0);
            $table->float('pre_demo_min',10,2)->default(0);
            $table->float('demolition_min',10,2)->default(0);
            $table->float('greening_min',10,2)->default(.01);
            $table->float('maintenance_min',10,2)->default(0);
            $table->float('admin_min',10,2)->default(0);
            $table->float('other_min',10,2)->default(0);
            $table->float('nip_loan_payoff_min',10,2)->default(0);

            $table->float('acquisition_document_req_min',10,2)->default(25000.01);
            $table->float('pre_demo_document_req_min',10,2)->default(25000.01);
            $table->float('demolition_document_req_min',10,2)->default(25000.01);
            $table->float('greening_document_req_min',10,2)->default(25000.01);
            $table->float('maintenance_document_req_min',10,2)->default(25000.01);
            $table->float('admin_document_req_min',10,2)->default(1000);
            $table->float('other_document_req_min',10,2)->default(25000.01);
            $table->float('nip_loan_payoff_document_req_min',10,2)->default(25000.01);

            $table->text('acquisition_document_categories')->nullable();
            $table->text('pre_demo_document_categories')->nullable();
            $table->text('demolition_document_categories')->nullable();
            $table->text('greening_document_categories')->nullable();
            $table->text('maintenance_document_categories')->nullable();
            $table->text('admin_document_categories')->nullable();
            $table->text('other_document_categories')->nullable();
            $table->text('nip_loan_payoff_document_categories')->nullable();
            $table->text('required_document_categories')->nullable();

            $table->float('parcel_total_max',10,2)->default(25000);

            $table->float('maintenance_recap_pro_rate',10,2)->default(33.33);

            $table->float('imputed_cost_per_parcel',10,2)->default(200.00);

            $table->tinyInteger('active')->default(1);
            $table->integer('for_parcel')->nullable();
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('program_rules');
    }
}
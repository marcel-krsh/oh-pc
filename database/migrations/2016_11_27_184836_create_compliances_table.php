<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compliances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sf_parcel_id')->nullable();
            $table->integer('property_type_id')->nullable();
            $table->tinyInteger('property_yes')->nullable();
            $table->string('property_notes')->nullable();
            $table->integer('parcel_id')->nullable();
            $table->integer('program_id')->nullable();
            $table->integer('created_by_user_id')->nullable();
            $table->integer('analyst_id')->nullable();
            $table->integer('auditor_id')->nullable();
            $table->timeStamp('audit_date')->nullable();
            $table->tinyInteger('checklist_yes')->nullable();
            $table->string('checklist_notes')->nullable();
            $table->tinyInteger('consolidated_certs_pass')->nullable();
            $table->string('consolidated_certs_notes')->nullable();
            $table->tinyInteger('contractors_yes')->nullable();
            $table->string('contractors_notes')->nullable();
            $table->tinyInteger('environmental_yes')->nullable();
            $table->string('environmental_notes')->nullable();
            $table->tinyInteger('funding_limits_pass')->nullable();
            $table->string('funding_limits_notes')->nullable();
            $table->tinyInteger('inelligible_costs_yes')->nullable();
            $table->string('inelligible_costs_notes')->nullable();
            $table->string('items_Reimbursed')->nullable();
            $table->tinyInteger('note_mortgage_pass')->nullable();
            $table->string('note_mortgage_notes')->nullable();
            $table->tinyInteger('payment_processing_pass')->nullable();
            $table->string('payment_processing_notes')->nullable();
            $table->tinyInteger('loan_requirements_pass')->nullable();
            $table->string('loan_requirements_notes')->nullable();
            $table->tinyInteger('photos_yes')->nullable();
            $table->string('photos_notes')->nullable();
            $table->tinyInteger('salesforce_yes')->nullable();
            $table->string('salesforce_notes')->nullable();
            $table->tinyInteger('right_to_demo_pass')->nullable();
            $table->string('right_to_demo_notes')->nullable();
            $table->tinyInteger('reimbursement_doc_pass')->nullable();
            $table->string('reimbursement_doc_notes')->nullable();
            $table->tinyInteger('target_area_yes')->nullable();
            $table->string('target_area_notes')->nullable();
            $table->tinyInteger('sdo_pass')->nullable();
            $table->string('sdo_notes')->nullable();
            $table->string('score')->nullable();
            $table->tinyInteger('if_fail_corrected')->nullable();
            $table->tinyInteger('property_pass')->nullable();
            $table->tinyInteger('random_audit')->nullable();
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
        Schema::dropIfExists('compliances');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCategoriesFromProgramRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('program_rules', function (Blueprint $table) {
            //
            $table->dropColumn([
                'acquisition_document_req_min',
                'pre_demo_document_req_min',
                'demolition_document_req_min',
                'greening_document_req_min',
                'maintenance_document_req_min',
                'admin_document_req_min',
                'other_document_req_min',
                'nip_loan_payoff_document_req_min',
                'acquisition_document_categories',
                'pre_demo_document_categories',
                'demolition_document_categories',
                'greening_document_categories',
                'maintenance_document_categories',
                'admin_document_categories',
                'other_document_categories',
                'nip_loan_payoff_document_categories',
                'required_document_categories'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_rules', function (Blueprint $table) {
            //
        });
    }
}

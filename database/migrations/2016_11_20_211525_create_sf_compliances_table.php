<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfCompliancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_compliances', function (Blueprint $table) {
            $table->increments('id');
            $table->text('PropertyID')->nullable();
            $table->string('PropertyType')->nullable();
            $table->tinyInteger('PropertyYes')->nullable();
            $table->string('PropertyVacantBlighted')->nullable();
            $table->string('PropertyNotes')->nullable();
            $table->string('ParcelID')->nullable();
            $table->string('ProgramName')->nullable();
            $table->string('PropertyName')->nullable();
            $table->string('ComplianceName')->nullable();
            $table->string('CreatedDate')->nullable();
            $table->string('CreatedByFullName')->nullable();
            $table->string('Analyst')->nullable();
            $table->string('Auditor')->nullable();
            $table->string('AuditDate')->nullable();
            $table->tinyInteger('ChecklistYes')->nullable();
            $table->string('Checklist')->nullable();
            $table->string('ChecklistNotes')->nullable();
            $table->tinyInteger('ConsolidatedCertsPass')->nullable();
            $table->string('ConsolidatedCertifications')->nullable();
            $table->string('ConsolidatedCertsNotes')->nullable();
            $table->tinyInteger('ContractorsYes')->nullable();
            $table->string('Contractors')->nullable();
            $table->string('ContractorsNotes')->nullable();
            $table->tinyInteger('EnvironmentalYes')->nullable();
            $table->string('Environmental')->nullable();
            $table->string('EnvironmentalNotes')->nullable();
            $table->tinyInteger('FundingLimitsPass')->nullable();
            $table->string('FundingLimits')->nullable();
            $table->string('FundingLimitsNotes')->nullable();
            $table->tinyInteger('InelligibleCostsYes')->nullable();
            $table->string('InelligibleCosts')->nullable();
            $table->string('InelligibleCostsNotes')->nullable();
            $table->string('ItemsReimbursed')->nullable();
            $table->tinyInteger('NoteMortgagePass')->nullable();
            $table->string('NoteMortgage')->nullable();
            $table->string('NoteMortgageNotes')->nullable();
            $table->tinyInteger('PaymentProcessingPass')->nullable();
            $table->string('PaymentProcessing')->nullable();
            $table->string('PaymentProcessingNotes')->nullable();
            $table->tinyInteger('LoanRequirementsPass')->nullable();
            $table->string('LoanRequirements')->nullable();
            $table->string('LoanRequirementsNotes')->nullable();
            $table->tinyInteger('PhotosYes')->nullable();
            $table->string('Photos')->nullable();
            $table->string('PhotosNotes')->nullable();
            $table->tinyInteger('SalesforceYes')->nullable();
            $table->string('Salesforce')->nullable();
            $table->string('SalesforceNotes')->nullable();
            $table->tinyInteger('RighttoDemoPass')->nullable();
            $table->string('RighttoDemo')->nullable();
            $table->string('RighttoDemoNotes')->nullable();
            $table->tinyInteger('ReimbursementDocPass')->nullable();
            $table->string('ReimbursementDocumentation')->nullable();
            $table->string('ReimbursementDocNotes')->nullable();
            $table->tinyInteger('TargetAreaYes')->nullable();
            $table->string('TargetArea')->nullable();
            $table->string('TargetAreaNotes')->nullable();
            $table->tinyInteger('SDOPass')->nullable();
            $table->string('SDO')->nullable();
            $table->string('SDONotes')->nullable();
            $table->string('Score')->nullable();
            $table->tinyInteger('Iffailcorrected')->nullable();
            $table->tinyInteger('PropertyPass')->nullable();
            $table->tinyInteger('RandomAudit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_compliances');
    }
}

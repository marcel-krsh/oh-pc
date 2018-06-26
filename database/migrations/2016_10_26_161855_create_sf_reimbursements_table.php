<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfReimbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_reimbursements', function (Blueprint $table) {
            $table->increments('id');
            $table->text('PropertyIDRecordID')->collate('utf8_bin')->nullable();
            $table->text('PropertyIDParcelID')->nullable();
            $table->text('PropertyIDPropertyName')->nullable();
            $table->text('ProgramProgramName')->nullable();
            $table->integer('BatchNumber')->nullable();
            $table->text('ReimbursementID')->collate('utf8_bin')->nullable();
            $table->text('ReimbursementCreatedDate')->nullable();
            $table->text('DatePaid')->nullable();
            $table->text('ReimbursementReimbursementName')->nullable();
            $table->integer('GreeningAdvanceDocumented')->nullable();
            $table->integer('GreeningAdvanceOption')->nullable();
            $table->float('GreeningCost', 10, 2)->nullable();
            $table->float('GreeningRequested', 10, 2)->nullable();
            $table->float('GreeningApproved', 10, 2)->nullable();
            $table->float('GreeningPaid', 10, 2)->nullable();
            $table->float('PreDemoCost', 10, 2)->nullable();
            $table->float('PreDemoRequested', 10, 2)->nullable();
            $table->float('PreDemoApproved', 10, 2)->nullable();
            $table->float('PreDemoPaid', 10, 2)->nullable();
            $table->float('MaintenanceCost', 10, 2)->nullable();
            $table->float('MaintenanceRequested', 10, 2)->nullable();
            $table->float('MaintenanceApproved', 10, 2)->nullable();
            $table->float('MaintenancePaid', 10, 2)->nullable();
            $table->float('DemolitionCost', 10, 2)->nullable();
            $table->float('DemolitionRequested', 10, 2)->nullable();
            $table->float('DemolitionApproved', 10, 2)->nullable();
            $table->float('DemolitionPaid', 10, 2)->nullable();
            $table->float('AdministrationCost', 10, 2)->nullable();
            $table->float('AdministrationRequested', 10, 2)->nullable();
            $table->float('AdministrationApproved', 10, 2)->nullable();
            $table->float('AdministrationPaid', 10, 2)->nullable();
            $table->float('AcquisitionCost', 10, 2)->nullable();
            $table->float('AcquisitionRequested', 10, 2)->nullable();
            $table->float('AcquisitionApproved', 10, 2)->nullable();
            $table->float('AcquisitionPaid', 10, 2)->nullable();
            $table->float('NIPLoanPayoffCost', 10, 2)->nullable();
            $table->float('NIPLoanPayoffRequested', 10, 2)->nullable();
            $table->float('NIPLoanPayoffApproved', 10, 2)->nullable();
            $table->float('NIPLoanPayoffPaid', 10, 2)->nullable();
            $table->float('TotalCost', 10, 2)->nullable();
            $table->float('TotalRequested', 10, 2)->nullable();
            $table->float('TotalApproved', 10, 2)->nullable();
            $table->float('TotalPaid' , 10, 2)->nullable();
            $table->text('ProcessDate')->nullable();
            $table->float('Retainage', 10, 2)->nullable();
            $table->integer('RetainagePaid')->nullable();
            $table->text('ReturnedFundsExplanation')->nullable();
            $table->float('ProgramIncome', 10, 2)->nullable();
            $table->float('NetProceeds', 10, 2)->nullable();
            $table->float('RecapturedOwed', 10, 2)->nullable();
            $table->tinyInteger('RecapturePaid')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_reimbursements');
    }
}

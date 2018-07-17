<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateApprovalTypesForDispositionHfa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //DB::table('approval_types')->truncate();
        
        $types = [
            // array('approval_type_name'=>'Disposition', 'table_name'=>'dispositions'),
            // array('approval_type_name'=>'Reimbursement Request', 'table_name'=>'reimbursement_requests'),
            // array('approval_type_name'=>'Reimbursement PO', 'table_name'=>'reimbursement_purchase_orders'),
            // array('approval_type_name'=>'Reimbursement Invoice', 'table_name'=>'reimbursement_invoices'),
            // array('approval_type_name'=>'Recapture Invoice', 'table_name'=>'recapture_invoices'),
            // array('approval_type_name'=>'Historic Waiver', 'table_name'=>''),
            // array('approval_type_name'=>'Target Area Amendment', 'table_name'=>''),
            // array('approval_type_name'=>'Reimbursement Invoice HFA Primary', 'table_name'=>'reimbursement_invoices'),
            // array('approval_type_name'=>'Reimbursement Invoice HFA Secondary', 'table_name'=>'reimbursement_invoices'),
            // ('approval_type_name'=>'Reimbursement Invoice HFA Tertiary', 'table_name'=>'reimbursement_invoices'),
            ['approval_type_name'=>'Disposition HFA', 'table_name'=>'dispositions']
            

        ];
        DB::table('approval_types')->insert($types);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

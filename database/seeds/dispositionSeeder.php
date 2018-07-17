<?php

use Illuminate\Database\Seeder;

class DispositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $approvalActionTypesData = array(
              array('approval_action_name'=>'Approve'),
              array('approval_action_name'=>'Corrections Needed'),
              array('approval_action_name'=>'Release'),
              array('approval_action_name'=>'Decline')
        );
        DB::table('approval_action_types')->insert($approvalActionTypesData);

        $approvalTypesData = array(
              array('approval_type_name'=>'Disposition', 'table_name'=>'dispositions'),
              array('approval_type_name'=>'Reimbursement Request', 'table_name'=>'reimbursement_requests'),
              array('approval_type_name'=>'Reimbursement PO', 'table_name'=>'reimbursement_purchase_orders'),
              array('approval_type_name'=>'Reimbursement Invoice', 'table_name'=>'reimbursement_invoices'),
              array('approval_type_name'=>'Recapture Invoice', 'table_name'=>'recapture_invoices'),
              array('approval_type_name'=>'Historic Waiver', 'table_name'=>''),
              array('approval_type_name'=>'Target Area Amendment', 'table_name'=>'')
        );
        DB::table('approval_types')->insert($approvalTypesData);
    }
}

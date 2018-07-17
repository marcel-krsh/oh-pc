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
        $approvalActionTypesData = [
              ['approval_action_name'=>'Approve'],
              ['approval_action_name'=>'Corrections Needed'],
              ['approval_action_name'=>'Release'],
              ['approval_action_name'=>'Decline']
        ];
        DB::table('approval_action_types')->insert($approvalActionTypesData);

        $approvalTypesData = [
              ['approval_type_name'=>'Disposition', 'table_name'=>'dispositions'],
              ['approval_type_name'=>'Reimbursement Request', 'table_name'=>'reimbursement_requests'],
              ['approval_type_name'=>'Reimbursement PO', 'table_name'=>'reimbursement_purchase_orders'],
              ['approval_type_name'=>'Reimbursement Invoice', 'table_name'=>'reimbursement_invoices'],
              ['approval_type_name'=>'Recapture Invoice', 'table_name'=>'recapture_invoices'],
              ['approval_type_name'=>'Historic Waiver', 'table_name'=>''],
              ['approval_type_name'=>'Target Area Amendment', 'table_name'=>'']
        ];
        DB::table('approval_types')->insert($approvalTypesData);
    }
}

<?php

use Illuminate\Database\Seeder;

class PropertyStatusOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $propertyStatusOptionData = array(
            array(
                'option_name' => 'LEGACY: Pending',
                'for'=>'landbank',
                'order'=>40

            ),
            array(
                'option_name' => 'LEGACY: Approved',
                'for'=>'landbank',
                'order'=>41
            ),
            array(
                'option_name' => 'LEGACY: Withdrawn',
                'for'=>'landbank',
                'order'=>42
            ),
            array(
                'option_name' => 'LEGACY: Declined',
                'for'=>'landbank',
                'order'=>43
            ),
            array(
                'option_name' => 'Internal - On Hold',
                'for'=>'landbank',
                'order'=>20
            ),
            array(
                'option_name' => 'Internal - Ready for Signature',
                'for'=>'landbank',
                'order'=>20
            ),
            array(
                'option_name' => 'Internal - Ready for Submission',
                'for'=>'landbank',
                'order'=>22
            ),
            array(
                'option_name' => 'Requested Reimbursement From HFA',
                'for'=>'landbank',
                'order'=>23
            ),
            array(
                'option_name' => 'Corrections Requested From HFA',
                'for'=>'landbank',
                'order'=>24
            ),
            array(
                'option_name' => 'Reimbursement Request Approved By HFA',
                'for'=>'landbank',
                'order'=>25
            ),
            array(
                'option_name' => 'Reimbursement Request Declined By HFA',
                'for'=>'landbank',
                'order'=>26
            ),
            array(
                'option_name' => 'Reimbursement Request Withdrawn',
                'for'=>'landbank',
                'order'=>27
            ),
            array(
                'option_name' => 'Invoiced to HFA',
                'for'=>'landbank',
                'order'=>28
            ),
            array(
                'option_name' => 'Paid by HFA',
                'for'=>'landbank',
                'order'=>29
            ),
            array(
                'option_name' => 'Disposition Requested to HFA',
                'for'=>'landbank',
                'order'=>30
            ),
            array(
                'option_name' => 'Disposition Approved by HFA',
                'for'=>'landbank',
                'order'=>30
            ),
            array(
                'option_name' => 'Disposition Released by HFA',
                'for'=>'landbank',
                'order'=>30
            ),
            array(
                'option_name' => 'Disposition Declined by HFA',
                'for'=>'landbank',
                'order'=>30
            ),
            array(
                'option_name' => 'Repayment Required',
                'for'=>'landbank',
                'order'=>30
            ),
            array(
                'option_name' => 'Repayment Paid to HFA',
                'for'=>'landbank',
                'order'=>30
            ),
            array(
                'option_name' => 'Compliance Review',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Processing',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Corrections Requested',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Ready for Signators',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Reimbursement Denied',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Reimbursement Approved',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Invoice Received',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Paid',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Requested',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Approved',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Invoiced',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Paid',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Released',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Repayment Required',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Repayment Invoiced',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Repayment Received',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Withdrawn',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Declined',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Unsubmitted',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'PO Sent',
                'for'=>'hfa',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Invoice Due',
                'for'=>'landbank',
                'order'=>20
            ),
            array(
                'option_name' => 'Disposition Paid',
                'for'=>'landbank',
                'order'=>20
            ),
            array(
                'option_name' => 'Imported - Needs Validated',
                'for'=>'landbank',
                'order'=>1
            ),
            array(
                'option_name' => 'Imported - Unable to Validate',
                'for'=>'landbank',
                'order'=>2
            ),
            array(
                'option_name' => 'Imported - Needs Documents',
                'for'=>'landbank',
                'order'=>3
            ),
            array(
                'option_name' => 'Imported - Needs Costs',
                'for'=>'landbank',
                'order'=>4
            )
        ,
            array(
                'option_name' => 'Internal - Corrections Needed',
                'for'=>'landbank',
                'order'=>21
            ),
            array(
                'option_name' => 'Disposition Submitted for Internal Approval',
                'for'=>'landbank',
                'order'=>22
            ),

        );

        DB::table('property_status_options')->insert($propertyStatusOptionData);
    }
}

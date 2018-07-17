<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ParcelSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // remove unused fields in parcels table
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn('unit-approval');
            $table->dropColumn('mobile-home-approval');
            $table->dropColumn('documents-to-review');
            $table->dropColumn('missing-documents');
            $table->dropColumn('all-submitted-documents-declined');
            $table->dropColumn('cost-amounts-missing');
            $table->dropColumn('request-amounts-missing');
            $table->dropColumn('request-amounts-invalid');
            $table->dropColumn('po-amounts-missing');
            $table->dropColumn('po-amounts-invalid');
            $table->dropColumn('invoice-amounts-missing');
            $table->dropColumn('invoice-amounts-invalid');
            $table->dropColumn('s1-lb-validated');
            $table->dropColumn('s1-has-cost-amounts');
            $table->dropColumn('s1-has-documents');
            $table->dropColumn('s1-has-request-amounts');
            $table->dropColumn('s2-added-to-a-request');
            $table->dropColumn('s2-request-approved');
            $table->dropColumn('s2-request-sent-to-hfa');
            $table->dropColumn('s3-hfa-validated');
            $table->dropColumn('s3-documents-approved');
            $table->dropColumn('s3-approved-amounts-added');
            $table->dropColumn('s3-approved-for-po');
            $table->dropColumn('s3-compliance-review');
            $table->dropColumn('s3-compliance-reviews-completed');
            $table->dropColumn('s3-po-approved');
            $table->dropColumn('s3-po-sent-to-lb');
            $table->dropColumn('s4-lb-created-invoice');
            $table->dropColumn('s4-lb-approved-invoice');
            $table->dropColumn('s4-lb-sent-invoice-to-hfa');
            $table->dropColumn('s5-invoice-approved-by-hfa-tier-1');
            $table->dropColumn('s5-invoice-approved-by-hfa-tier-2');
            $table->dropColumn('s5-invoice-approved-by-hfa-tier-3');
            $table->dropColumn('s5-invoice-paid-by-fiscal-agent');
            $table->dropColumn('has-retainages');
            $table->dropColumn('retainages-paid');
            $table->dropColumn('has-advances');
            $table->dropColumn('advances-paid');
        });

        // setup steps for parcels
        $step_types = array(
            array( // 2
                'name' => 'parcel'
            )
        );
        DB::table('guide_step_types')->insert($step_types);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guide_steps')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $guide_steps = array(
    // previous data
            array( // 1
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 1',
                'name_completed' => null,
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Complete form',
                'name_completed' => 'Form completed',
                'hfa' => 0,
                'step_help' => 'Save the disposition to mark the form as completed.'
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Upload supporting documents',
                'name_completed' => 'Supporting documents uploaded',
                'hfa' => 0,
                'step_help' => 'Upload documents for all required categories.'
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit for internal approval',
                'name_completed' => 'Submitted for internal approval',
                'hfa' => 0,
                'step_help' => 'Click the "Submit Disposition Request for Internal Approval" button when ready.'
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit to HFA',
                'name_completed' => 'Submitted to HFA',
                'hfa' => 0,
                'step_help' => 'Approve the disposition and submit to HFA for approval. This will complete step 1.'
            ),

            array( //6
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 2',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Confirm calculations',
                'name_completed' => 'Calculations confirmed',
                'hfa' => 1,
                'step_help' => 'Save the disposition to mark the calculations as confirmed.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Review supporting documents',
                'name_completed' => 'Supporting documents reviewed',
                'hfa' => 1,
                'step_help' => 'All supporting documents have been approved.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Approve request',
                'name_completed' => 'Request approved',
                'hfa' => 1,
                'step_help' => 'Click the Approve request button.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Notify Landbank',
                'name_completed' => 'Landbank notified',
                'hfa' => 1,
                'step_help' => 'Landbank will be notified automatically when the request is approved.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Request lien release',
                'name_completed' => 'Lien release requested',
                'hfa' => 1,
                'step_help' => 'Click the Request Release button.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Add to disposition invoice',
                'name_completed' => 'Added to disposition invoice',
                'hfa' => 1,
                'step_help' => 'The disposition will be automatically added to the current invoice upon approval.'
            ),

            array( //13
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 3',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Fiscal agent release lien',
                'name_completed' => 'Lien released by fiscal agent',
                'hfa' => 1,
                'step_help' => 'Release the disposition.'
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Begin sale of parcel',
                'name_completed' => 'Sale of parcel started',
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Finalize sale',
                'name_completed' => 'Sale/transfer finalized',
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Upload final executed release',
                'name_completed' => 'Final executed release uploaded',
                'hfa' => 0,
                'step_help' => 'Upload Final Executed Release'
            ),

            array( //18
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 4 HFA',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Approve invoice',
                'name_completed' => 'Invoice approved',
                'hfa' => 1,
                'step_help' => 'HFA approvers must approve the disposition invoice.'
            ),
            array(
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice',
                'name_completed' => 'Invoice sent',
                'hfa' => 1,
                'step_help' => 'The invoice must be sent to landbank for review.'
            ),

            array( //21
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 5 - (HFA)',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 21,
                'guide_step_type_id' => 1,
                'name' => 'Mark as paid',
                'name_completed' => 'Paid',
                'hfa' => 1,
                'step_help' => 'The invoice must be fully paid.'
            ),

    // new data
            array( // 23
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 1',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Validate Parcel',
                'hfa' => 0,
                'step_help' => 'Landbank must validate the parcel',
                'name_completed' => 'Parcel Validated'
            ),
            array(
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Enter Cost Amounts',
                'hfa' => 0,
                'step_help' => 'Cost amounts must be entered',
                'name_completed' => 'Cost Amounts Entered'
            ),
            array(
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Enter Request Amounts',
                'hfa' => 0,
                'step_help' => 'Landbank must enter request amounts',
                'name_completed' => 'Request Amounts Entered'
            ),
            array(
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Add Documents',
                'hfa' => 0,
                'step_help' => 'Supporting documents must be added',
                'name_completed' => 'Documents Added'
            ),


            array( // Step 2 // 28
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 2',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 28,
                'guide_step_type_id' => 2,
                'name' => 'Add to a Request',
                'hfa' => 0,
                'step_help' => 'Landbank must add this parcel to a request',
                'name_completed' => 'Added to a Request'
            ),
            array(
                'parent_id' => 28,
                'guide_step_type_id' => 2,
                'name' => 'Approve Request Internally',
                'hfa' => 0,
                'step_help' => 'Landbank approvers must approve the request',
                'name_completed' => 'Request Approved Internally'
            ),
            array(
                'parent_id' => 28,
                'guide_step_type_id' => 2,
                'name' => 'Send Request to HFA',
                'hfa' => 0,
                'step_help' => 'Landbank must send the request to HFA',
                'name_completed' => 'Request Sent to HFA'
            ),


            array( // Step 3 // 32
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 3',
                'hfa' => 1,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Validate Parcel Information',
                'hfa' => 1,
                'step_help' => 'HFA must validate the parcel information',
                'name_completed' => 'Parcel Information Validated'
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Review Documents',
                'hfa' => 1,
                'step_help' => 'HFA must approve all documents',
                'name_completed' => 'Documents Reviewed'
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Enter Approved Amounts',
                'hfa' => 1,
                'step_help' => 'HFA must enter approved amounts',
                'name_completed' => 'Approved Amounts Entered'
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Initial PO Approval',
                'hfa' => 1,
                'step_help' => 'PO must be approved before going through compliance review',
                'name_completed' => 'Initial PO Approval Completed'
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Complete Compliance Review',
                'hfa' => 1,
                'step_help' => 'Complete compliance review',
                'name_completed' => 'Compliance Review Completed'
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Final PO Approval',
                'hfa' => 1,
                'step_help' => 'Complete final PO approval',
                'name_completed' => 'PO Approved'
            ),
            array(
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Send PO to Land Bank',
                'hfa' => 1,
                'step_help' => 'Send PO to Land Bank',
                'name_completed' => 'PO Sent to Land Bank'
            ),

             array( // Step 3+ (any time after 3) //40
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Any point after step 3',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 40,
                'guide_step_type_id' => 2,
                'name' => 'LB: Upload Documented Payment',
                'hfa' => 0,
                'step_help' => 'Land Bank must upload the documented payment',
                'name_completed' => 'LB: Documented Payment Uploaded'
            ),
            array(
                'parent_id' => 40,
                'guide_step_type_id' => 2,
                'name' => 'HFA: Review Documentation',
                'hfa' => 1,
                'step_help' => 'HFA must review payment documentation',
                'name_completed' => 'HFA: Documentation Reviewed'
            ),
            array(
                'parent_id' => 40,
                'guide_step_type_id' => 2,
                'name' => 'HFA: Mark Paid',
                'hfa' => 1,
                'step_help' => 'HFA must mark the retainage as paid',
                'name_completed' => 'HFA: Retainage Paid'
            ),


            array( // Step 4 // 44
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 4',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 44,
                'guide_step_type_id' => 2,
                'name' => 'Create Invoice from PO',
                'hfa' => 0,
                'step_help' => 'Landbank must create an invoice from PO',
                'name_completed' => 'Invoice Created from PO'
            ),
            array(
                'parent_id' => 44,
                'guide_step_type_id' => 2,
                'name' => 'Approve the Invoice',
                'hfa' => 0,
                'step_help' => 'Landbank must approve the invoice',
                'name_completed' => 'Invoice Approved'
            ),
            array(
                'parent_id' => 44,
                'guide_step_type_id' => 2,
                'name' => 'Send Invoice to HFA',
                'hfa' => 0,
                'step_help' => 'The invoice must be sent to HFA',
                'name_completed' => 'Invoice Sent to HFA'
            ),


            array( // Step 5 // 48
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 5',
                'hfa' => 1,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Tier 1 Approve Invoice',
                'hfa' => 1,
                'step_help' => 'HFA tier 1 approvers must approve the invoice.',
                'name_completed' => 'Tier 1 Approved Invoice'
            ),
            array(
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Tier 2 Approve Invoice',
                'hfa' => 1,
                'step_help' => 'HFA tier 2 approvers must approve the invoice.',
                'name_completed' => 'Tier 2 Approved Invoice'
            ),
            array(
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Tier 3 Approve Invoice',
                'hfa' => 1,
                'step_help' => 'HFA tier 3 approvers must approve the invoice.',
                'name_completed' => 'Tier 3 Approved Invoice'
            ),
            array(
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Notify Fiscal Agent',
                'hfa' => 1,
                'step_help' => 'Fiscal agent must be notified',
                'name_completed' => 'Fiscal Agent Notified'
            ),


            array( // Step 6 // 53
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 6',
                'hfa' => 1,
                'step_help' => '',
                'name_completed' => ''
            ),
            array(
                'parent_id' => 53,
                'guide_step_type_id' => 2,
                'name' => 'Mark as Paid',
                'hfa' => 1,
                'step_help' => 'The invoice must be fully paid',
                'name_completed' => 'Invoice Paid'
            )
        );
        DB::table('guide_steps')->insert($guide_steps);
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

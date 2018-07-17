<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Updatestatusvsstep extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guide_steps', function (Blueprint $table) {
            //
            $table->integer('landbank_property_status_id')->nullable();
            $table->integer('hfa_property_status_id')->nullable();
            $table->integer('hidden_from_lb')->nullable();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guide_steps')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $guide_steps = [
    // previous data
            [ // 1
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 1',
                'name_completed' => null,
                'hfa' => 0,
                'step_help' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Complete form',
                'name_completed' => 'Form completed',
                'hfa' => 0,
                'step_help' => 'Save the disposition to mark the form as completed.',
                'landbank_property_status_id' => 51,
                'hfa_property_status_id' => 28,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Upload supporting documents',
                'name_completed' => 'Supporting documents uploaded',
                'hfa' => 0,
                'step_help' => 'Upload documents for all required categories.',
                'landbank_property_status_id' => 51,
                'hfa_property_status_id' => 28,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit for internal approval',
                'name_completed' => 'Submitted for internal approval',
                'hfa' => 0,
                'step_help' => 'Click the "Submit Disposition Request for Internal Approval" button when ready.',
                'landbank_property_status_id' => 49,
                'hfa_property_status_id' => 28,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit to HFA',
                'name_completed' => 'Submitted to HFA',
                'hfa' => 0,
                'step_help' => 'Approve the disposition and submit to HFA for approval. This will complete step 1.',
                'landbank_property_status_id' => 15,
                'hfa_property_status_id' => 29,
                'hidden_from_lb' => null
            ],

            [ //6
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 2',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Confirm calculations',
                'name_completed' => 'Calculations confirmed',
                'hfa' => 1,
                'step_help' => 'Save the disposition to mark the calculations as confirmed.',
                'landbank_property_status_id' => 15,
                'hfa_property_status_id' => 29,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Review supporting documents',
                'name_completed' => 'Supporting documents reviewed',
                'hfa' => 1,
                'step_help' => 'All supporting documents have been approved.',
                'landbank_property_status_id' => 15,
                'hfa_property_status_id' => 29,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Approve request',
                'name_completed' => 'Request approved',
                'hfa' => 1,
                'step_help' => 'Click the Approve request button.',
                'landbank_property_status_id' => 15,
                'hfa_property_status_id' => 30,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Notify Landbank',
                'name_completed' => 'Landbank notified',
                'hfa' => 1,
                'step_help' => 'Landbank will be notified automatically when the request is approved.',
                'landbank_property_status_id' => 16,
                'hfa_property_status_id' => 30,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Request lien release',
                'name_completed' => 'Lien release requested',
                'hfa' => 1,
                'step_help' => 'Click the Request Release button.',
                'landbank_property_status_id' => 52,
                'hfa_property_status_id' => 53,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Add to disposition invoice',
                'name_completed' => 'Added to disposition invoice',
                'hfa' => 1,
                'step_help' => 'The disposition will be automatically added to the current invoice upon approval.',
                'landbank_property_status_id' => 16,
                'hfa_property_status_id' => 53,
                'hidden_from_lb' => null
            ],

            [ //13
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 3',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Fiscal agent release lien',
                'name_completed' => 'Lien released by fiscal agent',
                'hfa' => 1,
                'step_help' => 'Release the disposition.',
                'landbank_property_status_id' => 17,
                'hfa_property_status_id' => 33,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Begin sale of parcel',
                'name_completed' => 'Sale of parcel started',
                'hfa' => 0,
                'step_help' => '',
                'landbank_property_status_id' => 17,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Finalize sale',
                'name_completed' => 'Sale/transfer finalized',
                'hfa' => 0,
                'step_help' => '',
                'landbank_property_status_id' => 17,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Upload final executed release',
                'name_completed' => 'Final executed release uploaded',
                'hfa' => 0,
                'step_help' => 'Upload Final Executed Release',
                'landbank_property_status_id' => 17,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],

            [ //18
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 4 HFA',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Approve invoice',
                'name_completed' => 'Invoice approved',
                'hfa' => 1,
                'step_help' => 'HFA approvers must approve the disposition invoice.',
                'landbank_property_status_id' => 17,
                'hfa_property_status_id' => 30,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice',
                'name_completed' => 'Invoice sent',
                'hfa' => 1,
                'step_help' => 'The invoice must be sent to landbank for review.',
                'landbank_property_status_id' => 41,
                'hfa_property_status_id' => 31,
                'hidden_from_lb' => null
            ],

            [ //21
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 5 - (HFA)',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 21,
                'guide_step_type_id' => 1,
                'name' => 'Mark as paid',
                'name_completed' => 'Paid',
                'hfa' => 1,
                'step_help' => 'The invoice must be fully paid.',
                'landbank_property_status_id' => 42,
                'hfa_property_status_id' => 32,
                'hidden_from_lb' => null
            ],
            [ // 23
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 1',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Validate Parcel',
                'hfa' => 0,
                'step_help' => 'Landbank must validate the parcel',
                'name_completed' => 'Parcel Validated',
                'landbank_property_status_id' => 46,
                'hfa_property_status_id' => 39,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Enter Cost Amounts',
                'hfa' => 0,
                'step_help' => 'Cost amounts must be entered',
                'name_completed' => 'Cost Amounts Entered',
                'landbank_property_status_id' => 45,
                'hfa_property_status_id' => 39,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Enter Request Amounts',
                'hfa' => 0,
                'step_help' => 'Landbank must enter request amounts',
                'name_completed' => 'Request Amounts Entered',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => 39,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 23,
                'guide_step_type_id' => 2,
                'name' => 'Add Documents',
                'hfa' => 0,
                'step_help' => 'Supporting documents must be added',
                'name_completed' => 'Documents Added',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => 39,
                'hidden_from_lb' => null
            ],


            [ // Step 2 // 28
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 2',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 28,
                'guide_step_type_id' => 2,
                'name' => 'Add to a Request',
                'hfa' => 0,
                'step_help' => 'Landbank must add this parcel to a request',
                'name_completed' => 'Added to a Request',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => 39,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 28,
                'guide_step_type_id' => 2,
                'name' => 'Approve Request',
                'hfa' => 0,
                'step_help' => 'Landbank approvers must approve the request',
                'name_completed' => 'Request Approved',
                'landbank_property_status_id' => 7,
                'hfa_property_status_id' => 39,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 28,
                'guide_step_type_id' => 2,
                'name' => 'Send Request to HFA',
                'hfa' => 0,
                'step_help' => 'Landbank must send the request to HFA',
                'name_completed' => 'Request Sent to HFA',
                'landbank_property_status_id' => 8,
                'hfa_property_status_id' => 22,
                'hidden_from_lb' => null
            ],


            [ // Step 3 // 32
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 3',
                'hfa' => 1,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Validate Parcel Information',
                'hfa' => 1,
                'step_help' => 'If the parcel information is valid, click the checkbox',
                'name_completed' => 'Parcel Information Validated',
                'landbank_property_status_id' => 8,
                'hfa_property_status_id' => 22,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Review Documents',
                'hfa' => 1,
                'step_help' => 'HFA must review all documents',
                'name_completed' => 'Documents Reviewed',
                'landbank_property_status_id' => 8,
                'hfa_property_status_id' => 22,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Enter Approved Amounts',
                'hfa' => 1,
                'step_help' => 'HFA must enter approved amounts',
                'name_completed' => 'Approved Amounts Entered',
                'landbank_property_status_id' => 8,
                'hfa_property_status_id' => 22,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'PO Ready for Compliance',
                'hfa' => 1,
                'step_help' => 'All parcels on the PO must be approved before starting compliance review',
                'name_completed' => 'PO Ready for Compliance',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 21,
                'hidden_from_lb' => 1
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Complete Compliance Review',
                'hfa' => 1,
                'step_help' => 'Complete compliance review',
                'name_completed' => 'Compliance Review Completed',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 24,
                'hidden_from_lb' => 1
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Final PO Approval',
                'hfa' => 1,
                'step_help' => 'Complete final PO approval',
                'name_completed' => 'PO Approved',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 26,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Send PO to Land Bank',
                'hfa' => 1,
                'step_help' => 'Send PO to Land Bank',
                'name_completed' => 'PO Sent to Land Bank',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 40,
                'hidden_from_lb' => null
            ],

             [ // Step 3+ (any time after 3) //40
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Any point after step 3',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 40,
                'guide_step_type_id' => 2,
                'name' => 'LB: Upload Documented Payment',
                'hfa' => 0,
                'step_help' => 'Land Bank must upload the documented payment',
                'name_completed' => 'LB: Documented Payment Uploaded',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 40,
                'guide_step_type_id' => 2,
                'name' => 'HFA: Review Documentation',
                'hfa' => 1,
                'step_help' => 'HFA must review payment documentation',
                'name_completed' => 'HFA: Documentation Reviewed',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 40,
                'guide_step_type_id' => 2,
                'name' => 'HFA: Mark Paid',
                'hfa' => 1,
                'step_help' => 'HFA must mark the retainage/advance as paid',
                'name_completed' => 'HFA: Retainage/Advance Paid',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],


            [ // Step 4 // 44
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 4',
                'hfa' => 0,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 44,
                'guide_step_type_id' => 2,
                'name' => 'Create Invoice from PO',
                'hfa' => 0,
                'step_help' => 'Landbank must create an invoice from PO',
                'name_completed' => 'Invoice Created from PO',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 40,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 44,
                'guide_step_type_id' => 2,
                'name' => 'Approve the Invoice',
                'hfa' => 0,
                'step_help' => 'Landbank must approve the invoice',
                'name_completed' => 'Invoice Approved',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 40,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 44,
                'guide_step_type_id' => 2,
                'name' => 'Send Invoice to HFA',
                'hfa' => 0,
                'step_help' => 'The invoice must be sent to HFA',
                'name_completed' => 'Invoice Sent to HFA',
                'landbank_property_status_id' => 13,
                'hfa_property_status_id' => 27,
                'hidden_from_lb' => null
            ],


            [ // Step 5 // 48
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 5',
                'hfa' => 1,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Tier 1 Approve Invoice',
                'hfa' => 1,
                'step_help' => 'HFA tier 1 approvers must approve the invoice.',
                'name_completed' => 'Tier 1 Approved Invoice',
                'landbank_property_status_id' => 13,
                'hfa_property_status_id' => 24,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Tier 2 Approve Invoice',
                'hfa' => 1,
                'step_help' => 'HFA tier 2 approvers must approve the invoice.',
                'name_completed' => 'Tier 2 Approved Invoice',
                'landbank_property_status_id' => 13,
                'hfa_property_status_id' => 24,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Tier 3 Approve Invoice',
                'hfa' => 1,
                'step_help' => 'HFA tier 3 approvers must approve the invoice.',
                'name_completed' => 'Tier 3 Approved Invoice',
                'landbank_property_status_id' => 13,
                'hfa_property_status_id' => 24,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 48,
                'guide_step_type_id' => 2,
                'name' => 'Notify Fiscal Agent',
                'hfa' => 1,
                'step_help' => 'Fiscal agent must be notified',
                'name_completed' => 'Fiscal Agent Notified',
                'landbank_property_status_id' => 13,
                'hfa_property_status_id' => 27,
                'hidden_from_lb' => null
            ],
            [ // Step 6 // 53
                'parent_id' => null,
                'guide_step_type_id' => 2,
                'name' => 'Step 6',
                'hfa' => 1,
                'step_help' => '',
                'name_completed' => '',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => null,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 53,
                'guide_step_type_id' => 2,
                'name' => 'Mark as Paid',
                'hfa' => 1,
                'step_help' => 'The invoice must be fully paid',
                'name_completed' => 'Invoice Paid',
                'landbank_property_status_id' => 14,
                'hfa_property_status_id' => 28,
                'hidden_from_lb' => null
            ],
            [
                'parent_id' => 32,
                'guide_step_type_id' => 2,
                'name' => 'Approve Parcel in PO',
                'hfa' => 1,
                'step_help' => 'This parcel must be approved (Breakouts, Checks and Actions)',
                'name_completed' => 'Parcel Approved in PO',
                'landbank_property_status_id' => 10,
                'hfa_property_status_id' => 22,
                'hidden_from_lb' => null
            ],
            //new data // id 56 for disposition
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Submit invoice for approval',
                'hfa' => 1,
                'step_help' => 'The disposition invoice must be submitted for approval.',
                'name_completed' => 'Disposition invoice submitted for approval',
                'landbank_property_status_id' => null,
                'hfa_property_status_id' => 30,
                'hidden_from_lb' => null
            ]
        ];
        DB::table('guide_steps')->insert($guide_steps);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guide_steps', function (Blueprint $table) {
            $table->dropColumn('landbank_property_status_id');
            $table->dropColumn('hfa_property_status_id');
            $table->dropColumn('hidden_from_lb');
        });
    }
}

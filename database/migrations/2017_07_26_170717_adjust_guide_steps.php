<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustGuideSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         Schema::table('guide_steps', function (Blueprint $table) {
            $table->string('name_completed')->nullable();
         });
         DB::table('guide_steps')->truncate();
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');

         $guide_steps = [
            [ // 1
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 1',
                'name_completed' => null,
                'hfa' => 0,
                'step_help' => ''
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Complete form',
                'name_completed' => 'Form completed',
                'hfa' => 0,
                'step_help' => 'Save the disposition to mark the form as completed.'
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Upload supporting documents',
                'name_completed' => 'Supporting documents uploaded',
                'hfa' => 0,
                'step_help' => 'Upload documents for all required categories.'
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit for internal approval',
                'name_completed' => 'Submitted for internal approval',
                'hfa' => 0,
                'step_help' => 'Click the "Submit Disposition Request for Internal Approval" button when ready.'
            ],
            [
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit to HFA',
                'name_completed' => 'Submitted to HFA',
                'hfa' => 0,
                'step_help' => 'Approve the disposition and submit to HFA for approval. This will complete step 1.'
            ],

            [ //6
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 2',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Confirm calculations',
                'name_completed' => 'Calculations confirmed',
                'hfa' => 1,
                'step_help' => 'Save the disposition to mark the calculations as confirmed.'
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Review supporting documents',
                'name_completed' => 'Supporting documents reviewed',
                'hfa' => 1,
                'step_help' => 'All supporting documents have been approved.'
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Approve request',
                'name_completed' => 'Request approved',
                'hfa' => 1,
                'step_help' => 'Click the Approve request button.'
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Notify Landbank',
                'name_completed' => 'Landbank notified',
                'hfa' => 1,
                'step_help' => 'Landbank will be notified automatically when the request is approved.'
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Request lien release',
                'name_completed' => 'Lien release requested',
                'hfa' => 1,
                'step_help' => 'Click the Request Release button.'
            ],
            [
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Add to disposition invoice',
                'name_completed' => 'Added to disposition invoice',
                'hfa' => 1,
                'step_help' => 'The disposition will be automatically added to the current invoice upon approval.'
            ],

            [ //13
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 3',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Fiscal agent release lien',
                'name_completed' => 'Lien released by fiscal agent',
                'hfa' => 1,
                'step_help' => 'Release the disposition.'
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Begin sale of parcel',
                'name_completed' => 'Sale of parcel started',
                'hfa' => 0,
                'step_help' => ''
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Finalize sale',
                'name_completed' => 'Sale/transfer finalized',
                'hfa' => 0,
                'step_help' => ''
            ],
            [
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Upload final executed release',
                'name_completed' => 'Final executed release uploaded',
                'hfa' => 0,
                'step_help' => 'Upload Final Executed Release'
            ],

            [ //18
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 4 HFA',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ],
            [
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Approve invoice',
                'name_completed' => 'Invoice approved',
                'hfa' => 1,
                'step_help' => 'HFA approvers must approve the disposition invoice.'
            ],
            [
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice',
                'name_completed' => 'Invoice sent',
                'hfa' => 1,
                'step_help' => 'The invoice must be sent to landbank for review.'
            ],

            [ //21
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 5 - (HFA)',
                'name_completed' => null,
                'hfa' => 1,
                'step_help' => ''
            ],
            [
                'parent_id' => 21,
                'guide_step_type_id' => 1,
                'name' => 'Mark as paid',
                'name_completed' => 'Paid',
                'hfa' => 1,
                'step_help' => 'The invoice must be fully paid.'
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
            $table->dropColumn('name_completed');
        });
    }
}

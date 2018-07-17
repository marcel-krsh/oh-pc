<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionGuide extends Migration
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
            //
            $table->string('step_help')->nullable();
        });

        DB::table('guide_steps')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $guide_steps = array(
            array( // 1
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 1',
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Complete form',
                'hfa' => 0,
                'step_help' => 'Save the disposition to mark the form as completed.'
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Upload supporting documents',
                'hfa' => 0,
                'step_help' => 'Upload documents for all required categories.'
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit for internal approval',
                'hfa' => 0,
                'step_help' => 'Click the "Submit Disposition Request for Internal Approval" button when ready.'
            ),
            array(
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit to HFA',
                'hfa' => 0,
                'step_help' => 'Approve the disposition and submit to HFA for approval. This will complete step 1.'
            ),

            array( //6
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 2',
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Confirm calculations',
                'hfa' => 1,
                'step_help' => 'Save the disposition to mark the calculations as confirmed.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Review supporting documents',
                'hfa' => 1,
                'step_help' => 'All supporting documents have been approved.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Approve request',
                'hfa' => 1,
                'step_help' => 'Click the Approve request button.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Notify Landbank',
                'hfa' => 1,
                'step_help' => 'Landbank will be notified automatically when the request is approved.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Request lien release',
                'hfa' => 1,
                'step_help' => 'Click the Request Release button.'
            ),
            array(
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Add to disposition invoice',
                'hfa' => 1,
                'step_help' => 'The disposition will be automatically added to the current invoice upon approval.'
            ),

            array( //13
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 3',
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Fiscal agent release lien',
                'hfa' => 1,
                'step_help' => 'Release the disposition.'
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Begin sale of parcel',
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Finalize sale',
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Upload final executed release',
                'hfa' => 0,
                'step_help' => ''
            ),

            array( //18
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 4 HFA',
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Review disposition',
                'hfa' => 1,
                'step_help' => 'Save the disposition to mark as completed.'
            ),
            array(
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Holly approval',
                'hfa' => 1,
                'step_help' => 'Holly must approve the disposition invoice.'
            ),
            array(
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Jim approval',
                'hfa' => 1,
                'step_help' => 'Jim must approve the disposition invoice.'
            ),
            array(
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice',
                'hfa' => 1,
                'step_help' => 'The invoice must be sent to landbank for review.'
            ),

            array( //23
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 5 LB',
                'hfa' => 0,
                'step_help' => ''
            ),
            array(
                'parent_id' => 23,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice payment',
                'hfa' => 0,
                'step_help' => 'Enter the date the payment was sent to HFA for processing.'
            ),

            array( //25
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 6 HFA',
                'hfa' => 1,
                'step_help' => ''
            ),
            array(
                'parent_id' => 25,
                'guide_step_type_id' => 1,
                'name' => 'Mark as paid',
                'hfa' => 1,
                'step_help' => 'The invoice must be fully paid.'
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
        Schema::table('guide_steps', function (Blueprint $table) {
            $table->dropColumn('step_help');
        });
    }
}

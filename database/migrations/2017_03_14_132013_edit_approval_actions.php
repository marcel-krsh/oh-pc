<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditApprovalActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('approval_actions', function (Blueprint $table) {
            $table->text('documents')->nullable();
        });

        DB::table('approval_action_types')->truncate();
        $new_action_types = array(
            array(
             'approval_action_name'=>'Approved',
             ),
            array(
             'approval_action_name'=>'Corrections needed',
             ),
            array(
             'approval_action_name'=>'Released',
             ),
            array(
             'approval_action_name'=>'Delined',
             ),
            array(
             'approval_action_name'=>'Approved by proxy',
             )
        );
        DB::table('approval_action_types')->insert($new_action_types);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('approval_actions', function (Blueprint $table) {
            $table->dropColumn('documents');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DispositionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('dispositions', function (Blueprint $table) {
            $table->float('transaction_cost', 10, 2)->nullable();
            $table->text('special_circumstance')->nullable();
            $table->integer('special_circumstance_id')->nullable();
            $table->text('full_description')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('permanent_parcel_id')->nullable();

            $table->tinyInteger('public_use_political')->default(0);
            $table->tinyInteger('public_use_community')->default(0);
            $table->tinyInteger('public_use_oneyear')->default(0);
            $table->tinyInteger('public_use_facility')->default(0);
            $table->tinyInteger('nonprofit_taxexempt')->default(0);
            $table->tinyInteger('nonprofit_community')->default(0);
            $table->tinyInteger('nonprofit_oneyear')->default(0);
            $table->tinyInteger('nonprofit_newuse')->default(0);
            $table->tinyInteger('dev_fmv')->default(0);
            $table->tinyInteger('dev_oneyear')->default(0);
            $table->tinyInteger('dev_newuse')->default(0);
            $table->tinyInteger('dev_purchaseag')->default(0);
            $table->tinyInteger('dev_taxescurrent')->default(0);
            $table->tinyInteger('dev_nofc')->default(0);

            $table->float('hfa_calc_income', 10, 2)->nullable();
            $table->float('hfa_calc_trans_cost', 10, 2)->nullable();
            $table->float('hfa_calc_maintenance_total', 10, 2)->nullable();
            $table->float('hfa_calc_monthly_rate', 10, 2)->nullable();
            $table->integer('hfa_calc_months')->nullable();
            $table->float('hfa_calc_maintenance_due', 10, 2)->nullable();
            $table->float('hfa_calc_demo_cost', 10, 2)->nullable();
            $table->float('hfa_calc_epi', 10, 2)->nullable();
            $table->float('hfa_calc_payback', 10, 2)->nullable();
            $table->float('hfa_calc_gain', 10, 2)->nullable();
        });
        
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->increments('id');
            // invoice, disposition, etc
            $table->integer('approval_type_id')->unsigned()->nullable();
            $table->foreign('approval_type_id')->references('id')->on('approval_types');
            // link to invoice, link to disposition, etc
            $table->integer('link_type_id')->unsigned()->nullable();
            // who needs to approve
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->dateTime('due_by')->nullable();
            $table->dateTime('seen_on')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('approval_type_name')->nullable();
            $table->string('table_name')->nullable();
        });

        Schema::create('approval_action_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('approval_action_name')->nullable();
        });

        Schema::create('approval_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_request_id')->unsigned()->nullable();
            $table->foreign('approval_request_id')->references('id')->on('approval_requests');
            $table->integer('approval_action_type_id')->unsigned()->nullable();
            $table->foreign('approval_action_type_id')->references('id')->on('approval_action_types');
            $table->text('note')->nullable();
            $table->timestamps();
        });
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
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropColumn('transaction_cost');
            $table->dropColumn('special_circumstance');
            $table->dropColumn('special_circumstance_id');
            $table->dropColumn('full_description');
            $table->dropColumn('status_id');
            $table->dropColumn('permanent_parcel_id');

            $table->dropColumn('public_use_political');
            $table->dropColumn('public_use_community');
            $table->dropColumn('public_use_oneyear');
            $table->dropColumn('public_use_facility');
            $table->dropColumn('nonprofit_taxexempt');
            $table->dropColumn('nonprofit_community');
            $table->dropColumn('nonprofit_oneyear');
            $table->dropColumn('nonprofit_newuse');
            $table->dropColumn('dev_fmv');
            $table->dropColumn('dev_oneyear');
            $table->dropColumn('dev_newuse');
            $table->dropColumn('dev_purchaseag');
            $table->dropColumn('dev_taxescurrent');
            $table->dropColumn('dev_nofc');

            $table->dropColumn('hfa_calc_income');
            $table->dropColumn('hfa_calc_trans_cost');
            $table->dropColumn('hfa_calc_maintenance_total');
            $table->dropColumn('hfa_calc_monthly_rate');
            $table->dropColumn('hfa_calc_months');
            $table->dropColumn('hfa_calc_maintenance_due');
            $table->dropColumn('hfa_calc_demo_cost');
            $table->dropColumn('hfa_calc_epi');
            $table->dropColumn('hfa_calc_payback');
            $table->dropColumn('hfa_calc_gain');
        });

        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_types');
        Schema::dropIfExists('approval_action_types');
        Schema::dropIfExists('approval_actions');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

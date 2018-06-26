<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLbApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lb_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->string('parcel_cost_approvers');
            $table->string('historic_parcel_approvers');
            $table->string('parcel_request_approvers');
            $table->string('purchase_order_approvers');
            $table->string('line_of_credit_approvers');
            $table->string('disposition_parcel_approvers');
            $table->string('disposition_invoice_approvers');
            $table->string('recapture_invoice_approvers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lb_approvals');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHfaApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hfa_approvals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->string('parcel_approvers');
            $table->string('request_approvers');
            $table->string('invoice_approvers');
            $table->string('payment_approvers');
            $table->string('disposition_parcel_approvers');
            $table->string('disposition_invoice_approvers');
            $table->string('recapture_parcel_approvers');
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
        Schema::dropIfExists('hfa_approvals');
    }
}

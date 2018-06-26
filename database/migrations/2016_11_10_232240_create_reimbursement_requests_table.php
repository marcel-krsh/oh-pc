<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sf_batch_id')->nullable();
            $table->integer('entity_id');
            $table->integer('program_id');
            $table->integer('account_id');
            $table->integer('status_id')->default(1);
            $table->tinyInteger('active');
            $table->timestamps();
            $table->index('entity_id');
            $table->index('program_id');
            $table->index('account_id');
            $table->index('status_id');
        });
        Schema::create('parcels_to_reimbursement_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parcel_id')->nullable();
            $table->integer('reimbursement_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reimbursement_requests');
        Schema::dropIfExists('parcels_to_reimbursement_requests');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrequalsToParcels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
           
            //
            $table->string('unit-approval')->default("NA");
            $table->string('mobile-home-approval')->default("NA");
            $table->integer('documents-to-review')->default("0");
            $table->integer('missing-documents')->default("0");
            $table->integer('all-submitted-documents-declined')->default("0");
            $table->integer('cost-amounts-missing')->default("0");
            $table->integer('request-amounts-missing')->default("0");
            $table->integer('request-amounts-invalid')->default("0");
            $table->integer('po-amounts-missing')->default("0");
            $table->integer('po-amounts-invalid')->default("0");
            $table->integer('invoice-amounts-missing')->default("0");
            $table->integer('invoice-amounts-invalid')->default("0");
            $table->integer('s1-lb-validated')->default("0");
            $table->integer('s1-has-cost-amounts')->default("0");
            $table->integer('s1-has-documents')->default("0");
            //$table->integer('s1-has-cost-amounts')->default("0");
            $table->integer('s1-has-request-amounts')->default("0");
            $table->integer('s2-added-to-a-request')->default("0");
            $table->integer('s2-request-approved')->default("0");
            $table->integer('s2-request-sent-to-hfa')->default("0");
            $table->integer('s3-hfa-validated')->default("0");
            $table->integer('s3-documents-approved')->default("0");
            $table->integer('s3-approved-amounts-added')->default("0");
            $table->integer('s3-approved-for-po')->default("0");
            $table->integer('s3-compliance-review')->default("0");
            $table->integer('s3-compliance-reviews-completed')->default("0");
            $table->integer('s3-po-approved')->default("0");
            $table->integer('s3-po-sent-to-lb')->default("0");
            $table->integer('s4-lb-created-invoice')->default("0");
            $table->integer('s4-lb-approved-invoice')->default("0");
            $table->integer('s4-lb-sent-invoice-to-hfa')->default("0");
            $table->integer('s5-invoice-approved-by-hfa-tier-1')->default("0");
            $table->integer('s5-invoice-approved-by-hfa-tier-2')->default("0");
            $table->integer('s5-invoice-approved-by-hfa-tier-3')->default("0");
            $table->integer('s5-invoice-paid-by-fiscal-agent')->default("0");
            $table->integer('has-retainages')->default("0");
            $table->integer('retainages-paid')->default("0");
            $table->integer('has-advances')->default("0");
            $table->integer('advances-paid')->default("0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            //
        });
    }
}

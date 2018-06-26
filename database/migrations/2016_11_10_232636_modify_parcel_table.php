<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyParcelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->integer('sf_batch_id')->nullable();
            $table->integer('parcel_hfa_status_id')->default(39);
            $table->integer('parcel_landbank_status_id')->default(5);
            $table->string('sf_parcel_id')->collate('utf8_bin')->nullable();
            $table->string('sf_program_name')->nullable();
            $table->index('parcel_hfa_status_id');
            $table->index('parcel_landbank_status_id');
            $table->index('entity_id');
            $table->index('account_id');
            $table->index('program_id');
            $table->integer('program_rules_id')->default(1);
            $table->boolean('lb_validated')->default(0);
            $table->boolean('hfa_validated')->default(0);
            $table->boolean('lb_documents_complete')->default(0);
            $table->boolean('hfa_documents_complete')->default(0);
        });
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

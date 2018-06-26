<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditComplianceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('compliances', function (Blueprint $table) {
            //
            $table->integer('parcel_hfa_status_id')->nullable();        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compliances', function (Blueprint $table) {
            $table->dropColumn('parcel_hfa_status_id');
        });
    }
}

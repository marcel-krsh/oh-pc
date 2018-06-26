<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusExplinationsToParcelsTable extends Migration
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
            $table->string('landbank_property_status_id_explanation')->nullable();
            $table->string('hfa_property_status_id_explanation')->nullable();
            if (Schema::hasColumn('parcel_hfa_status_id', 'parcel_landbank_status_id')){
                $table->dropColumn('parcel_hfa_status_id');
                $table->dropColumn('parcel_landbank_status_id');
            }
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
            $table->dropColumn('landbank_property_status_id_explanation');
            $table->dropColumn('hfa_property_status_id_explanation');
        });
    }
}

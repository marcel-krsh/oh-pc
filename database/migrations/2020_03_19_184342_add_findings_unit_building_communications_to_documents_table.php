<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFindingsUnitBuildingCommunicationsToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            //
            $table->json('building_ids')->nullable();
            $table->json('unit_ids')->nullable();
            $table->json('site_ids')->nullable();
            $table->json('communication_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            //
            //
            $table->dropColumn('building_ids');
            $table->dropColumn('unit_ids');
            $table->dropColumn('site_ids');
            $table->dropColumn('communication_ids');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComplianceSoftDelete extends Migration
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
            $table->softDeletes();
            $table->string('property_pass_notes')->nullable();
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
            $table->dropColumn('property_pass_notes');
        });
    }
}

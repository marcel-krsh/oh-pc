<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountColumnsToCachedAudits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cached_audits', function (Blueprint $table) {
            //
            $table->integer('file_findings_count')->nullable();
            $table->integer('unresolved_file_findings_count')->nullable();
            $table->integer('nlt_findings_count')->nullable();
            $table->integer('unresolved_nlt_findings_count')->nullable();
            $table->integer('lt_findings_count')->nullable();
            $table->integer('unresolved_lt_findings_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cached_audits', function (Blueprint $table) {
            //
        });
    }
}

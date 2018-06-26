<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdvanceDesignationBreakouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_items', function (Blueprint $table) {
            $table->tinyInteger('advance')->nullable();  
        });
        Schema::table('request_items', function (Blueprint $table) {
            $table->tinyInteger('advance')->nullable();       
        });
        Schema::table('po_items', function (Blueprint $table) {
            $table->tinyInteger('advance')->nullable();        
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->tinyInteger('advance')->nullable();              
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cost_items', function (Blueprint $table) {
            $table->dropColumn('advance');
        });
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn('advance');
        });
        Schema::table('po_items', function (Blueprint $table) {
            $table->dropColumn('advance');
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('advance');
        });
    }
}

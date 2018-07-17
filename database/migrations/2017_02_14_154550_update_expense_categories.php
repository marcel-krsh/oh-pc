<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateExpenseCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->string('color_hex')->nullable();
            $table->string('color_a')->nullable();
            $table->string('trans_color_hex')->nullable();
            $table->string('trans_color_a')->nullable();
            $table->string('advance_color_hex')->nullable();
            $table->string('advance_color_a')->nullable();
            $table->string('advance_trans_color_hex')->nullable();
            $table->string('advance_trans_color_a')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('color_hex');
            $table->dropColumn('color_a');
            $table->dropColumn('trans_color_hex');
            $table->dropColumn('trans_color_a');
            $table->dropColumn('advance_color_hex');
            $table->dropColumn('advance_color_a');
            $table->dropColumn('advance_trans_color_hex');
            $table->dropColumn('advance_trans_color_a');
        });
    }
}

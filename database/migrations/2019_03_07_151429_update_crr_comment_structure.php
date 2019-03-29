<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrrCommentStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('crr_comments', function (Blueprint $table) {
            $table->integer('crr_part_id');
            $table->integer('last_completed_by_id');
            $table->integer('last_declined_by_id');
            $table->timestamp('last_completed_date');
            $table->timestamp('last_declined_date');
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

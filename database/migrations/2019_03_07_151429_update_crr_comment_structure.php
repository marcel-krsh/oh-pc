<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        Schema::create('crr_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crr_part_id');
            $table->integer('crr_report_id');
            $table->integer('audit_id');
            $table->integer('author_id');
            $table->integer('version');
            $table->integer('last_completed_by_id')->nullable();
            $table->integer('last_declined_by_id')->nullable();
            $table->integer('last_approved_by_id')->nullable();
            $table->timestamp('last_completed_date')->nullable();
            $table->timestamp('last_declined_date')->nullable();
            $table->timestamp('last_approved_date')->nullable();
            $table->boolean('completed')->nullable();
            $table->boolean('declined')->nullable();
            $table->boolean('approved')->nullable();
            $table->timestamps();
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

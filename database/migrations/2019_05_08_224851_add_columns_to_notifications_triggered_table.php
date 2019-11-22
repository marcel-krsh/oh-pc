<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToNotificationsTriggeredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications_triggered', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable();
            $table->smallInteger('sent_count')->nullable();
            $table->string('model')->nullable();
            $table->integer('model_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications_triggered', function (Blueprint $table) {
            $table->dropColumn('sent_at');
            $table->dropColumn('sent_count');
            $table->dropColumn('model');
            $table->dropColumn('model_id');
        });
    }
}

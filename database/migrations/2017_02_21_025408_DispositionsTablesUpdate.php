<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DispositionsTablesUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('approval_actions', function (Blueprint $table) {
            $table->dropForeign('approval_actions_approval_request_id_foreign');
            $table->foreign('approval_request_id')
                    ->references('id')
                    ->on('approval_requests')
                    ->onDelete('cascade');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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

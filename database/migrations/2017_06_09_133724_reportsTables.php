<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');                             // "export_parcels"
            $table->string('folder')->nullable();               // "export/parcels"
            $table->string('filename')->nullable();             // filename.xls
            $table->tinyInteger('pending_request')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->integer('user_id')->unsigned()->nullable(); // requestor
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('report_downloads', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('report_id')->unsigned()->nullable();
            $table->foreign('report_id')->references('id')->on('reports');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');

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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_downloads');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

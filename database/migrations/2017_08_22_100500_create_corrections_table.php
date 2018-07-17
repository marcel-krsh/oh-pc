<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorrectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corrections', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uid');
            $table->integer('parcel_id');
            $table->integer('user_id');
            $table->integer('site_visit_id');
            $table->string('notes', 2000);
            $table->date('recorded_date');
            $table->boolean('corrected')->default(0)->nullable();
            $table->integer('corrected_site_visit_id')->nullable();
            $table->integer('corrected_user_id')->nullable();
            $table->date('corrected_date')->nullable();
            $table->boolean('deleted')->default(0);
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
        Schema::dropIfExists('corrections');
    }
}

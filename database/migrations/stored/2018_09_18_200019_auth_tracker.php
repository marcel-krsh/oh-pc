<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthTracker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('auth_tracker', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token'); // provided with user_id after user logged in Devco
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->tinyInteger('tries')->default(1)->nullable(); // count the number of tries within 5 minutes of created_at
            $table->dateTime('blocked_until')->nullable(); // if not null, set the date and time user stops being blocked
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('auth_tracker');
    }
}

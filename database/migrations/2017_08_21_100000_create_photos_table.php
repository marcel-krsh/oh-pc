<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uid');
            $table->integer('parcel_id');
            $table->integer('user_id');
            $table->date('recorded_date');
            $table->integer('site_visit_id')->nullable();
            $table->string('notes',1000);
            $table->decimal('latitude',9,7);
            $table->decimal('longitude',10,7);
            $table->integer('correction_id')->nullable();
            $table->integer('comment_id')->nullable();
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
        Schema::dropIfExists('photos');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrrPartTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crr_part_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('method_name')->nullable();
            $table->longtext('content')->nullable();
            $table->json('data')->nullable();
            $table->string('blade')->nullable();
            $table->boolean('active');
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
        Schema::dropIfExists('crr_part_types');
    }
}

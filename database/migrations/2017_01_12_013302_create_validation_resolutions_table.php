<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValidationResolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validation_resolutions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('parcel_id')->unsigned();
            $table->string('resolution_type');
            $table->integer('resolution_id')->unsigned();
            $table->string('resolution_lb_notes')->nullable();
            $table->string('resolution_system_notes')->nullable();
            $table->string('resolution_hfa_notes')->nullable();
            $table->tinyInteger('lb_resolved')->default(0);
            $table->dateTime('lb_resolved_at')->nullable();
            $table->tinyInteger('hfa_resolved')->default(0);
            $table->dateTime('hfa_resolved_at')->nullable();
            $table->tinyInteger('requires_hfa_resolution')->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('validation_resolutions');
    }
}

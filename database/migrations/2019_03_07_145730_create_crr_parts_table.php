<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrrPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crr_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('audit_id');
            $table->integer('crr_report_id');
            $table->integer('crr_section_id');
            $table->integer('crr_part_type_id');
            $table->string('title');
            $table->text('description');
            $table->json('data');
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
        Schema::dropIfExists('crr_parts');
    }
}

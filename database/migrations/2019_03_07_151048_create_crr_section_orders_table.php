<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrrSectionOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crr_section_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('audit_id');
            $table->integer('crr_report_id');
            $table->integer('crr_section_id');
            $table->integer('order');
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
        Schema::dropIfExists('crr_section_orders');
    }
}

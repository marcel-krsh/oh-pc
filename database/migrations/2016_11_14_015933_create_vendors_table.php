<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->timestamps();
            $table->string('vendor_name');
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone')->nullable();
            $table->string('vendor_mobile_phone')->nullable();
            $table->string('vendor_fax')->nullable()->nullable();
            $table->string('vendor_street_address')->nullable();
            $table->string('vendor_street_address2')->nullable();
            $table->string('vendor_city')->nullable();
            $table->string('vendor_state_id')->nullable();
            $table->string('vendor_zip')->nullable();
            $table->string('vendor_duns')->nullable();
            $table->tinyInteger('passed_sam_gov')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->text('vendor_notes')->nullable();
            $table->index('id');
            $table->index('vendor_name');
            $table->index('vendor_duns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispositions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('sf_parcel_id')->nullable();
            $table->integer('entity_id');
            $table->integer('program_id');
            $table->integer('account_id');
            $table->integer('parcel_id');
            $table->integer('disposition_type_id');
            $table->string('disposition_explanation')->nullable();
            $table->timestamp('release_date')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->float('program_income', 10, 2)->nullable();
            $table->timestamps();
            $table->index('entity_id');
            $table->index('program_id');
            $table->index('account_id');
            $table->index('parcel_id');
            $table->index('disposition_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispositions');
    }
}

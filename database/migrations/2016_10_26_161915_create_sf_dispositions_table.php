<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSfDispositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sf_dispositions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('PropertyID')->collate('utf8_bin')->nullable();
            $table->text('DispositionType')->nullable();
            $table->text('DispositionExplanation')->nullable();
            $table->float('MaintenanceRecaptureDue', 10, 2)->nullable();
            $table->text('MaintenanceRecaptureDueDate')->nullable();
            $table->integer('MaintenanceRepaid')->nullable();
            $table->integer('RetainageAmount')->nullable();
            $table->text('CreatedDate')->nullable();
            $table->text('ReleaseDate')->nullable();
            $table->text('LastModifiedDate')->nullable();
            $table->text('Status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sf_dispositions');
    }
}

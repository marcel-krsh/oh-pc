<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateFinancialTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('financial_types')) {
            Schema::create('financial_types', function (Blueprint $table) {
                $table->integer('id');
                $table->integer('financial_type_key')->nullable();
                $table->string('financial_type_name', 255)->nullable();
                $table->timestamp('last_edited', 3)->nullable();
                $table->nullableTimestamps();

                $table->primary('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_types');
    }
}

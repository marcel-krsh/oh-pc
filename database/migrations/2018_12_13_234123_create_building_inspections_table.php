<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1)
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateBuildingInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('building_inspections')) {
            Schema::create('building_inspections', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('building_id')->nullable();
                $table->unsignedInteger('building_key')->nullable();
                $table->unsignedInteger('audit_id')->nullable();
                $table->unsignedInteger('audit_key')->nullable();
                $table->unsignedInteger('project_id')->nullable();
                $table->unsignedInteger('project_key')->nullable();
                $table->unsignedInteger('pm_organization_id')->nullable();
                $table->text('auditors')->nullable();
                $table->integer('nlt_count')->nullable();
                $table->integer('lt_count')->nullable();
                $table->integer('followup_count')->nullable();
                $table->tinyInteger('complete')->nullable();
                $table->dateTime('submitted_date_time')->nullable();

                

                

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
        Schema::dropIfExists('building_inspections');
    }
}

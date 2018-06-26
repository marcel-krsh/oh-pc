<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteVisitStatusNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_visit_status_names', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name');
        });
        // Insert Values
        DB::table('site_visit_status_names')->insert(
            array(
                'name' => 'In Progress'
            )
        );
        // Insert Values
        DB::table('site_visit_status_names')->insert(
            array(
                'name' => 'Completed'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_visit_status_names');
    }
}

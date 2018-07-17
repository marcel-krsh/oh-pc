<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitListStatusNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_list_status_names', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name');
        });
        // Insert Values
        DB::table('visit_list_status_names')->insert(
            array(
                'name' => 'In Progress'
            )
        );
        // Insert Values
        DB::table('visit_list_status_names')->insert(
            array(
                'name' => 'Completed'
            )
        );
        // Insert Values
        DB::table('visit_list_status_names')->insert(
            array(
                'name' => 'Canceled'
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
        Schema::dropIfExists('visit_list_status_names');
    }
}

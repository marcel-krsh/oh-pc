<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePropertyStatusOptionsForReleaseRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $propertyStatusOptionData = [
                [
                          'option_name' => 'Disposition Release Requested',
                          'for'=>'landbank',
                          'order'=>30
                ],
                 [
                          'option_name' => 'Disposition Release Requested',
                          'for'=>'hfa',
                          'order'=>20
                 ]
        ];
              
        DB::table('property_status_options')->insert($propertyStatusOptionData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

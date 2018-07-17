<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePropertyStatusOptionsForLbDispositionRequested extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $propertyStatusOptionData = array(
                array(
                          'option_name' => 'Disposition Submitted for Internal Approval',
                          'for'=>'landbank',
                          'order'=>30
                ),
                 array(
                          'option_name' => 'Disposition Declined',
                          'for'=>'hfa',
                          'order'=>20
                ),
                 array(
                          'option_name' => 'Disposition Draft',
                          'for'=>'landbank',
                          'order'=>29
                )
        );
              
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

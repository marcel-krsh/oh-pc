<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolesVendors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rolesData = array(
            
            array( //70
            'role_parent_id'=>1,
            'role_name'=>'ACCESS:: Vendors <i class="uk-icon-info" uk-tooltip="Access vendor statistics and information"></i>',
            'protected'=>1,
            'active'=>1
            )
        );
        DB::table('roles')->insert($rolesData);
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

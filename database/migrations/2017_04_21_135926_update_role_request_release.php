<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRoleRequestRelease extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rolesData = array(
            
            array( //28
            'role_parent_id'=>1,
            'role_name'=>'Notified of Lien Release Request',
            'protected'=>1,
            'active'=>1
            )
        );
        DB::table('roles')->insert($rolesData);


        //$userRolesData = array(
            
            // array(
            // 'role_id'=>22,
            // 'user_id'=>2
            // ),
            // array(
            // 'role_id'=>22,
            // 'user_id'=>1
            // )
        //);
        //DB::table('users_roles')->insert($userRolesData);
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDispositionRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rolesData = [
            
            [ //22
            'role_parent_id'=>1,
            'role_name'=>'Disposition Invoice Approver',
            'protected'=>1,
            'active'=>1
            ]
        ];
        DB::table('roles')->insert($rolesData);


        $userRolesData = [
            
            // array(
            // 'role_id'=>22,
            // 'user_id'=>2
            // ),
            // array(
            // 'role_id'=>22,
            // 'user_id'=>1
            // )
        ];
        DB::table('users_roles')->insert($userRolesData);
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

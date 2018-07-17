<?php

use Illuminate\Database\Seeder;

class AddSiteVisitRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $rolesData = array(
            array( //71
                'id'=>71,
                'role_parent_id'=>1,
                'role_name'=>'SITE VISIT:: Manages and Can Edit Site Visits',
                'protected'=>1,
                'active'=>1
            ),
            array( //72
                'id'=>72,
                'role_parent_id'=>1,
                'role_name'=>'SITE VISIT:: Conducts Site Visits',
                'protected'=>1,
                'active'=>1
            ),
            array( //73
                'id'=>73,
                'role_parent_id'=>1,
                'role_name'=>'SITE VISIT:: Can View Site Visits',
                'protected'=>1,
                'active'=>1
            ),
            array( //74
                'id'=>74,
                'role_parent_id'=>2,
                'role_name'=>'SITE VISIT:: Can View Site Visits',
                'protected'=>1,
                'active'=>1
            ),
            array( //75
                'id'=>75,
                'role_parent_id'=>2,
                'role_name'=>'SITE VISIT:: Can Notify HFA of Corrections',
                'protected'=>1,
                'active'=>1
            )
        );
        \Illuminate\Support\Facades\DB::table('roles')->insert($rolesData);

        $userRolesData = array(

            array(
                'role_id'=>71,
                'user_id'=>1
            ),
            array(
                'role_id'=>72,
                'user_id'=>2
            ),
            array(
                'role_id'=>73,
                'user_id'=>1
            ),
            array(
                'role_id'=>74,
                'user_id'=>1
            ),
            array(
                'role_id'=>75,
                'user_id'=>1
            ),array(
                'role_id'=>71,
                'user_id'=>1
            ),
            array(
                'role_id'=>72,
                'user_id'=>1
            ),
            array(
                'role_id'=>73,
                'user_id'=>1
            ),
            array(
                'role_id'=>74,
                'user_id'=>1
            ),
            array(
                'role_id'=>75,
                'user_id'=>1
            )
        );
        \Illuminate\Support\Facades\DB::table('users_roles')->insert($userRolesData);
    }
}

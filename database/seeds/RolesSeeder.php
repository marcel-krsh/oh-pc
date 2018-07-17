<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolesData = array(
            array(
                'role_parent_id'=>0,
                'role_name'=>'HFA Roles',
                'protected'=>1,
                'active'=>1
            ),
            array(
                'role_parent_id'=>0,
                'role_name'=>'Land Bank Roles',
                'protected'=>1,
                'active'=>1
            ),
            array(
                'role_parent_id'=>1,
                'role_name'=>'OHFA Admin',
                'protected'=>1,
                'active'=>1
            ),
            array(
                'role_parent_id'=>2,
                'role_name'=>'Landbank Admin',
                'protected'=>1,
                'active'=>1
            ),
            array( //5
                'role_parent_id'=>1,
                'role_name'=>'New Landbank Approver',
                'protected'=>1,
                'active'=>1
            ),
            array( //6
                'role_parent_id'=>2,
                'role_name'=>'New Member Approver',
                'protected'=>1,
                'active'=>1
            ),
            array( //7
                'role_parent_id'=>1,
                'role_name'=>'Disposition Approver',
                'protected'=>1,
                'active'=>1
            ),
            array( //8
                'role_parent_id'=>1,
                'role_name'=>'Lien Manager',
                'protected'=>1,
                'active'=>1
            ),
            array( //9
                'role_parent_id'=>1,
                'role_name'=>'Disposition Reviewer',
                'protected'=>1,
                'active'=>1
            ),
            array( //10
                'role_parent_id'=>2,
                'role_name'=>'Disposition Reviewer',
                'protected'=>1,
                'active'=>1
            ),
            array( //11
                'role_parent_id'=>2,
                'role_name'=>'Disposition Approver',
                'protected'=>1,
                'active'=>1
            ),
            array( //12
                'role_parent_id'=>2,
                'role_name'=>'Disposition Manager',
                'protected'=>1,
                'active'=>1
            )
        );
        DB::table('roles')->insert($rolesData);


        $rolesData = [
            [ //7
                'role_parent_id'=>1,
                'role_name'=>'Disposition Approver',
                'protected'=>1,
                'active'=>1
            ],
            [ //8
                'role_parent_id'=>1,
                'role_name'=>'Lien Manager',
                'protected'=>1,
                'active'=>1
            ],
            [ //9
                'role_parent_id'=>1,
                'role_name'=>'Disposition Reviewer',
                'protected'=>1,
                'active'=>1
            ],
            [ //10
                'role_parent_id'=>2,
                'role_name'=>'Disposition Reviewer',
                'protected'=>1,
                'active'=>1
            ],
            [ //11
                'role_parent_id'=>2,
                'role_name'=>'Disposition Approver',
                'protected'=>1,
                'active'=>1
            ],
            [ //12
                'role_parent_id'=>2,
                'role_name'=>'Disposition Manager',
                'protected'=>1,
                'active'=>1
            ]

            
        ];

        \Illuminate\Support\Facades\DB::table('roles')->insert($rolesData);
    }
}

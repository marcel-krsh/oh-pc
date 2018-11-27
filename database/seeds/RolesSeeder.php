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
        $rolesData = [
            [ 
                'role_parent_id'=>0,
                'role_name'=>'Property Manager',
                'protected'=>1,
                'active'=>1
            ],
            [ 
                'role_parent_id'=>0,
                'role_name'=>'Auditor',
                'protected'=>1,
                'active'=>1
            ],
            [ 
                'role_parent_id'=>0,
                'role_name'=>'Manager',
                'protected'=>1,
                'active'=>1
            ],
            [ 
                'role_parent_id'=>0,
                'role_name'=>'System Admin',
                'protected'=>1,
                'active'=>1
            ]
        ];

        \Illuminate\Support\Facades\DB::table('roles')->insert($rolesData);
    }
}

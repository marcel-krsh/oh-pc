<?php

use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        // roles
        // pm 1
        // auditor 2
        // manager 3
        // system admin 4

        $permissionRoleData = [
            [
                'role_id'=>1,
                'permission_id'=>1 // project_lookup
            ],
            [
                'role_id'=>1,
                'permission_id'=>2 // my_communications
            ],
            [
                'role_id'=>1,
                'permission_id'=>3 // findings_tab
            ],
            [
                'role_id'=>1,
                'permission_id'=>4 // project_details_tab
            ],
            [
                'role_id'=>1,
                'permission_id'=>5 // project_documents
            ],
            [
                'role_id'=>2,
                'permission_id'=>6 // property_lookup
            ],
            [
                'role_id'=>2,
                'permission_id'=>2 // my_communications
            ],
            [
                'role_id'=>2,
                'permission_id'=>7 // stats
            ],
            [
                'role_id'=>2,
                'permission_id'=>8 // audit view mine
            ],
            [
                'role_id'=>2,
                'permission_id'=>9 // audit_create_new
            ],
            [
                'role_id'=>2,
                'permission_id'=>10 // audit_pending
            ],
            [
                'role_id'=>2,
                'permission_id'=>11 // audit_in_progress
            ],
            [
                'role_id'=>2,
                'permission_id'=>12 // audit_pending_response
            ],
            [
                'role_id'=>2,
                'permission_id'=>13 // audit_completed
            ],
            [
                'role_id'=>2,
                'permission_id'=>14 // audit_approved
            ],
            [
                'role_id'=>2,
                'permission_id'=>3 // findings_tab
            ],
            [
                'role_id'=>2,
                'permission_id'=>15 // reports_tab
            ],
            [
                'role_id'=>2,
                'permission_id'=>16 // all_project_tabs
            ],
            [
                'role_id'=>2,
                'permission_id'=>17 // documents_shared
            ],
            [
                'role_id'=>2,
                'permission_id'=>18 // allita_pc_app
            ],
            [
                'role_id'=>3,
                'permission_id'=>19 // users_tab
            ],
            [
                'role_id'=>3,
                'permission_id'=>20 // stats_tab
            ],
            [
                'role_id'=>3,
                'permission_id'=>21 // management_tools
            ],
            [
                'role_id'=>4,
                'permission_id'=>22 // admin_tools
            ]
        ];

        \Illuminate\Support\Facades\DB::table('roles_and_permissions')->insert($permissionRoleData);
    }
}

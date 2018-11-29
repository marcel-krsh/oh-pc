<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsData = [
            [
                'permission_name'  => 'project_lookup',
                'permission_label' => 'Project Lookup',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'my_communications',
                'permission_label' => 'My Communications',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'findings_tab',
                'permission_label' => 'Findings Tab',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'project_details_tab',
                'permission_label' => 'Project Details Tab',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'project_documents',
                'permission_label' => 'Project Documents',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'property_lookup',
                'permission_label' => 'Property Lookup',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'stats',
                'permission_label' => 'Stats',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_view_mine',
                'permission_label' => 'My Audits',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_create_new',
                'permission_label' => 'Create New Audit',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_pending',
                'permission_label' => 'Audit Pending',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_in_progress',
                'permission_label' => 'Audit In Progress',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_pending_response',
                'permission_label' => 'Audit Pending Response',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_completed',
                'permission_label' => 'Audit Completed',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'audit_approved',
                'permission_label' => 'Audit Approved',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'reports_tab',
                'permission_label' => 'Reports Tab',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'all_project_tabs',
                'permission_label' => 'All Project Tabs',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'documents_shared',
                'permission_label' => 'Documents Shared',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'allita_pc_app',
                'permission_label' => 'Allita PC App',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'users_tab',
                'permission_label' => 'Users Tab',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'stats_tab',
                'permission_label' => 'Stats Tab',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'management_tools',
                'permission_label' => 'Management Tools',
                'for'              => '',
                'active'           => 1
            ],
            [
                'permission_name'  => 'admin_tools',
                'permission_label' => 'Admin Tools',
                'for'              => '',
                'active'           => 1
            ]
        ];

        DB::table('permissions')->insert($permissionsData);
    }
}

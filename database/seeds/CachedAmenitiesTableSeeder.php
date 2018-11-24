<?php

use Illuminate\Database\Seeder;

class CachedAmenitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $amenitiesSeeder = [
            [
                'id' => 1, 
                'audit_id' => 123,
                'project_id' => 123,
                'building_id' => 123221,
                'unit_id' => 123,
                'status' => 'action-needed',
                'name' => 'Stair #1', 
                'auditor_id' => 1,  // add
                'auditor_name' => 'Brian Greenwood',
                'auditor_initials' => 'BG',
                'auditor_color' => 'green',
                'finding_nlt_status' => 'action-needed',
                'finding_lt_status' => 'action-required',
                'finding_sd_status' => 'no-action',
                'finding_photo_status' => '',
                'finding_comment_status' => '',
                'finding_copy_status' => 'no-action',
                'finding_trash_status' => ''
            ],
            [
                'id' => 2, 
                'audit_id' => 123,
                'project_id' => 123,
                'building_id' => 123221,
                'unit_id' => 123,
                'status' => 'critical',
                'name' => 'Bedroom #1', 
                'auditor_id' => 1,  // add
                'auditor_name' => 'Brian Greenwood',
                'auditor_initials' => 'BG',
                'auditor_color' => 'yellow',
                'finding_nlt_status' => 'action-needed',
                'finding_lt_status' => 'action-required',
                'finding_sd_status' => 'no-action',
                'finding_photo_status' => '',
                'finding_comment_status' => '',
                'finding_copy_status' => 'no-action',
                'finding_trash_status' => ''
            ],
            [
                'id' => 3, 
                'audit_id' => 123,
                'project_id' => 123,
                'building_id' => 123221,
                'unit_id' => 123,
                'status' => 'in-progress',
                'name' => 'Bedroom #2', 
                'auditor_id' => 1,  // add
                'auditor_name' => 'Brian Greenwood',
                'auditor_initials' => 'BG',
                'auditor_color' => 'pink',
                'finding_nlt_status' => 'action-needed',
                'finding_lt_status' => 'action-required',
                'finding_sd_status' => 'no-action',
                'finding_photo_status' => '',
                'finding_comment_status' => '',
                'finding_copy_status' => 'no-action',
                'finding_trash_status' => ''
            ],
            [
                'id' => 4, 
                'audit_id' => 123,
                'project_id' => 123,
                'building_id' => 123221,
                'unit_id' => 123,
                'status' => 'in-progress',
                'name' => 'Bedroom #3', 
                'auditor_id' => 1,  // add
                'auditor_name' => 'Brian Greenwood',
                'auditor_initials' => 'BG',
                'auditor_color' => 'green',
                'finding_nlt_status' => 'action-needed',
                'finding_lt_status' => 'action-required',
                'finding_sd_status' => 'no-action',
                'finding_photo_status' => '',
                'finding_comment_status' => '',
                'finding_copy_status' => 'no-action',
                'finding_trash_status' => ''
            ]
        ];

        \Illuminate\Support\Facades\DB::table('cached_amenities')->insert($amenitiesSeeder);
    }
}

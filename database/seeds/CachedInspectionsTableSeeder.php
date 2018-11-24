<?php

use Illuminate\Database\Seeder;

class CachedInspectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inspectionsSeeder = [
            [
                'id' => 1, 
                'audit_id' => 123,
                'project_id' => 122,
                'building_id' => 123876,
                'unit_id' => null,

                'status' => 'critical',
                'address' => '123457 Silvegwood Street', 
                'city' => 'Columbus', 
                'state' => 'OH', 
                'zip' => '43219', 

                'auditors_json' => '[
                    {"name":"Brian Greenwood",
                    "initials":"BG",
                    "color":"green",
                    "status":"warning"},
                    {"name":"Another Name",
                    "initials":"AN",
                    "color":"blue",
                    "status":""}
                ]',
                'type' => 'building',
                'type_total' => 4,
                'type_text' => 'Building',
                'type_text_plural' => 'Buildings',
                'menu_json' => '[
                        {"name":"SITE AUDIT", "icon":"a-mobile-home", "status":"critical active", "style":"", "action":"site_audit"},
                        {"name":"FILE AUDIT", "icon":"a-folder", "status":"action-required", "style":"", "action": "file_audit"},
                        {"name":"MESSAGES", "icon":"a-envelope-incoming", "status":"action-needed", "style":"", "action":"messages"},
                        {"name":"SUBMIT", "icon":"a-avatar-star", "status":"in-progress", "style":"margin-top:30px;", "action":"submit"}
                ]'
            ]
        ];

        \Illuminate\Support\Facades\DB::table('cached_inspections')->insert($inspectionsSeeder);
    }
}

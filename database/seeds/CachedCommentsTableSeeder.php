<?php

use Illuminate\Database\Seeder;

class CachedCommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $commentsSeeder = [
            [
                'id' => 1, 
                'audit_id' => 123,
                'project_id' => 123,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => null,
                'type' => 'finding',
                'type_icon' => 'a-booboo',
                'type_text' => 'NLT',
                'status' => 'action-needed',
                'user_id' => 2,
                'user_name' => 'Holly Swisher',
                'user_json' => '{
                    "id":"2",
                    "name":"Holly Swisher",
                    "initials":"HS",
                    "color":"green"
                }',
                'content' => 'STAIR #1: Finding Description Goes here and continues here for when it is long.',
                'finding_type' => 'nlt',
                'document_id' => null,
                'document_json' => '',
                'photos_json' => '',
                'followup_date' => null,
                'followup_assigned_id' => null,
                'followup_assigned_name' => '',
                'followup_actions_json' => '',
                'actions_json' => '[
                	{ "name":"FOLLOW UP", "icon":"a-calendar-pencil", "action":"followup"},
                	{ "name":"COMMENT", "icon":"a-comment-text", "action":"comment"},
                	{ "name":"DOCUMENT", "icon":"a-file-clock", "action":"document"},
                	{ "name":"PHOTO", "icon":"a-picture", "action":"photo"},
                ]',
                'created_at' => '2018-12-22 12:51:38'
            ],
            [
                'id' => 2, 
                'audit_id' => 123,
                'project_id' => 123,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => 1,
                'type' => 'comment',
                'type_icon' => 'a-comment-text',
                'type_text' => 'COMMENT',
                'status' => '',
                'user_id' => 2,
                'user_name' => 'Holly Swisher',
                'user_json' => '{
                    "id":"2",
                    "name":"Holly Swisher",
                    "initials":"HS",
                    "color":"green"
                }',
                'content' => 'Comment goes here and is italicised to show that it is a comment and not a finding.',
                'finding_type' => 'nlt',
                'document_id' => null,
                'document_json' => '',
                'photos_json' => '',
                'followup_date' => null,
                'followup_assigned_id' => null,
                'followup_assigned_name' => '',
                'followup_actions_json' => '',
                'actions_json' => '[
                	{ "name":"FOLLOW UP", "icon":"a-calendar-pencil", "action":"followup"},
                	{ "name":"COMMENT", "icon":"a-comment-text", "action":"comment"},
                	{ "name":"DOCUMENT", "icon":"a-file-clock", "action":"document"},
                	{ "name":"PHOTO", "icon":"a-picture", "action":"photo"},
                ]',
                'created_at' => '2018-12-31 12:51:38'
            ],
        ];

        \Illuminate\Support\Facades\DB::table('cached_comments')->insert($commentsSeeder);
    }
}

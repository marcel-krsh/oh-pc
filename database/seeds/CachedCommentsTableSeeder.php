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
                'finding_id' => 1,
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
                	{ "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-22 12:51:38'
            ],
            [
                'id' => 2, 
                'audit_id' => 123,
                'project_id' => 123,
                'finding_id' => 1,
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
                    { "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-31 12:51:38'
            ],
            [
                'id' => 3, 
                'audit_id' => 123,
                'project_id' => 123,
                'finding_id' => 1,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => null,
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
                'content' => 'This is a comment without a finding.',
                'finding_type' => null,
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
                    { "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-12 12:51:38'
            ],
            [
                'id' => 4, 
                'audit_id' => 123,
                'project_id' => 123,
                'finding_id' => 1,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => 1,
                'type' => 'photo',
                'type_icon' => 'a-picture',
                'type_text' => 'PHOTO',
                'status' => '',
                'user_id' => 2,
                'user_name' => 'Holly Swisher',
                'user_json' => '{
                    "id":"2",
                    "name":"Holly Swisher",
                    "initials":"HS",
                    "color":"green"
                }',
                'content' => 'This is a comment on a set of photos attached to a finding.',
                'finding_type' => null,
                'document_id' => null,
                'document_json' => '',
                'photos_json' => '[
                        {"id":"22", "url":"http://fpoimg.com/420x300", "commentscount":"2"},
                        {"id":"23", "url":"http://fpoimg.com/420x300", "commentscount":"1"},
                        {"id":"24", "url":"http://fpoimg.com/420x300", "commentscount":"0"},
                        {"id":"25", "url":"http://fpoimg.com/420x300", "commentscount":"0"},
                        {"id":"26", "url":"http://fpoimg.com/420x300", "commentscount":"0"},
                        {"id":"27", "url":"http://fpoimg.com/420x300", "commentscount":"0"}
                    ]',
                'followup_date' => null,
                'followup_assigned_id' => null,
                'followup_assigned_name' => '',
                'followup_actions_json' => '',
                'actions_json' => '[
                    { "name":"FOLLOW UP", "icon":"a-calendar-pencil", "action":"followup"},
                    { "name":"COMMENT", "icon":"a-comment-text", "action":"comment"},
                    { "name":"DOCUMENT", "icon":"a-file-clock", "action":"document"},
                    { "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-12 12:51:38'
            ],
            [
                'id' => 5, 
                'audit_id' => 123,
                'project_id' => 123,
                'finding_id' => 1,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => 1,
                'type' => 'file',
                'type_icon' => 'a-file-left',
                'type_text' => 'FILE',
                'status' => 'action-required',
                'user_id' => 2,
                'user_name' => 'Holly Swisher',
                'user_json' => '{
                    "id":"2",
                    "name":"Holly Swisher",
                    "initials":"HS",
                    "color":"green"
                }',
                'content' => 'This is a comment on a document attached to a finding.',
                'finding_type' => null,
                'document_id' => null,
                'document_json' => '{
                    "categories":[
                        {"id":"1", "name":"Category Name 1", "status":"checked"},
                        {"id":"2", "name":"Category Name 2", "status":"checked"},
                        {"id":"3", "name":"Category Name 3", "status":"notchecked"},
                        {"id":"4", "name":"Category Name 4", "status":""}
                    ],
                    "file": {
                        "id":"1", 
                        "name":"my_long-filename.pdf", 
                        "url":"#", 
                        "type":"pdf", 
                        "size":"1.3"
                    }
                }',
                'photos_json' => '',
                'followup_date' => null,
                'followup_assigned_id' => null,
                'followup_assigned_name' => '',
                'followup_actions_json' => '',
                'actions_json' => '[
                    { "name":"FOLLOW UP", "icon":"a-calendar-pencil", "action":"followup"},
                    { "name":"COMMENT", "icon":"a-comment-text", "action":"comment"},
                    { "name":"DOCUMENT", "icon":"a-file-clock", "action":"document"},
                    { "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-12 12:51:38'
            ],
            [
                'id' => 6, 
                'audit_id' => 123,
                'project_id' => 123,
                'finding_id' => 1,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => 1,
                'type' => 'followup',
                'type_icon' => 'a-bell-plus',
                'type_text' => 'FOLLOW UP',
                'status' => 'action-needed',
                'user_id' => 2,
                'user_name' => 'Holly Swisher',
                'user_json' => '{
                    "id":"2",
                    "name":"Holly Swisher",
                    "initials":"HS",
                    "color":"green"
                }',
                'content' => 'This is a follow up attached to a finding.',
                'finding_type' => null,
                'document_id' => null,
                'document_json' => '',
                'photos_json' => '',
                'followup_date' => '2018-12-22',
                'followup_assigned_id' => 1,
                'followup_assigned_name' => 'PM name here',
                'followup_actions_json' => '',
                'actions_json' => '[
                    { "name":"COMMENT", "icon":"a-comment-text", "action":"comment"},
                    { "name":"DOCUMENT", "icon":"a-file-clock", "action":"document"},
                    { "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-12 12:51:38'
            ],
            [
                'id' => 7, 
                'audit_id' => 123,
                'project_id' => 123,
                'finding_id' => 1,
                'building_id' => 123221,
                'unit_id' => 123,
                'parent_id' => null,
                'type' => 'photo',
                'type_icon' => 'a-picture',
                'type_text' => 'PHOTO',
                'status' => '',
                'user_id' => 2,
                'user_name' => 'Holly Swisher',
                'user_json' => '{
                    "id":"2",
                    "name":"Holly Swisher",
                    "initials":"HS",
                    "color":"green"
                }',
                'content' => 'This is a comment on a set of photos attached to a finding.',
                'finding_type' => null,
                'document_id' => null,
                'document_json' => '',
                'photos_json' => '[
                        {"id":"22", "url":"http://fpoimg.com/420x300", "commentscount":"2"},
                        {"id":"23", "url":"http://fpoimg.com/420x300", "commentscount":"1"},
                        {"id":"24", "url":"http://fpoimg.com/420x300", "commentscount":"0"},
                        {"id":"25", "url":"http://fpoimg.com/420x300", "commentscount":"0"},
                        {"id":"26", "url":"http://fpoimg.com/420x300", "commentscount":"0"},
                        {"id":"27", "url":"http://fpoimg.com/420x300", "commentscount":"0"}
                    ]',
                'followup_date' => null,
                'followup_assigned_id' => null,
                'followup_assigned_name' => '',
                'followup_actions_json' => '',
                'actions_json' => '[
                    { "name":"FOLLOW UP", "icon":"a-calendar-pencil", "action":"followup"},
                    { "name":"COMMENT", "icon":"a-comment-text", "action":"comment"},
                    { "name":"DOCUMENT", "icon":"a-file-clock", "action":"document"},
                    { "name":"PHOTO", "icon":"a-picture", "action":"photo"}
                ]',
                'created_at' => '2018-12-12 12:51:38'
            ]
        ];

        \Illuminate\Support\Facades\DB::table('cached_comments')->insert($commentsSeeder);
    }
}

<?php

use Illuminate\Database\Seeder;

class CachedBuildingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingsSeeder = [
            [
                'id' => '123', 
                'audit_id' => '123',
                'status' => 'critical',
                'amenity_id' => null,
                'type' => 'building',
                'type_text' => 'Building',
                'type_text_plural' => 'Buildings',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" }
				]',
            ],
            [
                'id' => '345', 
                'audit_id' => '111',
                'status' => 'action-needed',
                'amenity_id' => 122,
                'type' => 'pool',
                'type_text' => '',
                'type_text_plural' => '',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street 1',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[]',
            ],
            [
                'id' => '12333', 
                'audit_id' => '12344',
                'status' => 'in-progress',
                'amenity_id' => null,
                'type' => 'building',
                'type_text' => 'Building',
                'type_text_plural' => 'Buildings',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street2',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" }
				]',
            ],
            [
                'id' => '123876', 
                'audit_id' => '123',
                'status' => 'ok-actionable',
                'amenity_id' => null,
                'type' => 'building',
                'type_text' => 'Building',
                'type_text_plural' => 'Buildings',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" }
				]',
            ],
            [
                'id' => '12399', 
                'audit_id' => '123',
                'status' => '',
                'amenity_id' => null,
                'type' => 'building',
                'type_text' => 'Building',
                'type_text_plural' => 'Buildings',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" }
				]',
            ],
            [
                'id' => '999', 
                'audit_id' => '123',
                'status' => '',
                'amenity_id' => 133,
                'type' => 'pool',
                'type_text' => '',
                'type_text_plural' => '',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[]',
            ],
            [
                'id' => '999888', 
                'audit_id' => '123',
                'status' => '',
                'is_amenity' => 1,
                'type' => 'pool',
                'type_text' => '',
                'type_text_plural' => '',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '',
            ],
            [
                'id' => '123221', 
                'audit_id' => '123',
                'status' => 'critical',
                'type' => 'building',
                'amenity_id' => null,
                'type_text' => 'Building',
                'type_text_plural' => 'Buildings',
                'type_total' => '4',
                'program_total' => '2',
                'followup_date' => '2018-12-10',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
                'finding_total' => '3',
                'finding_file_status' => 'action-needed',
                'finding_nlt_status' => 'in-progress',
                'finding_lt_status' => 'in-progress',
                'finding_sd_status' => 'action-required',
                'finding_file_total' => '3',
                'finding_file_completed' => '0',
                'finding_nlt_total' => '5',
                'finding_nlt_completed' => '2',
                'finding_lt_total' => '3',
                'finding_lt_completed' => '0',
                'finding_sd_total' => '5',
                'finding_sd_completed' => '0',
                'auditors_json' => '[
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					},
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					},
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					},
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					},
                	{
						"id": "1",
						"name": "Brian Greenwood",
						"initials": "BG",
						"color": "green",
						"status": "alert"
					},{
						"id": "2",
						"name": "Brian Greenwood 2",
						"initials": "BF",
						"color": "blue",
						"status": ""
					}
				]',
                'amenities_json' => '[
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" },
	                { "type": "Elevator", "qty": "2", "status": "pending" },
	                { "type": "ADA", "qty": null, "status": "inspected" },
	                { "type": "Floors", "qty": "2", "status": "pending" },
	                { "type": "Common Areas", "qty": "2", "status": "action" },
	                { "type": "Fitness Room", "qty": "1", "status": "pending" }
				]',
            ]
        ];

        \Illuminate\Support\Facades\DB::table('cached_buildings')->insert($buildingsSeeder);
    }
}

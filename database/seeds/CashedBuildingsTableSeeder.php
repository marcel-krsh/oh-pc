<?php

use Illuminate\Database\Seeder;

class CashedBuildingsTableSeeder extends Seeder
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
                'type' => 'building',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
                'type' => 'pool',
                'address' => '123457 Silvegwood Street 1',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
                'id' => '12333', 
                'audit_id' => '12344',
                'status' => 'in-progress',
                'type' => 'building',
                'address' => '123457 Silvegwood Street2',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
                'type' => 'building',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
                'type' => 'building',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
                'type' => 'pool',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
                'id' => '123221', 
                'audit_id' => '123',
                'status' => 'critical',
                'type' => 'building',
                'address' => '123457 Silvegwood Street',
                'city' => 'COLUMBUS',
                'state' => 'OH',
                'zip' => '43219',
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
                'areas_json' => '[
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
        ];

        \Illuminate\Support\Facades\DB::table('cached_buildings')->insert($buildingsSeeder);
    }
}

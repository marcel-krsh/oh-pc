<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;

class AuditController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
    	   Auth::onceUsingId(1); // TEST BRIAN
        }
    }

    public function buildingsFromAudit($audit, Request $request)
    {
    	$target = $request->get('target');
    	$buildings = collect([
    					[
    						'id' => 144, 
    						'status' => 'critical',
    						'street' => '123457 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action'],
                                ['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
                                ['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
                                ['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action'],
                                ['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
                                ['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
                                ['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 244, 
    						'status' => 'action-needed',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'pool',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 344, 
    						'status' => 'in-progress',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 444, 
    						'status' => 'ok-actionable',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 544, 
    						'status' => '',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 644, 
    						'status' => '',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'pool',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 744, 
    						'status' => '',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					]
    				]);

    	return view('dashboard.partials.audit_buildings', compact('audit', 'target', 'buildings'));
    }

    public function detailsFromBuilding($audit, $building, Request $request) {
    	$target = $request->get('target');
    	$targetaudit = $request->get('targetaudit');
    	$details = collect([
    						[
    						'id' => 1, 
    						'status' => 'critical',
    						'street' => '123457 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 2, 
    						'status' => 'action-needed',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'pool',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 3, 
    						'status' => 'in-progress',
    						'street' => '123466 Silvegwood Street', 
    						'city' => 'Columbus', 
    						'state' => 'OH', 
    						'zip' => '43219', 
    						'auditors' => [
    							['name' => 'Brian Greenwood',
    							'initials' => 'BG',
    							'color' => 'green',
    							'status' => 'warning'],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => ''],
    							['name' => 'Another Name',
    							'initials' => 'AN',
    							'color' => 'blue',
    							'status' => '']
    						],
    						'type' => 'building',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					]
    				]);

    	return view('dashboard.partials.audit_building_details', compact('audit', 'target', 'building', 'details', 'targetaudit'));
    }

    public function inspectionFromBuilding($audit_id, $building_id, Request $request) {
        $target = $request->get('target');
        $rowid = $request->get('rowid');
        $inspection = "test";
        $data['detail'] = collect([
                            'id' => 1, 
                            'status' => 'critical',
                            'street' => '123457 Silvegwood Street', 
                            'city' => 'Columbus', 
                            'state' => 'OH', 
                            'zip' => '43219', 
                            'auditors' => [
                                ['name' => 'Brian Greenwood',
                                'initials' => 'BG',
                                'color' => 'green',
                                'status' => 'warning'],
                                ['name' => 'Another Name',
                                'initials' => 'AN',
                                'color' => 'blue',
                                'status' => '']
                            ],
                            'type' => 'building',
                            'areas' => [
                                ['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
                                ['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
                                ['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
                            ]
                    ]);
        // ok-actionable, no-action, action-needed, action-required, in-progress, critical
        $data['menu'] = collect([
                            ['name' => 'SITE AUDIT', 'icon' => 'a-mobile-home', 'status' => 'critical active', 'style' => '', 'action' => 'site_audit'],
                            ['name' => 'FILE AUDIT', 'icon' => 'a-folder', 'status' => 'action-required', 'style' => '', 'action' => 'file_audit'],
                            ['name' => 'MESSAGES', 'icon' => 'a-envelope-incoming', 'status' => 'action-needed', 'style' => '', 'action' => 'messages'],
                            ['name' => 'FOLLOW UPS', 'icon' => 'a-bell-2', 'status' => 'no-action', 'style' => '', 'action' => 'followups'],
                            ['name' => 'SUBMIT', 'icon' => 'a-avatar-star', 'status' => 'in-progress', 'style' => 'margin-top:30px;', 'action' => 'submit'],
                    ]);
        $data['areas'] = collect([
                            [
                                'id' => 1, 
                                'status' => 'action-needed',
                                'name' => 'Stair #1', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'green',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 2, 
                                'status' => 'critical',
                                'name' => 'Bedroom #1', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'yellow',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ]
                    ]);
        return response()->json($data);
        //return view('dashboard.partials.audit_building_inspection', compact('audit_id', 'target', 'detail_id', 'building_id', 'detail', 'inspection', 'areas', 'rowid'));
    }

    public function inspectionFromBuildingDetail($audit_id, $building_id, $detail_id, Request $request) {
        $target = $request->get('target');
        $rowid = $request->get('rowid');
        $inspection = "test";
        $data['detail'] = collect([
                            'id' => 1, 
                            'status' => 'critical',
                            'street' => '123457 Silvegwood Street', 
                            'city' => 'Columbus', 
                            'state' => 'OH', 
                            'zip' => '43219', 
                            'auditors' => [
                                ['name' => 'Brian Greenwood',
                                'initials' => 'BG',
                                'color' => 'green',
                                'status' => 'warning'],
                                ['name' => 'Another Name',
                                'initials' => 'AN',
                                'color' => 'blue',
                                'status' => '']
                            ],
                            'type' => 'building',
                            'areas' => [
                                ['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
                                ['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
                                ['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
                                ['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
                            ]
                    ]);
        // ok-actionable, no-action, action-needed, action-required, in-progress, critical
        $data['menu'] = collect([
                            ['name' => 'SITE AUDIT', 'icon' => 'a-mobile-home', 'status' => 'critical active', 'style' => '', 'action' => 'site_audit'],
                            ['name' => 'FILE AUDIT', 'icon' => 'a-folder', 'status' => 'action-required', 'style' => '', 'action' => 'file_audit'],
                            ['name' => 'MESSAGES', 'icon' => 'a-envelope-incoming', 'status' => 'action-needed', 'style' => '', 'action' => 'messages'],
                            ['name' => 'FOLLOW UPS', 'icon' => 'a-bell-2', 'status' => 'no-action', 'style' => '', 'action' => 'followups'],
                            ['name' => 'SUBMIT', 'icon' => 'a-avatar-star', 'status' => 'in-progress', 'style' => 'margin-top:30px;', 'action' => 'submit']
                    ]);
        $data['areas'] = collect([
                            [
                                'id' => 1, 
                                'status' => 'action-needed',
                                'name' => 'Stair #1', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'green',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 2, 
                                'status' => 'critical',
                                'name' => 'Bedroom #1', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'yellow',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ],
                            [
                                'id' => 3, 
                                'status' => 'in-progress',
                                'name' => 'Bedroom #2', 
                                'auditor' => [
                                    'name' => 'Brian Greenwood',
                                    'initials' => 'BG',
                                    'color' => 'pink',
                                    'status' => 'warning'
                                ],
                                'findings' => [
                                    'nltstatus' => 'action-needed',
                                    'ltstatus' => 'action-required',
                                    'sdstatus' => 'no-action',
                                    'photostatus' => '',
                                    'commentstatus' => '',
                                    'copystatus' => 'no-action',
                                    'trashstatus' => ''
                                ]
                            ]
                    ]);
        return response()->json($data);
        //return view('dashboard.partials.audit_building_inspection', compact('audit_id', 'target', 'detail_id', 'building_id', 'detail', 'inspection', 'areas', 'rowid'));
    }

    public function getProject( $project=null) {
        return view('projects.project');
    }

    public function getProjectTitle ( $project = null ) {
        return '<i class="a-mobile-repeat"></i><i class="a-home-question"></i> <span class="list-tab-text"> PROJECT TAB :: CREATED DYNAMICALLY FROM CONTROLLER</span>';
    }
}
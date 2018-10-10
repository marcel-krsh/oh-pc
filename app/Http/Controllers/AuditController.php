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
        $context = $request->get('context');
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

    	return view('dashboard.partials.audit_buildings', compact('audit', 'target', 'buildings', 'context'));
    }

    public function detailsFromBuilding($audit, $building, Request $request) {
    	$target = $request->get('target');
    	$targetaudit = $request->get('targetaudit');
        $context = $request->get('context');
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

    	return view('dashboard.partials.audit_building_details', compact('audit', 'target', 'building', 'details', 'targetaudit', 'context'));
    }

    public function inspectionFromBuilding($audit_id, $building_id, Request $request) {
        $target = $request->get('target');
        $rowid = $request->get('rowid');
        $context = $request->get('context');
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
        $context = $request->get('context');
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

        $projectId = '19200114';

        $projectTabs = collect([
                ['title' => 'Details', 'icon' => 'a-clipboard', 'status' => '', 'badge' => '', 'action' => 'project.details'],
                ['title' => 'Communications', 'icon' => 'a-envelope-incoming', 'status' => '', 'badge' => '', 'action' => 'project.communications'],
                ['title' => 'Documents', 'icon' => 'a-file-clock', 'status' => '', 'badge' => '', 'action' => 'project.documents'],
                ['title' => 'Notes', 'icon' => 'a-file-text', 'status' => '', 'badge' => '', 'action' => 'project.notes'],
                ['title' => 'Comments', 'icon' => 'a-comment-text', 'status' => '', 'badge' => '', 'action' => 'project.comments'],
                ['title' => 'Photos', 'icon' => 'a-picture', 'status' => '', 'badge' => '', 'action' => 'project.photos'],
                ['title' => 'Findings', 'icon' => 'a-mobile-info', 'status' => '', 'badge' => '', 'action' => 'project.findings'],
                ['title' => 'Follow-ups', 'icon' => 'a-bell-ring', 'status' => '', 'badge' => '', 'action' => 'project.followups'],
                ['title' => 'Reports', 'icon' => 'a-file-chart-3', 'status' => '', 'badge' => '', 'action' => 'project.reports'],
            ]);
        $tab = 'project-detail-tab-1';

        return view('projects.project', compact('tab', 'projectTabs', 'projectId'));
    }

    public function getProjectTitle ( $project = null ) {
        return '<i class="a-mobile-repeat"></i><i class="a-home-question"></i> <span class="list-tab-text"> PROJECT TAB :: CREATED DYNAMICALLY FROM CONTROLLER</span>';
    }

    public function getProjectDetails ( $project = null ) {
        $stats = collect([
                "project_id" => "1920114",
                "project_name" => "The Garden Oaks",
                "last_audit_completed" => "December 12, 2017",
                "next_audit_due" => "December 31, 2018",
                "score_percentage" => "88%",
                "score" => "B-",
                "total_building" => "99",
                "total_building_common_areas" => "99",
                "total_project_common_areas" => "10",
                "total_units" => "9,999",
                "market_rate" => "8,999",
                "subsidized" => "1,000",
                "programs" => [
                    ["name" => "Program Name 1", "units" => "250"],
                    ["name" => "Program Name 2", "units" => "250"],
                    ["name" => "Program Name 3", "units" => "50"],
                    ["name" => "Program Name 4", "units" => "550"],
                    ["name" => "Program Name 5", "units" => "1000"],
                ]
            ]);
        $owner = collect([
                "name" => "Jane Doe Properties",
                "poc" => "Jane Doe",
                "phone" => "(123) 344-4444",
                "fax" => "(123) 448-8888",
                "email" => "bob@bob.com",
                "address" => "123 Sesame Street",
                "address2" => "Suite 123",
                "city" => "City",
                "state" => "State",
                "zip" => "12345",
            ]);
        $manager = collect([
                "name" => "The Really Long Named Property Manager Name",
                "poc" => "Bob Doe",
                "phone" => "(123) 344-3333",
                "fax" => "(123) 448-3333",
                "email" => "bob3@bob.com",
                "address" => "12333 Sesame Street",
                "address2" => "Suite 12345",
                "city" => "City2",
                "state" => "State2",
                "zip" => "22222",
            ]);
        return view('projects.partials.details', compact('stats', 'owner', 'manager'));
    }

    public function getProjectDetailsInfo ( $project, $type ) {
        // types: compliance, assignment, findings, followups, reports, documents, comments, photos
        // project: project_id?

        switch ($type) {
            case 'compliance':
                $data = collect([
                    "project" => [
                        'id' => 1
                    ],
                    "summary" => [
                        'required_unit_selected' => 0,
                        'inspectable_areas_assignment_needed' => 12,
                        'required_units_selection' => 13,
                        'file_audits_needed' => 14,
                        'physical_audits_needed' => 15,
                        'schedule_conflicts' => 16
                    ],
                    "programs" => [
                        ['id' => 1, 'name' => 'Program Name A'],
                        ['id' => 2, 'name' => 'Program Name B'],
                        ['id' => 3, 'name' => 'Program Name C'],
                        ['id' => 4, 'name' => 'Program Name D'],
                        ['id' => 5, 'name' => 'Program Name E'],
                        ['id' => 6, 'name' => 'Program Name F']
                    ]
                ]);
                break;
            case 'assignment':
                
                break;
            case 'findings':
                
                break;
            case 'followups':
                
                break;
            case 'reports':
                
                break;
            case 'documents':
                
                break;
            case 'comments':
                
                break;
            case 'photos':
                
                break;
            default:
               
        }

        return view('projects.partials.details-'.$type, compact('data'));
    }

    public function getProjectCommunications ( $project = null ) {
        return view('projects.partials.communications');
    }

    public function getProjectDocuments ( $project = null ) {
        return view('projects.partials.documents');
    }

    public function getProjectNotes ( $project = null ) {
        return view('projects.partials.notes');
    }

    public function getProjectComments ( $project = null ) {
        return view('projects.partials.comments');
    }

    public function getProjectPhotos ( $project = null ) {
        return view('projects.partials.photos');
    }

    public function getProjectFindings ( $project = null ) {
        return view('projects.partials.findings');
    }

    public function getProjectFollowups ( $project = null ) {
        return view('projects.partials.followups');
    }

    public function getProjectReports ( $project = null ) {
        return view('projects.partials.reports');
    }

    public function modalProjectProgramSummary( $project_id, $program_id ) {
        // units are automatically selected using the selection process
        // then randomize all units before displaying them on the modal
        // then user can adjust selection for that program
        $data = collect([
            'units' => [
                [
                    "id" => 1, 
                    "status" => "not-inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "" ]
                    ]
                ]
            ]
        ]);
        return view('modals.project-summary', compact('data'));
    }
}
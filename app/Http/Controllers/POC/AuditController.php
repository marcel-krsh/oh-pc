<?php

namespace App\Http\Controllers\POC;

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
    	Auth::onceUsingId(1); // TEST BRIAN
    }

    public function buildingsFromAudit($audit, Request $request)
    {
    	$target = $request->get('target');
    	$output = "testing";
    	$buildings = collect([
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
    						'type' => '',
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
    						'type' => '',
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
    						'type' => '',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 4, 
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
    						'type' => '',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 5, 
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
    						'type' => '',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 6, 
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
    						'type' => '',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					],
    					[
    						'id' => 7, 
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
    						'type' => '',
    						'areas' => [
    							['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    							['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    							['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    							['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    						]
    					]
    				]);

    	return view('poc.dashboard.partials.audit_buildings', compact('audit', 'target', 'buildings'));
    }
}
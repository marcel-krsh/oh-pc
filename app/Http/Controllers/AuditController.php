<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\OrderingBuilding;
use Auth;
use Session;
use App\LogConverter;
use Carbon;

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

        // check if user can see that audit
        // 

        // FILTER BUILDINGS BY AUDIT TBD
        // $buildings = CachedBuilding::get();

        // count buildings & count ordering_buildings
        if(OrderingBuilding::where('audit_id', '=', $audit)->where('user_id','=',Auth::user()->id)->count() == 0 && CachedBuilding::where('audit_id', '=', $audit)->count() != 0){

            // if ordering_buildings is empty, create a default entry for the ordering
            $buildings = CachedBuilding::where('audit_id','=',$audit)->orderBy('id','desc')->get();
            
            $i = 1;
            $new_ordering = array();

            foreach($buildings as $building){

                $ordering = new OrderingBuilding([
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $building->id,
                    'order' => $i
                ]);
                $ordering->save();
                $i++;

            }   

        }elseif(CachedBuilding::where('audit_id', '=', $audit)->count() != OrderingBuilding::where('audit_id', '=', $audit)->where('user_id','=',Auth::user()->id)->count() && CachedBuilding::where('audit_id', '=', $audit)->count() != 0){

            $buildings = null;

        }
        
        $buildings = OrderingBuilding::where('audit_id','=',$audit)->where('user_id','=',Auth::user()->id)->orderBy('order','asc')->with('building')->get();

        // query building using join ordering_building using that order
        

    	// $buildings = collect([
    	// 				[
    	// 					'id' => 144, 
    	// 					'status' => 'critical',
    	// 					'street' => '123457 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => 'alert']
    	// 					],
    	// 					'type' => 'building',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action'],
     //                            ['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
     //                            ['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
     //                            ['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
     //                            ['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
     //                            ['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action'],
     //                            ['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
     //                            ['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
     //                            ['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
     //                            ['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
     //                            ['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				],
    	// 				[
    	// 					'id' => 244, 
    	// 					'status' => 'action-needed',
    	// 					'street' => '123466 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => '']
    	// 					],
    	// 					'type' => 'pool',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				],
    	// 				[
    	// 					'id' => 344, 
     //                        'audit_id' => 123, // added
    	// 					'status' => 'in-progress',
    	// 					'street' => '123466 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => '']
    	// 					],
    	// 					'type' => 'building',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				],
    	// 				[
    	// 					'id' => 444, 
    	// 					'status' => 'ok-actionable',
    	// 					'street' => '123466 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => '']
    	// 					],
    	// 					'type' => 'building',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				],
    	// 				[
    	// 					'id' => 544, 
    	// 					'status' => '',
    	// 					'street' => '123466 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => '']
    	// 					],
    	// 					'type' => 'building',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				],
    	// 				[
    	// 					'id' => 644, 
    	// 					'status' => '',
    	// 					'street' => '123466 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => '']
    	// 					],
    	// 					'type' => 'pool',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				],
    	// 				[
    	// 					'id' => 744, 
    	// 					'status' => '',
    	// 					'street' => '123466 Silvegwood Street', 
    	// 					'city' => 'Columbus', 
    	// 					'state' => 'OH', 
    	// 					'zip' => '43219', 
    	// 					'auditors' => [
    	// 						['name' => 'Brian Greenwood',
    	// 						'initials' => 'BG',
    	// 						'color' => 'green',
    	// 						'status' => 'alert'],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => ''],
    	// 						['name' => 'Another Name',
    	// 						'initials' => 'AN',
    	// 						'color' => 'blue',
    	// 						'status' => '']
    	// 					],
    	// 					'type' => 'building',
    	// 					'areas' => [
    	// 						['type' => 'Elevators', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'ADA', 'qty' => null, 'status' => 'inspected'],
    	// 						['type' => 'Floors', 'qty' => 2, 'status' => 'pending'],
    	// 						['type' => 'Common Areas', 'qty' => 2, 'status' => 'inspected'],
    	// 						['type' => 'Fitness Room', 'qty' => 1, 'status' => 'action']
    	// 					]
    	// 				]
    	// 			]);

    	return view('dashboard.partials.audit_buildings', compact('audit', 'target', 'buildings', 'context'));
    }

    public function reorderBuildingsFromAudit($audit, Request $request) {
        $building = $request->get('building');
        $index = $request->get('index');

        // select all building orders except for the one we want to reorder
        $current_ordering = OrderingBuilding::where('audit_id','=',$audit)->where('user_id','=',Auth::user()->id)->where('building_id','!=',$building)->orderBy('order','asc')->get()->toArray();

        $inserted = array( [
                    'user_id' => Auth::user()->id,
                    'audit_id' => $audit,
                    'building_id' => $building,
                    'order' => $index
               ]);

        // insert the building ordering in the array
        $reordered_array = $current_ordering;
        array_splice( $reordered_array, $index, 0, $inserted );

        // delete previous ordering
        OrderingBuilding::where('audit_id','=',$audit)->where('user_id','=',Auth::user()->id)->delete();

        // clean-up the ordering and store
        foreach($reordered_array as $key => $ordering){
            $new_ordering = new OrderingBuilding([
                'user_id' => $ordering['user_id'],
                'audit_id' => $ordering['audit_id'],
                'building_id' => $ordering['building_id'],
                'order' => $key+1
            ]);
            $new_ordering->save();
        }

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
                        'schedule_conflicts' => 16,
                        'estimated' => '107:00',
                        'needed' => '27:00',
                    ],
                    'auditors' => [
                        [
                            'id' => 1,
                            'name' => 'Brian Greenwood',
                            'initials' => 'BG',
                            'color' => 'pink'
                        ],
                        [
                            'id' => 2,
                            'name' => 'Brianna Bluewood',
                            'initials' => 'BB',
                            'color' => 'blue'
                        ],
                        [
                            'id' => 3,
                            'name' => 'John Smith',
                            'initials' => 'JS',
                            'color' => 'black'
                        ],
                        [
                            'id' => 4,
                            'name' => 'Sarah Connor',
                            'initials' => 'SC',
                            'color' => 'red'
                        ]
                    ],
                    "days" => [
                        [
                            'id' => 6, 
                            'date' => '12/22/2018',
                            'status' => 'action-required',
                            'icon' => 'a-avatar-fail'
                        ],
                        [
                            'id' => 7, 
                            'date' => '12/23/2018',
                            'status' => 'ok-actionable',
                            'icon' => 'a-avatar-approve'
                        ]
                    ],
                    'projects' => [
                        [
                            'id' => '19200114',
                            'date' => '12/22/2018',
                            'name' => 'The Garden Oaks', 
                            'street' => '123466 Silvegwood Street', 
                            'city' => 'Columbus', 
                            'state' => 'OH', 
                            'zip' => '43219', 
                            'lead' => 2, // user_id
                            'schedules' => [
                                ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                            ]
                        ],
                        [
                            'id' => '19200115',
                            'date' => '12/22/2018',
                            'name' => 'The Garden Oaks 2', 
                            'street' => '123466 Silvegwood Street', 
                            'city' => 'Columbus', 
                            'state' => 'OH', 
                            'zip' => '43219', 
                            'lead' => 1, // user_id
                            'schedules' => [
                                ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                            ]
                        ],
                        [
                            'id' => '19200116',
                            'date' => '12/22/2018',
                            'name' => 'The Garden Oaks 3', 
                            'street' => '123466 Silvegwood Street', 
                            'city' => 'Columbus', 
                            'state' => 'OH', 
                            'zip' => '43219', 
                            'lead' => 2, // user_id
                            'schedules' => [
                                ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                                ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                            ]
                        ]
                    ]
                ]);
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

    public function getProjectDetailsAssignmentSchedule( $project, $dateid ) {

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
                'schedule_conflicts' => 16,
                'estimated' => '107:00',
                'needed' => '27:00',
            ],
            'auditors' => [
                [
                    'id' => 1,
                    'name' => 'Brian Greenwood',
                    'initials' => 'BG',
                    'color' => 'pink'
                ],
                [
                    'id' => 2,
                    'name' => 'Brianna Bluewood',
                    'initials' => 'BB',
                    'color' => 'blue'
                ],
                [
                    'id' => 3,
                    'name' => 'John Smith',
                    'initials' => 'JS',
                    'color' => 'black'
                ],
                [
                    'id' => 4,
                    'name' => 'Sarah Connor',
                    'initials' => 'SC',
                    'color' => 'red'
                ]
            ],
            "days" => [
                [
                    'id' => 6, 
                    'date' => '12/22/2018',
                    'status' => 'action-required',
                    'icon' => 'a-avatar-fail'
                ],
                [
                    'id' => 7, 
                    'date' => '12/23/2018',
                    'status' => 'ok-actionable',
                    'icon' => 'a-avatar-approve'
                ]
            ],
            'projects' => [
                [
                    'id' => '19200114',
                    'date' => '12/22/2018',
                    'name' => 'The Garden Oaks', 
                    'street' => '123466 Silvegwood Street', 
                    'city' => 'Columbus', 
                    'state' => 'OH', 
                    'zip' => '43219', 
                    'lead' => 2, // user_id
                    'schedules' => [
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                    ]
                ],
                [
                    'id' => '19200115',
                    'date' => '12/22/2018',
                    'name' => 'The Garden Oaks 2', 
                    'street' => '123466 Silvegwood Street', 
                    'city' => 'Columbus', 
                    'state' => 'OH', 
                    'zip' => '43219', 
                    'lead' => 1, // user_id
                    'schedules' => [
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                    ]
                ],
                [
                    'id' => '19200116',
                    'date' => '12/22/2018',
                    'name' => 'The Garden Oaks 3', 
                    'street' => '123466 Silvegwood Street', 
                    'city' => 'Columbus', 
                    'state' => 'OH', 
                    'zip' => '43219', 
                    'lead' => 2, // user_id
                    'schedules' => [
                        ['icon' => '', 'status' => '', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-checked', 'status' => 'ok-actionable', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 0, 'tooltip' =>'APPROVE SCHEDULE CONFLICT'],
                        ['icon' => 'a-circle-cross', 'status' => 'action-required', 'is_lead' => 1, 'tooltip' =>'APPROVE SCHEDULE CONFLICT']
                    ]
                ]
            ]
        ]);
        return view('projects.partials.details-assignment-schedule', compact('data'));
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

    public function modalProjectProgramSummaryFilterProgram( $project_id, $program_id, Request $request) {
        $programs = $request->get('programs');

        if(is_array($programs) && count($programs)>0){
            $filters = collect([
                'programs' => $programs
            ]);
        }else{
            $filters = null;
        }

        // query here

        $data = collect([
                'project' => [
                    "id" => 1,
                    "name" => "Project Name",
                    'selected_program' => $program_id
                ],
                'programs' => [
                    ["id" => 1, "name" => "Program Name 1"],
                    ["id" => 2, "name" => "Program Name 2"],
                    ["id" => 3, "name" => "Program Name 3"],
                    ["id" => 4, "name" => "Program Name 4"]
                ],
                'units' => [
                    [
                        "id" => 1, 
                        "status" => "not-inspectable", 
                        "address" => "123457 Silvegwood Street",
                        "address2" => "#102",
                        "move_in_date" => "1/29/2018",
                        "programs" => [
                            ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                            ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "not-inspectable" ]
                        ]
                    ],
                    [
                        "id" => 2, 
                        "status" => "inspectable", 
                        "address" => "123457 Silvegwood Street",
                        "address2" => "#102",
                        "move_in_date" => "1/29/2018",
                        "programs" => [
                            ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "not-inspectable" ],
                            ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "inspectable" ]
                        ]
                    ]
                ]
            ]);

        return view('dashboard.partials.project-summary-unit', compact('data'));
    }
    public function modalProjectProgramSummary($project_id, $program_id) {
        // units are automatically selected using the selection process
        // then randomize all units before displaying them on the modal
        // then user can adjust selection for that program

        $data = collect([
            'project' => [
                "id" => 1,
                "name" => "Project Name",
                'selected_program' => $program_id
            ],
            'programs' => [
                ["id" => 1, "name" => "Program Name 1"],
                ["id" => 2, "name" => "Program Name 2"],
                ["id" => 3, "name" => "Program Name 3"],
                ["id" => 4, "name" => "Program Name 4"]
            ],
            'units' => [
                [
                    "id" => 1, 
                    "status" => "not-inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "not-inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "not-inspectable" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "not-inspectable" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "not-inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "", "file_audit_checked" => "", "selected" => "", "status" => "inspectable" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "inspectable" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "inspectable" ]
                    ]
                ],
                [
                    "id" => 2, 
                    "status" => "inspectable", 
                    "address" => "123457 Silvegwood Street",
                    "address2" => "#102",
                    "move_in_date" => "1/29/2018",
                    "programs" => [
                        ["id" => 1, "name" => "Program name 1", "physical_audit_checked" => "true", "file_audit_checked" => "false", "selected" => "", "status" => "inspectable" ],
                        ["id" => 2, "name" => "Program name 2", "physical_audit_checked" => "false", "file_audit_checked" => "true", "selected" => "", "status" => "not-inspectable" ]
                    ]
                ]
            ]
        ]);
        
        return view('modals.project-summary', compact('data'));
    }

    public function addAssignmentAuditor($id, $orderby=null) {

        $data = collect([
            "project" => [
                "id" => $id,
                "name" => "Project Name"
            ],
            "summary" => [
                'date' => 'DECEMBER 22, 2018',
                'estimated' => '107:00',
                'needed' => '27:00'
            ],
            "auditors" => [
                [
                    "id" => 1,
                    "name" => "Jane Doe",
                    "status" => "ok-actionable",
                    "icon" => "a-circle-checked",
                    "icon_tooltip" => "CLICK TO REMOVE AUDITOR",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-home-marker",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 2,
                    "name" => "Jane Doe 2",
                    "status" => "",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "CLICK TO ADD AUDITOR",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-home-marker",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 3,
                    "name" => "Jane Doe 3",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 4,
                    "name" => "Jane Doe 4",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 5,
                    "name" => "Jane Doe 5",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 6,
                    "name" => "Jane Doe 6",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ],
                [
                    "id" => 7,
                    "name" => "Jane Doe 7",
                    "status" => "action-required",
                    "icon" => "a-circle-plus",
                    "icon_tooltip" => "THIS AUDITOR WILL REQUIRE CONFLICT APPROVAL BY LEAD",
                    "availability" => "Available 8:30 AM - 6:00 PM",
                    "open" => "08:00",
                    "open_tooltip" => "8 HOURS ARE OPEN FOR SCHEDULING",
                    "starting" => "08:30",
                    "starting_tooltip" => "JILL DOE CAN START ON THIS AUDIT AT APPROXIMATELY 8:30 AM",
                    "distance_time" => "01:15",
                    "distance" => "54",
                    "distance_icon" => "a-marker-basic",
                    "distance_tooltip" => "The Other Place<br />123 Sesame Street, City, OH 12345"
                ]
            ]
        ]);
        return view('modals.project-assignment-add-auditor', compact('data'));
    }

    public function addAssignmentAuditorStats($id, $auditorid) {
        // id is project id
        
        $data = collect([
            "project" => [
                "id" => $id,
                "name" => "Project Name"
            ],
            "summary" => [
                "id" => $auditorid,
                "name" => "Jane Doe",
                'initials' => 'JD',
                'color' => 'blue',
                'date' => 'DECEMBER 22, 2018',
                'ref' => '20181222',
                'date-previous' => 'DECEMBER 13, 2018',
                'ref-previous' => '20181213',
                'date-next' => 'DECEMBER 31, 2018',
                'ref-next' => '20181231',
                'preferred_longest_drive' => '02:30',
                'preferred_lunch' => '00:30',
                'total_estimated_commitment' => "07:40"
            ],
            "itinerary-start" => [
                "id" => 1,
                "icon" => "a-home-marker",
                "type" => "start",
                "status" => "",
                "name" => "Default address",  
                "address" => "address here",
                "unit" => "unit 3",
                "city" => "city",
                "state" => "OH",
                "zip" => "12345",                  
                "average" => "00:00",
                "end" => "08:30 AM",
                "lead" => 1, // user id
                "order" => 1,
                "itinerary" => []
            ],
            "itinerary-end" => [
                "id" => 9,
                "icon" => "a-home-marker",
                "type" => "end",
                "status" => "",
                "name" => "The Ending Address", 
                "address" => "address here",
                "unit" => "unit 3",
                "city" => "city",
                "state" => "OH",
                "zip" => "12345",          
                "average" => "01:00",
                "end" => "4:10 PM",
                "lead" => 1,
                "order" => 5,
                "itinerary" => []
            ],
            "itinerary" => [
                [
                    "id" => 2,
                    "icon" => "a-marker-basic",
                    "type" => "site",
                    "status" => "in-progress",
                    "name" => "The Garden Oaks",
                    "average" => "00:00",
                    "end" => "08:30 AM",
                    "lead" => 1,
                    "order" => 2,
                    "itinerary" => [
                        [
                            "id" => 3,
                            "icon" => "a-mobile-home",
                            "type" => "site",
                            "status" => "in-progress",
                            "name" => "The Garden Oaks",
                            "average" => "02:00",
                            "end" => "11:30 AM",
                            "lead" => 1,
                            "order" => 1,
                        ],
                        [
                            "id" => 4,
                            "icon" => "a-suitcase-2",
                            "type" => "break",
                            "status" => "",
                            "name" => "LUNCH",
                            "average" => "00:30",
                            "end" => "12:00 AM",
                            "lead" => 1,
                            "order" => 2,
                        ]
                    ]
                ],
                [
                    "id" => 5,
                    "icon" => "a-marker-basic",
                    "type" => "site",
                    "status" => "",
                    "name" => "The Other Place",
                    "average" => "00:15",
                    "end" => "12:15 PM",
                    "lead" => 1,
                    "order" => 3,
                    "itinerary" => [
                        [
                            "id" => 6,
                            "icon" => "a-folder",
                            "type" => "file",
                            "status" => "in-progress",
                            "name" => "The Other Place",
                            "average" => "01:40",
                            "end" => "1:55 PM",
                            "lead" => 1,
                            "order" => 1,
                        ]
                    ]
                ],
                [
                    "id" => 7,
                    "icon" => "a-marker-basic",
                    "type" => "site",
                    "status" => "",
                    "name" => "The Womping Willow",
                    "average" => "00:15",
                    "end" => "2:10 PM",
                    "lead" => 2,
                    "order" => 4,
                    "itinerary" => [
                        [
                            "id" => 8,
                            "icon" => "a-folder",
                            "type" => "file",
                            "status" => "in-progress",
                            "name" => "The Womping Willow",
                            "average" => "01:00",
                            "end" => "3:10 PM",
                            "lead" => 2,
                            "order" => 1,
                        ]
                    ]
                ]
            ],
            "calendar" => [
                "header" => ["12/18", "12/19", "12/20", "12/21", "12/22", "12/23", "12/24", "12/25", "12/26"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/19",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/20",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/21",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/22",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 114,
                        "date" => "12/23",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/24",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/25",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/26",
                        "no_availability" => 1
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 13, 2018", 
                    'ref-previous' => '20181213',
                    "today" => "DECEMBER 22, 2018", 
                    "next" => "DECEMBER 31, 2018",
                    'ref-next' => '20181231'
                ]
            ],
            "calendar-previous" => [
                "header" => ["12/09", "12/10", "12/11", "12/12", "12/13", "12/14", "12/15", "12/16", "12/17"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/09",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/10",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/11",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/12",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/13",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/14",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/15",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/16",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/17",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 04, 2018", 
                    'ref-previous' => '20181204',
                    "today" => "DECEMBER 13, 2018", 
                    "next" => "DECEMBER 22, 2018",
                    'ref-next' => '20181222'
                ]
            ],
            "calendar-next" => [
                "header" => ["12/27", "12/28", "12/29", "12/30", "12/31", "01/01", "01/02", "01/03", "01/04"],
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/09",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/10",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/11",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/12",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/13",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/14",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/15/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/16",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/17",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ]
                ],
                "footer" => [
                    "previous" => "DECEMBER 22, 2018", 
                    'ref-previous' => '20181222',
                    "today" => "DECEMBER 31, 2018", 
                    "next" => "JANUARY 09, 2019",
                    'ref-next' => '20190109',
                ]
            ]
        ]);

        return view('projects.partials.details-assignment-auditor-stat', compact('data'));
    }

    public function getAssignmentAuditorCalendar($id, $auditorid, $currentdate, $beforeafter) {
        // from the current date and beforeafter, calculate new target date
        $created = Carbon\Carbon::createFromFormat('Ymd', $currentdate);
        if($beforeafter == "before"){
            $newdate = $created->subDays(9);

            $newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('F d, Y');
            $newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->subDays(18)->format('Ymd');
            $newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
            $newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');

            $newdateref = $newdate->format('Ymd');
            $newdateformatted = $newdate->format('F d, Y');

            $header_dates = []; 
            $header_dates[] = $newdate->subDays(4)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
        }else{
            $newdate = $created->addDays(9);

            $newdate_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('F d, Y');
            $newdate_ref_previous = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->format('Ymd');
            $newdate_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('F d, Y');
            $newdate_ref_next = Carbon\Carbon::createFromFormat('Ymd', $currentdate)->addDays(18)->format('Ymd');

            $newdateref = $newdate->format('Ymd');
            $newdateformatted = $newdate->format('F d, Y');

            $header_dates = []; 
            $header_dates[] = $newdate->subDays(4)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
            $header_dates[] = $newdate->addDays(1)->format('m/d');
        }
        // dd($header_dates);
        // dd($currentdate." - ".$created." - ".$newdate." - ".$newdateformatted." - ".$newdateref);
        $data = collect([
            "project" => [
                "id" => $id,
                "name" => "Project Name"
            ],
            "summary" => [
                "id" => $auditorid,
                "name" => "Jane Doe",
                'initials' => 'JD',
                'color' => 'blue',
                'date' => $newdateformatted,
                'ref' => $newdateref
            ],
            "calendar" => [
                "header" => $header_dates,
                "content" => [
                    [
                        "id" => 111,
                        "date" => "12/18",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "24",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "33",
                                "span" =>  "2",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "35",
                                "span" =>  "11",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 112,
                        "date" => "12/19",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "22",
                                "span" =>  "24",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 113,
                        "date" => "12/20",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "action-required",
                                "start" => "9",
                                "span" =>  "12",
                                "icon" => "a-mobile-not",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "21",
                                "span" =>  "4",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 114,
                                "status" => "",
                                "start" => "25",
                                "span" =>  "21",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 115,
                        "date" => "12/21",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-top",
                                "modal_type" => "choose-filing"
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "30",
                                "span" =>  "16",
                                "icon" => "a-circle-plus",
                                "lead" => 1,
                                "class" => "available no-border-bottom",
                                "modal_type" => "choose-filing"
                            ]
                        ]
                    ],
                    [
                        "id" => 116,
                        "date" => "12/22",
                        "no_availability" => 0,
                        "start_time" => "08:00 AM",
                        "end_time" => "05:30 PM",
                        "before_time_start" => "1",
                        "before_time_span" => "8",
                        "after_time_start" => "46",
                        "after_time_span" => "15",
                        "events" => [
                            [
                                "id" => 112,
                                "status" => "in-progress",
                                "start" => "9",
                                "span" =>  "16",
                                "icon" => "a-mobile-checked",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => "change-date"
                            ],
                            [
                                "id" => 113,
                                "status" => "breaktime",
                                "start" => "25",
                                "span" =>  "1",
                                "icon" => "",
                                "lead" => 1,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "26",
                                "span" =>  "12",
                                "icon" => "a-folder",
                                "lead" => 2,
                                "class" => "",
                                "modal_type" => ""
                            ],
                            [
                                "id" => 113,
                                "status" => "",
                                "start" => "38",
                                "span" =>  "8",
                                "icon" => "a-folder",
                                "lead" => 1,
                                "class" => "no-border-bottom",
                                "modal_type" => ""
                            ]
                        ]
                    ],
                    [
                        "id" => 114,
                        "date" => "12/23",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/24",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/25",
                        "no_availability" => 1
                    ],
                    [
                        "id" => 114,
                        "date" => "12/26",
                        "no_availability" => 1
                    ]
                ],
                "footer" => [
                    "previous" => $newdate_previous, 
                    'ref-previous' => $newdate_ref_previous,
                    "today" => $newdateformatted, 
                    "next" => $newdate_next,
                    'ref-next' => $newdate_ref_next
                ]
            ]
        ]);

        return view('projects.partials.details-assignment-auditor-calendar', compact('data'));
    }
}
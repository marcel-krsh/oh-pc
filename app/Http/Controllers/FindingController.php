<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;

class FindingController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
    	   Auth::onceUsingId(1); // TEST BRIAN
        }
    }

    public function modalFindings($type, $auditid, $buildingid, $unitid='')
    {
    	// get user's audits, projects, buildings, areas, units based on click
    	/*
    	
		• Clicking on the finding icon from the audit list level will default to the project name - project common areas - and the first common area of that project.
		• Clicking on an amenity item listed on a building or unit will filter to that item, and use the "*" finding type (the auditor should select a specific type to shorten the list).
		• Clicking on the finding icon from the building level list will default to the building address, the building,
		and the first amenity on the building.
		• Clicking on the finding icon at the unit level will default to unit's building address, the unit number, and the first amenity on the unit.
		• Clicking on the finding icon in the program expansion screen will automatically select that specific item.

    	 */
    	


    	/*
    	if the auditor did not open the add findings window from
		the program detail expansion of a building or unit, and they click the "Done Adding Findings" button or they change to a different building, unit or common area set checkDoneAddingFindings to 1 otherwise 0.
    	 */
    	$checkDoneAddingFindings = 1;

    	$data = collect([
    		'selected-audit' => [
    			'id' => 123,
    			'ref' => "1234567",
    			'address' => '12345 Bob Street, City, State 22233',
    			'selected-location' => [
    				'id' => 2,
    				'name' => 'Building 2',
    				'amenities' => [
    					[
    						'id' => 9,
    						'name' => 'Inspectable area 9'
    					],
    					[
    						'id' => 8,
    						'name' => 'Inspectable area 8'
    					],
    					[
    						'id' => 7,
    						'name' => 'Inspectable area 7'
    					]
    				]
    			], 
    			'selected-amenity' => [
    				'id' => 9,
    				'name' => 'Inspectable area 9'
    			]
    		],
    		'audits' => [
    			[
	    			'id' => 123,
	    			'ref' => "1234567",
	    			'address' => '12345 Bob Street, City, State 22233'
	    		],
    			[
	    			'id' => 456,
	    			'ref' => "567",
	    			'address' => '555 Other Street, City, State 11111'
	    		],
    			[
	    			'id' => 789,
	    			'ref' => "12555",
	    			'address' => '66666 Bobby Street, City, State 55555'
	    		],
    			[
	    			'id' => 555,
	    			'ref' => "44467",
	    			'address' => '99877 John Street, City, State 66666'
	    		]
    		],
    		'finding-types' => [
    			['id'=> 1, 'name' => 'Inspection Group SD finding description here', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 2, 'name' => 'INSPECTION GROUP SD FINDING DESCRIPTION HERE WITH A REALLY LONG NAME THAT FLOWS TO THE NEXT LINE', 'type' => 'sd', 'icon' => 'a-flames'],
    			['id'=> 3, 'name' => 'Inspection Group SD finding description here 3', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 4, 'name' => 'Inspection Group SD finding description here 6', 'type' => 'nlt', 'icon' => 'a-booboo'],
    			['id'=> 5, 'name' => 'Inspection Group SD finding description here 1', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 6, 'name' => 'Inspection Group SD finding description here 9', 'type' => 'sd', 'icon' => 'a-flames'],
    			['id'=> 7, 'name' => 'INSPECTION GROUP SD FINDING DESCRIPTION HERE WITH A REALLY LONG NAME THAT FLOWS TO THE NEXT LINE 4', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 8, 'name' => 'Inspection Group SD finding description here 23', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 9, 'name' => 'Inspection Group SD finding description here 44', 'type' => 'lt', 'icon' => 'a-skull'],
    			['id'=> 10, 'name' => 'Inspection Group SD finding description here 12', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 11, 'name' => 'Inspection Group SD finding description here33', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 12, 'name' => 'Inspection Group SD finding description here1', 'type' => 'nlt', 'icon' => 'a-booboo'],
    			['id'=> 13, 'name' => 'Inspection Group SD finding description here093', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 14, 'name' => 'Inspection Group SD finding description here56', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 15, 'name' => 'Inspection Group SD finding description here 7', 'type' => 'nlt', 'icon' => 'a-booboo'],
    			['id'=> 16, 'name' => 'Inspection Group SD finding description here 8', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 17, 'name' => 'Inspection Group SD finding description here 9', 'type' => 'lt', 'icon' => 'a-skull'],
    			['id'=> 18, 'name' => 'Inspection Group SD finding description here45', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 19, 'name' => 'Inspection Group SD finding description here43', 'type' => 'file', 'icon' => 'a-folder'],
    			['id'=> 20, 'name' => 'Inspection Group SD finding description here23', 'type' => 'lt', 'icon' => 'a-skull']
    		],
    		'findings' => [
    			[
    				'id' => 987,
    				'ref' => '20120394',
    				'status' => 'action-needed',
    				'type' => 'nlt',
    				'finding-filter' => 'my-finding',
    				'audit-filter' => 'this-audit',
    				'icon' => 'a-booboo',
    				'audit' => '20120394',
    				'date' => '12/22/2018 12:51:38 PM',
    				'description' => 'Inspection Group NLT Finding Description Here',
    				'auditor' => [
    					'id' => 1,
    					'name' => 'Holly Swisher'
    				],
    				'building' => [
    					'id' => 144,
    					'name' => 'Building 2'
    				],
    				'amenity' => [
    					'id' => '111',
    					'name' => 'STAIR #1',
	    				'address' => '123457 Silvegwood Street',
	    				'city' => 'Columbus',
	    				'state' => 'OH',
	    				'zip' => '43219'
    				],
    				'items' => [
    					[
    						'id' => 333,
    						'type' => 'comment',
    						'date' => '12/22/2018 12:51:38 PM',
		    				'auditor' => [
		    					'id' => 1,
		    					'name' => 'Holly Swisher'
		    				]
    					]
    				]

    			],
    			[
    				'id' => 947,
    				'ref' => '11112394',
    				'status' => 'action-required',
    				'type' => 'sd',
    				'finding-filter' => '',
    				'audit-filter' => 'this-audit',
    				'icon' => 'a-flames',
    				'audit' => '20121111',
    				'date' => '12/22/2018 12:51:38 PM',
    				'description' => 'Inspection Group SD Finding Description Here',
    				'auditor' => [
    					'id' => 1,
    					'name' => 'Holly Swisher'
    				],
    				'building' => [
    					'id' => 144,
    					'name' => 'Building 2'
    				],
    				'amenity' => [
    					'id' => '111',
    					'name' => 'STAIR #1',
	    				'address' => '123457 Silvegwood Street',
	    				'city' => 'Columbus',
	    				'state' => 'OH',
	    				'zip' => '43219'
    				],
    				'items' => [
    					[
    						'id' => 333,
    						'type' => 'comment',
    						'date' => '12/22/2018 12:51:38 PM',
		    				'auditor' => [
		    					'id' => 1,
		    					'name' => 'Holly Swisher'
		    				]
    					]
    				]

    			],
    			[
    				'id' => 947,
    				'ref' => '11112394',
    				'status' => 'action-required',
    				'type' => 'sd',
    				'finding-filter' => '',
    				'audit-filter' => '',
    				'icon' => 'a-flames',
    				'audit' => '20121111',
    				'date' => '12/22/2018 12:51:38 PM',
    				'description' => 'Inspection Group SD Finding Description Here',
    				'auditor' => [
    					'id' => 1,
    					'name' => 'Holly Swisher'
    				],
    				'building' => [
    					'id' => 144,
    					'name' => 'Building 2'
    				],
    				'amenity' => [
    					'id' => '111',
    					'name' => 'STAIR #1',
	    				'address' => '123457 Silvegwood Street',
	    				'city' => 'Columbus',
	    				'state' => 'OH',
	    				'zip' => '43219'
    				],
    				'items' => [
    				]

    			]
    		]
    	]);
    	return view('modals.findings', compact('data', 'checkDoneAddingFindings'));
    }

    function findingItems($findingid, $itemid = '') {
    	// itemid used for children of items

    	$data['items'] = collect([
				[
					'id' => 333,
					'ref' => '123456',
    				'status' => 'action-required',
    				'audit' => '20121111',
					'findingid' => $findingid,
					'parentitemid' => $itemid,
					'type' => 'comment',
    				'icon' => 'a-comment-text',
					'date' => '12/05/2018 12:51:38 PM',
    				'auditor' => [
    					'id' => 1,
    					'name' => 'Holly Swisher'
    				],
    				'comment' => 'Custom comment based on stuff I saw...',
    				'stats' => [
    					['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 1],
    					['type' => 'file', 'icon' => 'a-file-plus', 'count' => 2],
    					['type' => 'photo', 'icon' => 'a-picture', 'count' => 3]
    				]
				],
				[
					'id' => 444,
					'ref' => '333444',
    				'status' => 'action-needed',
    				'audit' => '20121111',
					'findingid' => $findingid,
					'parentitemid' => $itemid,
					'type' => 'followup',
    				'icon' => 'a-bell-plus',
    				'duedate' => '12/22/2018',
					'date' => '12/22/2018 3:51:38 PM',
					'assigned' => ['id' => 3, 'name' => 'PM Name Here'],
    				'auditor' => [
    					'id' => 1,
    					'name' => 'Holly Swisher'
    				],
    				'comment' => 'Auto-generated follow-up for SD with tasks and due date auto-set for same day.',
    				'stats' => [
    					['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 0],
    					['type' => 'file', 'icon' => 'a-file-plus', 'count' => 0],
    					['type' => 'photo', 'icon' => 'a-picture', 'count' => 0]
    				]
				],
                [
                    'id' => 555,
                    'ref' => '123666',
                    'status' => '',
                    'audit' => '20121111',
                    'findingid' => $findingid,
                    'parentitemid' => $itemid,
                    'type' => 'photo',
                    'icon' => 'a-picture',
                    'date' => '12/05/2018 12:51:38 PM',
                    'auditor' => [
                        'id' => 1,
                        'name' => 'Holly Swisher'
                    ],
                    'photos' => [
                        ['id' => 22, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 2],
                        ['id' => 23, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 1],
                        ['id' => 24, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 3],
                        ['id' => 25, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 4],
                        ['id' => 26, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 6],
                        ['id' => 27, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 0]
                    ],
                    'comment' => '',
                    'stats' => [
                        ['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 2],
                        ['type' => 'photo', 'icon' => 'a-picture', 'count' => 5]
                    ]
                ]
    	]);
        return response()->json($data);
    }

    function findingItemPhoto($finding_id, $item_id, $photo_id) {
        $photo = collect([
            'id' => $photo_id,
            'url' => 'http://fpoimg.com/840x600',
            'comments' => [
                [
                    'id' => 1,
                    'ref' => '123456',
                    'status' => '',
                    'audit' => '20121111',
                    'findingid' => $finding_id,
                    'parentitemid' => $item_id,
                    'photoid' => $photo_id,
                    'type' => 'comment',
                    'icon' => 'a-comment-text',
                    'date' => '12/05/2018 12:51:38 PM',
                    'auditor' => [
                        'id' => 1,
                        'name' => 'Holly Swisher'
                    ],
                    'comment' => 'Custom comment based on stuff I saw...'
                ],
                [
                    'id' => 2,
                    'ref' => '123457',
                    'status' => '',
                    'audit' => '20121111',
                    'findingid' => $finding_id,
                    'parentitemid' => $item_id,
                    'photoid' => $photo_id,
                    'type' => 'comment',
                    'icon' => 'a-comment-text',
                    'date' => '12/06/2018 12:51:38 PM',
                    'auditor' => [
                        'id' => 1,
                        'name' => 'Holly Swisher'
                    ],
                    'comment' => 'Second custom comment based on stuff I saw...'
                ]
            ]
        ]);
        return view('modals.photo', compact('photo'));
    }

    function autosave(Request $request) {
    	return "done";
    }

}
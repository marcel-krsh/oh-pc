<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\CachedAudit;
use App\Models\Audit;
use App\Models\Building;
use App\Models\Project;
use App\Models\AmenityInspection;
use App\Models\UnitInspection;
use App\Models\BuildingInspection;
use App\Models\Amenity;
use App\Models\Finding;
use App\Models\FindingType;
use App\Models\Followup;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\SyncDocuware;
use Carbon;


class FindingController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            //Auth::onceUsingId(286); // TEST BRIAN
            Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
        }
    }

    public function addFindingForm($findingtypeid,AmenityInspection $amenityinspectionid,Request $request){
        if(Auth::user()->auditor_access()){
            $findingtypeid = FindingType::where('id',$findingtypeid)->first();
            //dd($findingtypeid, $amenityinspectionid);
            // return form with boilerplates assigned?
            $amenityincrement = $request->amenity_increment;
            return view('templates.modal-findings-new-form',compact('findingtypeid','amenityinspectionid','amenityincrement'));
        }else{
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function addFinding(Request $request){
        if(Auth::user()->auditor_access()){
            $inputs = $request->input('inputs');
            parse_str($inputs, $inputs);

            // make sure we have what we need
            $error = '';
            if($inputs['finding_type_id'] == ''){
                $error .= '<p>I am having trouble with the finding type you selected. Please refresh your page and try again.</p>';
            }
            if($inputs['amenity_inspection_id'] == ''){
                $error .= '<p>I am having trouble with the amenity you selected. Please refresh your page and try again.</p>';
            }
            if($inputs['level'] == ''){
                $error .= '<p>Please select a level.</p>';
            }

            if($error != ''){

                return $error;

            }else{
                // passed initial error checking - lets get the data
                $findingType = FindingType::find($inputs['finding_type_id']);
                $amenityInspection = AmenityInspection::find($inputs['amenity_inspection_id']);

                $date = Carbon\Carbon::createFromFormat('Y-m-d' , $inputs['date'])->format('Y-m-d H:i:s');

                $cached_audit = CachedAudit::where('audit_id', '=', $amenityInspection->audit_id)->first();
                $project = $cached_audit->project;

                $owner_organization_id = $project->owner()['organization_id'];
                $pm_organization_id = $project->pm()['organization_id'];

                // Check to make sure that we got that data
                if(is_null($findingType)){
                    $error .= '<p>I was not able to identify the finding type you selected. This is not your fault! </p><p>Please notify your admin that you tried to add finding type id '.$input['finding_type_id'].' and it gave you this error:<br /> FindingController: Error #79<p>';
                }
                if(is_null($amenityInspection)){
                    $error .= '<p>I was not able to identify the amenity you selected. It is possible it was deleted while you were working on it by another user.</p><p>Please refresh your screen by closing the inpsection and reopening it. If you still see the amenity there, still try clicking on it to add a finding again, as it may be been deleted and re-added with a new identifier.</p><p>If that does not work, please notify your admin that you tried to add a finding to amenity inspection id '.$input['amenity_inspection_id'].' and it gave you this error:<br /> FindingController: Error #82<p>';
                }

                if($error != ''){
                    return $error;
                } else {
                    // we have the goods - let's store this bad boy!
                    $errors = ''; // tracking errors to return to user.
                    $finding = new Finding([
                                'date_of_finding' => $date,
                                'owner_organization_id' => $owner_organization_id,
                                'pm_organization_id' => $pm_organization_id,
                                'user_id' => Auth::user()->id,
                                'audit_id' => $amenityInspection->audit_id,
                                'project_id' => $project->id,
                                'building_id' => $amenityInspection->building_id,
                                'unit_id' => $amenityInspection->unit_id,
                                'finding_type_id' => $findingType->id,
                                'amenity_id' => $amenityInspection->amenity_id,
                                'amenity_inspection_id' => $amenityInspection->id,
                                'weight' => $findingType->nominal_item_weight,
                                'criticality' => $findingType->criticality,
                                'level'=> $inputs['level'],
                                'site'=> $findingType->site,
                                'building_system' => $findingType->building_system,
                                'building_exterior' => $findingType->building_exterior,
                                'common_area'=> $findingType->common_area,
                                'allita_type' => $findingType->allita_type,
                                'finding_status_id' => 1,
                        ]);
                    $finding->save();

                    // save comment if there is one:
                    if(strlen($inputs['comment']) > 0){
                        // there was text entered - create the comment and attach it to the finding
                        Comment::insert([
                            'user_id' => Auth::user()->id,
                            'audit_id' => $amenityInspection->audit_id,
                            'finding_id' => $finding->id,
                            'comment' => $inputs['comment'],
                            'recorded_date' => $date
                        ]);

                    }
                    // put in default follow-ups
                    if(count($findingType->default_follow_ups)){
                        $errors = '';
                        foreach ($findingType->default_follow_ups as $fu) {
                            // set assignee
                            switch ($fu->assignment) {
                                case 'pm':
                                    $assigned_user_id = "???";# code...
                                    break;

                                case 'lead':
                                    $assigned_user_id = "???";
                                    break;
                                
                                case 'user':
                                    $assigned_user_id = Auth::user()->id;
                                    break;

                                default:
                                    $error .= '<p>Sorry, the default follow-up with id '.$fu->id.' could not be created because the default asigned user was not defined.</p> <p>FindingController Error #143</p>';
                                    break;
                            }
                            // set due date
                            $today = new DateTime(date("Y-m-d H:i:s",time()));
                            $due = $today->modify("+ {$fu->quantity} {$fu->duration}");

                            // reply photo doc doc_categories <--- reference to columns in table

                            if($error == ''){
                                Followup::insert([
                                    'created_by_user_id' => Auth::user()->id,
                                    'assigned_to_user' => $assigned_user_id,
                                    'date_due' => $due, 
                                    'finding_id' => $finding->id,
                                    'project_id' =>$amenityInspection->project_id,
                                    'audit_id' => $amenityInspection->audit_id,
                                    'comment_type' => $fu->reply,
                                    'document_type' => $fu->doc,
                                    'document_categories' => $fu->doc_categories,
                                    'photo_type' => $fu->photo,
                                    'description' => $fu->description
                                ]);
                            } else { 
                                $errors .= $error;
                                $error = ''; // reset this so it can do all folow-ups even if this one is bad.
                            }
                        }

                    }
                    if($errors == ''){
                            // no errors
                            return '<h2>Added finding to the project.</h2> 
                                    <script> // close the stacked modal but leave open the add finding. Refresh the findings list. 

                                    </script>';
                        } else {
                            return '<h2>I added the finding but...</h2>
                                    <p>One or more of the default follow-ups had erors- please see below and send this information to your admin.</p>
                            '.$errors;
                        }
                }
            }
            //dd($inputs['finding_type_id'],$inputs['amenity_inspection_id'],$inputs['comment'],$inputs['level']);
            // return form with boilerplates assigned?
        }else{
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function findingList($type, $amenityinspection, Request $request){
        if(Auth::user()->auditor_access()){
            
        $allFindingTypes = null;
        $ai = null;
        $search = $request->search;
        if(is_null($search)) { $search = '';}

        $ai = AmenityInspection::where('id',$amenityinspection)->with('amenity')->first();
        

                
        if($ai){
                $amenityInspectionId = $ai->id;
                // determine the amenity type
                if($ai->building_id){
                    $amenityLocationType = "b";
                } else if($ai->project_id){
                    $amenityLocationType = "p";
                } else if($ai->unit_id) {
                    $amenityLocationType = "u";
                }
            if($type != 'all'){    

                $allFindingTypes = $ai->amenity->finding_types();
                   
            } else {

                $allFindingTypes = FindingType::select('*')->get();

            }

                $allFindingTypes = $allFindingTypes->filter(function($findingType) use($type, $search,$amenityLocationType) {

                        if($findingType->type == $type || $type == 'all'){

                            switch ($amenityLocationType) {
                                case 'b':
                                    if(!$findingType->building_exterior && !$findingType->building_system && !$findingType->common_area){
                                        return false;
                                    }
                                    break;

                                case 'p':
                                    if(!$findingType->site && !$findingType->common_area){
                                        return false;
                                    }
                                    break;

                                case 'u':
                                    if(!$findingType->unit){
                                        return false;
                                    }
                                    break;

                                default:
                                    return false;
                                    break;
                            }
                            
                            if($search != '' ){
                                if(strpos(strtolower($findingType->name), strtolower($search)) !== false) {
                                    return true;
                                } else {
                                    return false;
                                }

                            }
                            return true;

                        } else {
                            return false;
                        }
                    });
        } else {
                    
                        return 'I was not able to find that amenity... it appears to have been delted. Perhaps it was deleted by another auditor? Try closing this inspection and reopening it to view an updated amenity list.';
                   
                    
        }

        //->orderBy('type','asc')->orderBy('name','asc')->get();
        
        //dd($type,$amenityinspection,$request,$allFindingTypes,$ai,$request->search);

        return view('modals.finding-types-list', compact('allFindingTypes','search','type','amenityLocationType','amenityInspectionId'));
        }else{
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function modalFindings($type, $auditid, $buildingid = null, $unitid = null, $amenityid = null)
    {
        // get user's audits, projects, buildings, areas, units, based on click
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

        //dd('type:'.$type.' auditid:'.$auditid.' buildingid:'.$buildingid.' unitid:'.$unitid.' amenityid:'.$amenityid);
        //// "type:nlt auditid:6410 buildingid:16721 unitid:1005379 amenityid:"

        // the selected one that opened this modal

        if(Auth::user()->auditor_access()){
        $audit = null;
        $building = null;
        $unit = null;
        $amenity = null;

        $buildings = null;
        $units = null;
        $amenities = null;
        $allFindings = null;

        if($auditid > 0){
            //$audit = CachedAudit::where('audit_id',$auditid)->with('inspection_items')->with('inspection_items.amenity.finding_types')->with('inspection_items.amenity.finding_types.boilerplates()')->first();
            $audit = CachedAudit::where('audit_id',$auditid)->with('inspection_items')->first();
        }
        if($buildingid > 0){
            // always use the audit id as a selector to ensure you get the correct one
            $building = CachedBuilding::where('audit_id',$auditid)->where('id',$buildingid)->with('building.address')->first();

            //dd($buildingid, $building,$auditid);
        }
        if($unitid > 0){
            // always use the audit id as a selector to ensure you get the correct one
            $unit = CachedUnit::where('audit_id',$auditid)->where('id',$unitid)->with('building')->with('building.address')->first();
           // dd($unit, $unitid);
        }
        if($amenityid > 0){
            // we use the inspection id to make sure we get the one associated that they clicked on (in case of duplicate amenities)
            $amenity = AmenityInspection::where('id',$amenityid)->first();
        }
        if(is_null($audit)){
            return "alert('No audit found for ID:".$auditid."');";
        }

        //dd($audit);
        /// All of them for switching
            $audits = CachedAudit::where('project_id',$audit->project_id)->get()->all();

            // always use the audit id as a selector to ensure you get the correct one
            $buildings = BuildingInspection::where('audit_id',$auditid)->get();
       
            // always use the audit id as a selector to ensure you get the correct one
            $units = UnitInspection::select('unit_id','unit_key','unit_name','building_id','building_key','audit_id','complete')->where('audit_id',$auditid)->where('complete',0)->orWhereNull('complete')->groupBy('unit_id')->get();

        
            // always use the audit id as a selector to ensure you get the correct one
            $amenities = AmenityInspection::where('audit_id',$auditid)->with('amenity')->get(); 

            $findings = Finding::where('project_id',$audit->project_id)
                ->with('comments')
                ->with('comments.comments')
                ->with('photos')
                ->with('photos.comments')
                ->with('photos.comments.comments')
                ->with('followups')
                ->with('followups.comments')
                ->with('followups.comments.comments')
                ->with('followups.documents')
                ->with('followups.documents.comments')
                ->with('followups.documents.comments.comments')
                ->with('followups.photos')
                ->with('followups.photos.comments')
                ->with('followups.photos.comments.comments')
                ->orderBy('updated_at','desc')
                ->get()->all();

            $followups = Followup::where('project_id',$audit->project_id)
                ->with('comments')
                ->with('comments.comments')
                ->with('photos')
                ->with('photos.comments')
                ->with('photos.comments.comments')
                ->with('documents')
                ->with('documents.comments')
                ->with('documents.comments.comments')
                ->orderBy('updated_at','desc')
                ->get()->all();
           
            //get comments that are only on the root of the project
            $comments = Comment::where('project_id',$audit->project_id)
                ->with('comments')
                ->whereNull('finding_id')
                ->whereNull('document_id')
                ->whereNull('photo_id')
                ->whereNull('followup_id')
                ->whereNull('comment_id')
                ->orderBy('updated_at','desc')
                ->get()
                ->all();
 
            //get documents that are only on the root of the project or attached to a communication - this is only for auditors to see and above.
            $documents = SyncDocuware::where('project_id',$audit->project_id)
                ->with('comments')
                ->with('comments.comments')
                ->whereNull('finding_id')
                ->whereNull('photo_id')
                ->whereNull('followup_id')
                ->orderBy('updated_at','desc')
                ->get()
                ->all();

            //get documents that are only on the root of the project or attached to a communication - this is only for auditors to see and above.
            $photos = Photo::where('project_id',$audit->project_id)
                ->with('comments')
                ->with('comments.comments')
                ->with('photos')
                ->with('photos.comments')
                ->with('photos.comments.comments')
                ->whereNull('finding_id')
                ->whereNull('photo_id')
                ->whereNull('followup_id')
                ->orderBy('updated_at','desc')
                ->get()
                ->all();


      


        if (is_null($type)) {
            // default filter is all
            $type = 'all';
        }

        $checkDoneAddingFindings = 1;

        $data = collect([
            'selected-audit' => [
                'id' => $auditid,
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
                    'id' => rand(100, 10000),
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
                        'id' => rand(100, 10000),
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
                            'id' => rand(100, 10000),
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
                    'id' => rand(100, 10000),
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
                        'id' => rand(100, 10000),
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
                            'id' => rand(100, 10000),
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
                    'id' => rand(100, 10000),
                    'ref' => '999999948',
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
        return view('modals.findings', compact('data','audit', 'checkDoneAddingFindings', 'type' , 'photos','comments','findings','documents','unit','building','amenity','project','followups','audits','units','buildings','amenities','allFindingTypes'));
        }else{
            return "Sorry, you do not have permission to access this page.";
        }
    }

    function findingItems($findingid, $itemid = '')
    {
        // itemid used for children of items

        $data['items'] = collect([
                [
                    'id' => rand(100, 10000),
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
                    'id' => rand(100, 10000),
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
                    'id' => rand(100, 10000),
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
                ],
                [
                    'id' => rand(100, 10000),
                    'ref' => '333444',
                    'status' => 'action-required',
                    'audit' => '20121111',
                    'findingid' => $findingid,
                    'parentitemid' => $itemid,
                    'type' => 'file',
                    'icon' => 'a-file-left',
                    'duedate' => '12/22/2018',
                    'date' => '12/22/2018 3:51:38 PM',
                    'assigned' => ['id' => 3, 'name' => 'PM Name Here'],
                    'auditor' => [
                        'id' => 1,
                        'name' => 'Holly Swisher'
                    ],
                    'categories' => [
                        ['id' => 1, 'name' => 'Category Name 1', 'status' => 'checked'],
                        ['id' => 2, 'name' => 'Category Name 2', 'status' => 'checked'],
                        ['id' => 3, 'name' => 'Category Name 3', 'status' => 'notchecked'],
                        ['id' => 4, 'name' => 'Category Name 4', 'status' => '']
                    ],
                    'file' => [
                        'id' => 1,
                        'name' => 'my_long-filename.pdf',
                        'url' => '#',
                        'type' => 'pdf',
                        'size' => '1.3'
                    ],
                    'comment' => '',
                    'stats' => [
                        ['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 0],
                        ['type' => 'file', 'icon' => 'a-file-plus', 'count' => 0],
                        ['type' => 'photo', 'icon' => 'a-picture', 'count' => 0]
                    ]
                ]
        ]);
        return response()->json($data);
    }

    function findingItemPhoto($finding_id, $item_id, $photo_id)
    {
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

    // function autosave(Request $request)
    // {
    //     return "done";
    // }
}

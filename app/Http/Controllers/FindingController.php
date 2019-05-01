<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\AmenityInspection;
use App\Models\BuildingInspection;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Finding;
use App\Models\FindingType;
use App\Models\Followup;
use App\Models\Photo;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\User;
use Auth;
use Carbon;
use Illuminate\Http\Request;

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

    public function addFindingForm($findingtypeid, AmenityInspection $amenityinspectionid, Request $request)
    {
        if (Auth::user()->auditor_access()) {
            $findingtypeid = FindingType::where('id', $findingtypeid)->first();
            //dd($findingtypeid, $amenityinspectionid);
            // return form with boilerplates assigned?
            $amenityincrement = $request->amenity_increment;
            return view('templates.modal-findings-new-form', compact('findingtypeid', 'amenityinspectionid', 'amenityincrement'));
        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function editFindingForm($findingid)
    {
        if (Auth::user()->auditor_access()) {
            $finding = Finding::where('id', '=', $findingid)->first();

            return view('templates.modal-findings-edit-form', compact('finding'));
        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function editFinding(Request $request)
    {
        if (Auth::user()->auditor_access()) {
            $inputs = $request->input('inputs');
            parse_str($inputs, $inputs);

            $error = '';
            if ($inputs['finding_type_id'] == '') {
                $error .= '<p>I am having trouble with the finding type you selected. Please refresh your page and try again.</p>';
            }
            if ($inputs['level'] == '') {
                $error .= '<p>Please select a level.</p>';
            }

            if ($error != '') {

                return $error;

            } else {
                $findingType = FindingType::find($inputs['finding_type_id']);
                $date = Carbon\Carbon::createFromFormat('F j, Y', $inputs['date'])->format('Y-m-d H:i:s');

                $finding = Finding::where('id', '=', $inputs['finding_id'])->first();
                $finding->date_of_finding = $date;
                $finding->finding_type_id = $findingType->id;
                $finding->level = $inputs['level'];
                $finding->save();

                return 1;

            }

        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function cancelFinding(Request $request, $findingid)
    {
        if (Auth::user()->auditor_access()) {

            $finding = Finding::where('id', '=', $findingid)->first();
            $date = Carbon\Carbon::now()->format('Y-m-d H:i:s');

            $finding->cancelled_at = $date;
            $finding->save();

            return 1;

        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function restoreFinding(Request $request, $findingid)
    {
        if (Auth::user()->auditor_access()) {

            $finding = Finding::where('id', '=', $findingid)->first();

            $finding->cancelled_at = null;
            $finding->save();

            return 1;

        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function addFinding(Request $request)
    {
        if (Auth::user()->auditor_access()) {
            $inputs = $request->input('inputs');
            parse_str($inputs, $inputs);

            // make sure we have what we need
            $error = '';
            if ($inputs['finding_type_id'] == '') {
                $error .= '<p>I am having trouble with the finding type you selected. Please refresh your page and try again.</p>';
            }
            if ($inputs['amenity_inspection_id'] == '') {
                $error .= '<p>I am having trouble with the amenity you selected. Please refresh your page and try again.</p>';
            }
            if ($inputs['level'] == '') {
                $error .= '<p>Please select a level.</p>';
            }

            if ($error != '') {

                return $error;

            } else {
                // passed initial error checking - lets get the data
                $findingType = FindingType::find($inputs['finding_type_id']);
                $amenityInspection = AmenityInspection::find($inputs['amenity_inspection_id']);

                $date = Carbon\Carbon::createFromFormat('Y-m-d', $inputs['date'])->format('Y-m-d H:i:s');

                $cached_audit = CachedAudit::where('audit_id', '=', $amenityInspection->audit_id)->first();
                $project = $cached_audit->project;

                $owner_organization_id = $project->owner()['organization_id'];
                $pm_organization_id = $project->pm()['organization_id'];

                // Check to make sure that we got that data
                if (is_null($findingType)) {
                    $error .= '<p>I was not able to identify the finding type you selected. This is not your fault! </p><p>Please notify your admin that you tried to add finding type id ' . $input['finding_type_id'] . ' and it gave you this error:<br /> FindingController: Error #79<p>';
                }
                if (is_null($amenityInspection)) {
                    $error .= '<p>I was not able to identify the amenity you selected. It is possible it was deleted while you were working on it by another user.</p><p>Please refresh your screen by closing the inpsection and reopening it. If you still see the amenity there, still try clicking on it to add a finding again, as it may be been deleted and re-added with a new identifier.</p><p>If that does not work, please notify your admin that you tried to add a finding to amenity inspection id ' . $input['amenity_inspection_id'] . ' and it gave you this error:<br /> FindingController: Error #82<p>';
                }

                if ($error != '') {
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
                        'level' => $inputs['level'],
                        'site' => $findingType->site,
                        'building_system' => $findingType->building_system,
                        'building_exterior' => $findingType->building_exterior,
                        'common_area' => $findingType->common_area,
                        'allita_type' => $findingType->allita_type,
                        'finding_status_id' => 1,
                    ]);
                    $finding->save();

                    // save comment if there is one:
                    if (strlen($inputs['comment']) > 0) {
                        // there was text entered - create the comment and attach it to the finding
                        $newcomment = new Comment([
                            'user_id' => Auth::user()->id,
                            'audit_id' => $amenityInspection->audit_id,
                            'finding_id' => $finding->id,
                            'comment' => $inputs['comment'],
                            'recorded_date' => $date,
                        ]);
                        $newcomment->save();

                    }
                    // put in default follow-ups
                    if (count($findingType->default_follow_ups)) {
                        $errors = '';
                        foreach ($findingType->default_follow_ups as $fu) {
                            // set assignee
                            switch ($fu->assignment) {
                                case 'pm':
                                    $assigned_user_id = "???"; # code...
                                    break;

                                case 'lead':
                                    $assigned_user_id = "???";
                                    break;

                                case 'user':
                                    $assigned_user_id = Auth::user()->id;
                                    break;

                                default:
                                    $error .= '<p>Sorry, the default follow-up with id ' . $fu->id . ' could not be created because the default asigned user was not defined.</p> <p>FindingController Error #143</p>';
                                    break;
                            }
                            // set due date
                            $today = new DateTime(date("Y-m-d H:i:s", time()));
                            $due = $today->modify("+ {$fu->quantity} {$fu->duration}");

                            // reply photo doc doc_categories <--- reference to columns in table

                            if ($error == '') {
                                Followup::insert([
                                    'created_by_user_id' => Auth::user()->id,
                                    'assigned_to_user' => $assigned_user_id,
                                    'date_due' => $due,
                                    'finding_id' => $finding->id,
                                    'project_id' => $amenityInspection->project_id,
                                    'audit_id' => $amenityInspection->audit_id,
                                    'comment_type' => $fu->reply,
                                    'document_type' => $fu->doc,
                                    'document_categories' => $fu->doc_categories,
                                    'photo_type' => $fu->photo,
                                    'description' => $fu->description,
                                ]);
                            } else {
                                $errors .= $error;
                                $error = ''; // reset this so it can do all folow-ups even if this one is bad.
                            }
                        }

                    }
                    if ($errors == '') {
                        // no errors
                        return '1';
                    } else {
                        return '<h2>I added the finding but...</h2>
                          	<p>One or more of the default follow-ups had erors- please see below and send this information to your admin.</p>
                          	' . $errors;
                    }
                }
            }
            //dd($inputs['finding_type_id'],$inputs['amenity_inspection_id'],$inputs['comment'],$inputs['level']);
            // return form with boilerplates assigned?
        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function replyFindingForm($id, $fromtype, $type)
    {

        // $type: followup, photo, document, comment

        if (Auth::user()->auditor_access()) {
            if ($fromtype == 'finding') {
                $from = Finding::where('id', '=', $id)->first();
            } elseif ($fromtype == 'comment') {
                $from = Comment::where('id', '=', $id)->first();
            } elseif ($fromtype == 'photo') {
                $from = Photo::where('id', '=', $id)->first();
            } elseif ($fromtype == 'document') {
                $from = Document::where('id', '=', $id)->first();
            }

            return view('modals.finding-reply-' . $type, compact('from', 'fromtype'));
        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function saveReplyFinding(Request $request)
    {
        if (Auth::user()->auditor_access()) {
            $inputs = $request->input('inputs');
            parse_str($inputs, $inputs);

            $date = Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $fromtype = $inputs['fromtype'];

            if ($fromtype == 'finding') {
                $from = Finding::where('id', '=', $inputs['id'])->first();
                $finding_id = $from->id;
            } elseif ($fromtype == 'comment') {
                $from = Comment::where('id', '=', $inputs['id'])->first();
                $finding_id = $from->finding_id;
            } elseif ($fromtype == 'photo') {
                $from = Photo::where('id', '=', $inputs['id'])->first();
                $finding_id = $from->finding_id;
            } elseif ($fromtype == 'document') {
                $from = Document::where('id', '=', $inputs['id'])->first();
                $finding_id = $from->finding_id;
            } elseif ($fromtype == 'followup') {
                $from = Followup::where('id', '=', $inputs['id'])->first();
                $finding_id = $from->finding_id;
            }

            if ($inputs['type'] == 'comment') {
                if (strlen($inputs['comment']) > 0) {
                    $newcomment = new Comment([
                        'user_id' => Auth::user()->id,
                        'audit_id' => $from->audit_id,
                        'finding_id' => $finding_id,
                        'comment' => $inputs['comment'],
                        'recorded_date' => $date,
                    ]);

                    if ($fromtype == 'comment') {
                        $newcomment->comment_id = $from->id;
                    } elseif ($fromtype == 'photo') {
                        $newcomment->photo_id = $from->id;
                    } elseif ($fromtype == 'document') {
                        $newcomment->document_id = $from->id;
                    } elseif ($fromtype == 'followup') {
                        $newcomment->followup_id = $from->id;
                    }

                    $newcomment->save();
                }
                return 1;

            } elseif ($inputs['type'] == 'photo') {

            } elseif ($inputs['type'] == 'followup') {

            } elseif ($inputs['type'] == 'document') {

            }

        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function findingList($type, $amenityinspection, Request $request)
    {
        if (Auth::user()->auditor_access()) {
            $allFindingTypes = null;
            $ai = null;
            $search = $request->search;
            if (is_null($search)) {
                $search = '';
            }
            $ai = AmenityInspection::where('id', $amenityinspection)->with('amenity')->first();
            if ($ai) {
                $amenityInspectionId = $ai->id;
                // determine the amenity type
                if ($ai->building_id) {
                    $amenityLocationType = "b";
                } else if ($ai->project_id) {
                    $amenityLocationType = "p";
                } else if ($ai->unit_id) {
                    $amenityLocationType = "u";
                }
                if ($type != 'all') {
                    $allFindingTypes = $ai->amenity->finding_types();
                } else {
                    $allFindingTypes = FindingType::select('*')->get();
                }
                $allFindingTypes = $allFindingTypes->filter(function ($findingType) use ($type, $search, $amenityLocationType) {
                    if ($findingType->type == $type || $type == 'all') {
                        switch ($amenityLocationType) {
                            case 'b':
                                if (!$findingType->building_exterior && !$findingType->building_system && !$findingType->common_area) {
                                    return false;
                                }
                                break;
                            case 'p':
                                if (!$findingType->site && !$findingType->common_area) {
                                    return false;
                                }
                                break;
                            case 'u':
                                if (!$findingType->unit && !$findingType->file) {
                                    return false;
                                }
                                break;
                            case 'f':
                                if (!$findingType->file) {
                                    return false;
                                }
                                break;
                            default:
                                return false;
                                break;
                        }
                        if ($search != '') {
                            if (strpos(strtolower($findingType->name), strtolower($search)) !== false) {
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

            return view('modals.finding-types-list', compact('allFindingTypes', 'search', 'type', 'amenityLocationType', 'amenityInspectionId'));
        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function modalFindings($type, $auditid, $buildingid = null, $unitid = null, $amenityid = null, $toplevel = 0, $refresh_stream = 0)
    {
        // $toplevel is to detect top level amenities
        // a project-level amenity will appear like a building, toplevel will be set to 1 to differentiate
        // a building-level amenity will appear like a unit, toplevel will be set to 1 to differentiate

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

        if (Auth::user()->auditor_access()) {
            $audit = null;
            $building = null;
            $unit = null;
            $amenity = null;

            $buildings = null;
            $units = null;
            $amenities = null;
            $allFindings = null;

            if ($auditid > 0) {
                //$audit = CachedAudit::where('audit_id',$auditid)->with('inspection_items')->with('inspection_items.amenity.finding_types')->with('inspection_items.amenity.finding_types.boilerplates()')->first();
                $audit = CachedAudit::where('audit_id', $auditid)->with('inspection_items')->first();
            }
            if ($buildingid > 0) {
                // always use the audit id as a selector to ensure you get the correct one
                $building = CachedBuilding::where('audit_id', $auditid)->where('building_id', $buildingid)->with('building.address')->first();
                if(!$building) {
                	$building = CachedBuilding::where('audit_id', $auditid)->where('id', $buildingid)->with('building.address')->first();
                }

                //dd($buildingid, $building,$auditid);
            }
            if ($unitid > 0) {
                // always use the audit id as a selector to ensure you get the correct one
                $unit = CachedUnit::where('audit_id', $auditid)->where('unit_id', $unitid)->with('building')->with('building.address')->first();
                //dd($unit, $unitid);
            }
            if ($amenityid > 0) {
                // we use the inspection id to make sure we get the one associated that they clicked on (in case of duplicate amenities)
                $amenity = AmenityInspection::where('id', $amenityid)->first();
            }
            //dd($amenity->cached_unit()->unit_name);
            if (is_null($audit)) {
                return "alert('No audit found for ID:" . $auditid . "');";
            }

            //dd($audit);
            /// All of them for switching
            $audits = CachedAudit::where('project_id', $audit->project_id)->get()->all();

            // always use the audit id as a selector to ensure you get the correct one
            $buildings = BuildingInspection::where('audit_id', $auditid)->get();

            // foreach ($buildings as $key => $value) {
            //     return $value->units->where('building_id', $value->building_id);
            // }
            // return $buildings->first();

            // always use the audit id as a selector to ensure you get the correct one
            $units = UnitInspection::select('unit_id', 'unit_key', 'unit_name', 'building_id', 'building_key', 'audit_id', 'complete')
                ->where('audit_id', $auditid)
                ->where('complete', 0)
                ->orWhereNull('complete')
                ->groupBy('unit_id')
                ->get();

            // always use the audit id as a selector to ensure you get the correct one
            $amenities_query = AmenityInspection::where('audit_id', $auditid)->with('amenity');
            $amenities = $amenities_query->get();
            $site = $amenities_query->whereNotNull('project_id')->whereNull('completed_date_time')->get();

            $findings = Finding::where('project_id', $audit->project_id)
                ->whereNull('cancelled_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            $cancelled_findings = Finding::where('project_id', $audit->project_id)
                ->whereNotNull('cancelled_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            foreach ($cancelled_findings as $cancelled_finding) {
                $findings->add($cancelled_finding);
            }

            if (is_null($type)) {
                // default filter is all
                $type = 'all';
            }
            $checkDoneAddingFindings = 1;

            if ($refresh_stream) {
                return view('audit_stream.audit_stream', compact('audit', 'checkDoneAddingFindings', 'type', 'comments', 'findings', 'documents', 'unit', 'building', 'amenity', 'project', 'followups', 'audits', 'units', 'buildings', 'amenities', 'allFindingTypes', 'auditid', 'buildingid', 'unitid', 'amenityid', 'toplevel'));
            } else {
                return view('modals.findings', compact('audit', 'checkDoneAddingFindings', 'type', 'findings', 'unit', 'building', 'amenity', 'audits', 'units', 'buildings', 'amenities', 'auditid', 'buildingid', 'unitid', 'amenityid', 'toplevel', 'site'));
            }

        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function findingAmenities($auditid)
    {
        if (Auth::user()->auditor_access()) {
            $audit = null;
            $building = null;
            $unit = null;
            $amenity = null;

            $buildings = null;
            $units = null;
            $amenities = null;
            $allFindings = null;

            if ($auditid > 0) {
                $audit = CachedAudit::where('audit_id', $auditid)->with('inspection_items')->first();
            }
            $audits = CachedAudit::where('project_id', $audit->project_id)->get()->all();
            $buildings = BuildingInspection::where('audit_id', $auditid)->get();
            $units = UnitInspection::select('unit_id', 'unit_key', 'unit_name', 'building_id', 'building_key', 'audit_id', 'complete')
                ->where('audit_id', $auditid)
                ->where('complete', 0)
                ->orWhereNull('complete')
                ->groupBy('unit_id')
                ->get();
            $checkDoneAddingFindings = 1;

            // always use the audit id as a selector to ensure you get the correct one
            $amenities_query = AmenityInspection::where('audit_id', $auditid)->with('amenity');
            $amenities = $amenities_query->get();
            return view('modals.partials.finding-all-unit-amenities', compact('amenities', 'audit', 'buildings', 'units', 'checkDoneAddingFindings'));
        } else {
            return "Sorry, you do not have permission to access this page.";
        }
    }

    public function findingLocations($auditid)
    {
        if ($auditid > 0) {
            $audit = CachedAudit::where('audit_id', $auditid)->with('inspection_items')->first();
        }
        if (is_null($audit)) {
            return "alert('No audit found for ID:" . $auditid . "');";
        }
        $buildings = BuildingInspection::where('audit_id', $auditid)->get();
        $units = UnitInspection::select('unit_id', 'unit_key', 'unit_name', 'building_id', 'building_key', 'audit_id', 'complete')
            ->where('audit_id', $auditid)
            ->where('complete', 0)
            ->orWhereNull('complete')
            ->groupBy('unit_id')
            ->get();
        $amenities_query = AmenityInspection::where('audit_id', $auditid)->with('amenity');
        $amenities = $amenities_query->get();
        $site = $amenities_query->whereNotNull('project_id')->whereNull('completed_date_time')->get();
        return view('modals.partials.finding-locations', compact('audit', 'buildings', 'units', 'site'));
    }

    /**
     * [findingSiteAmenities description]
     * @return [partial view]              [Shows the list of site amenities]
     */
    public function findingSiteAmenities($auditid, $project_ref)
    {
        if ($auditid > 0) {
            $audit = CachedAudit::where('audit_id', $auditid)->with('inspection_items')->first();
        }
        if (is_null($audit)) {
            return "alert('No audit found for ID:" . $auditid . "');";
        }
        $buildings = BuildingInspection::where('audit_id', $auditid)->get();
        $units = UnitInspection::select('unit_id', 'unit_key', 'unit_name', 'building_id', 'building_key', 'audit_id', 'complete')
            ->where('audit_id', $auditid)
            ->where('complete', 0)
            ->orWhereNull('complete')
            ->groupBy('unit_id')
            ->get();
        $amenities_query = AmenityInspection::where('audit_id', $auditid)->with('amenity');
        $amenities = $amenities_query->get();
        $site = $amenities_query->whereNotNull('project_id')->whereNull('completed_date_time')->get();
        return view('modals.partials.finding-site-amenities', compact('audit', 'buildings', 'units', 'site', 'amenities'));
    }

    /**
     * [findingBuildingAmenities description]\
     * @return [partial view]              [Shows the list of amenities in selected building]
     */
    public function findingBuildingAmenities($auditid, $building_id)
    {
        if ($auditid > 0) {
            $audit = CachedAudit::where('audit_id', $auditid)->with('inspection_items')->first();
        }
        if (is_null($audit)) {
            return "alert('No audit found for ID:" . $auditid . "');";
        }
        $amenities_query = AmenityInspection::where('audit_id', $auditid)->with('amenity');
        $amenities = $amenities_query->get();
        $amenities = $amenities->where('building_id', $building_id)->sortBy('building_id')->sortBy('amenity_id')->sortBy('id');
        return view('modals.partials.finding-building-amenities', compact('amenities', 'audit', 'building_id'));
    }

    /**
     * [findingUnitAmenities description]
     * @return [partial view]          [Shows the list of amenities assciated with the unit]
     */
    public function findingUnitAmenities($auditid, $unit_id)
    {
        if ($auditid > 0) {
            $audit = CachedAudit::where('audit_id', $auditid)->with('inspection_items')->first();
        }
        if (is_null($audit)) {
            return "alert('No audit found for ID:" . $auditid . "');";
        }
        $amenities_query = AmenityInspection::where('audit_id', $auditid)->with('amenity');
        $amenities = $amenities_query->get();

        $amenities = $amenities->where('unit_id', $unit_id)->sortBy('unit_id')->sortBy('amenity_id')->sortBy('id');
        return view('modals.partials.finding-unit-amenities', compact('amenities', 'audit', 'building_id'));
    }

    public function findingItems($findingid, $type = null, $typeid = null)
    {
        // type and typeid used for children of items (maybe it is a comment, with comment_id)

        if (!$type || !$typeid) {
            $followups = Followup::where('finding_id', $findingid)
                ->orderBy('updated_at', 'desc')
                ->get();

            $comments = Comment::where('finding_id', $findingid)
                ->get();

            //get documents that are only on the root of the project or attached to a communication - this is only for auditors to see and above.
            // $documents = SyncDocuware::where('project_id',$audit->project_id)
            //     ->orderBy('updated_at','desc')
            //     ->get();
            $documents = Document::where('finding_id', $findingid)
                ->orderBy('updated_at', 'desc')
                ->get();

            $photos = Photo::where('finding_id', $findingid)
                ->orderBy('updated_at', 'desc')
                ->get();
        } else {

            if ($type == 'comment') {
                // comment, photo, document
                $followups = null;

                $comments = Comment::where('finding_id', $findingid)
                    ->where('comment_id', $typeid)
                    ->get();

                $documents = Document::where('finding_id', $findingid)
                    ->where('comment_id', $typeid)
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $photos = Photo::where('finding_id', $findingid)
                    ->where('comment_id', $typeid)
                    ->orderBy('updated_at', 'desc')
                    ->get();

            } elseif ($type == 'photo') {
                // comment, photo
                $followups = null;

                $comments = Comment::where('finding_id', $findingid)
                    ->where('photo_id', $typeid)
                    ->get();

                $documents = null;

                $photos = Photo::where('finding_id', $findingid)
                    ->where('photo_id', $typeid)
                    ->orderBy('updated_at', 'desc')
                    ->get();

            } elseif ($type == 'document') {
                // comment, photo
                $followups = null;

                $comments = Comment::where('finding_id', $findingid)
                    ->where('document_id', $typeid)
                    ->get();

                $documents = null;

                $photos = Photo::where('finding_id', $findingid)
                    ->where('document_id', $typeid)
                    ->orderBy('updated_at', 'desc')
                    ->get();
            } elseif ($type == 'followup') {
                // comment, photo, document
                $followups = null;

                $comments = Comment::where('finding_id', $findingid)
                    ->where('followup_id', $typeid)
                    ->get();

                $documents = Document::where('finding_id', $findingid)
                    ->where('followup_id', $typeid)
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $photos = Photo::where('finding_id', $findingid)
                    ->where('followup_id', $typeid)
                    ->orderBy('updated_at', 'desc')
                    ->get();
            }
        }

        // all those items have different formats, we need to combine, reformat and reorder.
        //
        $data = array();

        if ($comments) {
            foreach ($comments as $comment) {
                $data['items'][] = [
                    'id' => $comment->id,
                    'ref' => $comment->id,
                    'status' => '',
                    'audit' => $comment->audit_id,
                    'findingid' => $findingid,
                    'parentitemid' => $typeid,
                    'type' => 'comment',
                    'icon' => 'a-comment-text',
                    'date' => formatDate($comment->recorded_date),
                    'auditor' => [
                        'id' => $comment->user_id,
                        'name' => $comment->user->full_name(),
                    ],
                    'comment' => $comment->comment,
                    'stats' => [
                        ['type' => 'comment', 'icon' => 'a-comment', 'count' => count($comment->comments)],
                        ['type' => 'file', 'icon' => 'a-file', 'count' => count($comment->documents)],
                        ['type' => 'photo', 'icon' => 'a-picture', 'count' => count($comment->photos)],
                    ],
                    'actions' => '<div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $comment->id . ', \'comment\', \'comment\')"><i class="a-comment-plus"></i></div><div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $comment->id . ', \'document\',\'comment\')"><i class="a-file-plus"></i></div><div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $comment->id . ', \'photo\',\'comment\')"><i class="a-picture"></i></div>',
                ];
            }
        }

        if ($followups) {
            foreach ($followups as $followup) {
                // 'parentitemid' => $itemid,
                $data['items'][] = [
                    'id' => $followup->id,
                    'ref' => $followup->id,
                    'status' => '',
                    'audit' => $followup->audit_id,
                    'findingid' => $followup->finding_id,
                    'parentitemid' => $typeid,
                    'type' => 'followup',
                    'icon' => 'a-bell',
                    'duedate' => formatDate($followup->date_due),
                    'date' => formatDate($followup->created_at),
                    'assigned' => [
                        'id' => $followup->assigned_to_user_id,
                        'name' => $followup->assigned_user->full_name(),
                    ],
                    'auditor' => [
                        'id' => $followup->created_by_user_id,
                        'name' => $followup->auditor->full_name(),
                    ],
                    'stats' => [
                        ['type' => 'comment', 'icon' => 'a-comment', 'count' => count($followup->comments)],
                        ['type' => 'file', 'icon' => 'a-file', 'count' => count($followup->documents)],
                        ['type' => 'photo', 'icon' => 'a-picture', 'count' => count($followup->photos)],
                    ],
                    'actions' => '<div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $followup->id . ', \'comment\',\'followup\')"><i class="a-comment-plus"></i></div><div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $followup->id . ', \'document\',\'followup\')"><i class="a-file-plus"></i></div><div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $followup->id . ', \'photo\',\'followup\')"><i class="a-picture"></i></div>',
                ];
            }
        }

        // TBD TEST DOCS
        if ($documents) {
            foreach ($documents as $document) {
                $categories = array();

                $category_array = json_decode($document->categories, true);
                $document_categories = DocumentCategory::whereIn('id', $category_array)->where('active', '1')->orderby('document_category_name', 'asc')->get();

                foreach ($document_categories as $category) {
                    $categories[] = [
                        'id' => $category->id,
                        'name' => $category->document_category_name,
                        'status' => '',
                    ];
                }

                $data['items'][] = [
                    'id' => $document->id,
                    'ref' => $document->id,
                    'status' => '',
                    'audit' => $document->audit_id,
                    'findingid' => $document->finding_id,
                    'parentitemid' => $typeid,
                    'type' => 'file',
                    'icon' => 'a-file-left',
                    'date' => formatDate($document->created_at),
                    'auditor' => [
                        'id' => $document->user_id,
                        'name' => $document->auditor->full_name(),
                    ],
                    'categories' => $categories,
                    'file' => [
                        'id' => $document->id,
                        'name' => $document->filename,
                        'url' => $document->file_path,
                        'type' => '',
                        'size' => '',
                    ],
                    'stats' => [
                        ['type' => 'comment', 'icon' => 'a-comment', 'count' => count($document->comments)],
                        ['type' => 'photo', 'icon' => 'a-picture', 'count' => count($document->photos)],
                    ],
                    'actions' => '<div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $document->id . ', \'comment\',\'document\')"><i class="a-comment-plus"></i></div><div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $document->id . ', \'photo\',\'document\')"><i class="a-picture"></i></div>',
                ];
            }
        }

        if ($photos) {
            foreach ($photos as $photo) {

                $photos = array();

                foreach ($photo->photos as $phototo) {
                    $photos[] = [
                        'id' => $phototo->id,
                        'url' => $phototo->file_path,
                        'commentscount' => count($phototo->comments),
                    ];
                }

                $data['items'][] = [
                    'id' => $photo->id,
                    'ref' => $photo->id,
                    'status' => '',
                    'audit' => $photo->audit_id,
                    'findingid' => $findingid,
                    'parentitemid' => $typeid,
                    'type' => 'photo',
                    'icon' => 'a-picture',
                    'date' => formatDate($photo->recorded_date),
                    'auditor' => [
                        'id' => $photo->user_id,
                        'name' => $photo->user->full_name(),
                    ],
                    'photos' => $photos,
                    'comment' => $photo->comment->comment,
                    'stats' => [
                        ['type' => 'comment', 'icon' => 'a-comment', 'count' => count($photo->comments)],
                        ['type' => 'photo', 'icon' => 'a-picture', 'count' => count($photo->photos)],
                    ],
                    'actions' => '<div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $photo->id . ', \'comment\',\'photo\')"><i class="a-comment-plus"></i></div><div class="icon-circle use-hand-cursor"  onclick="addChildItem(' . $photo->id . ', \'photo\',\'photo\')"><i class="a-picture"></i></div>',
                ];
            }
        }
        return response()->json($data);

        // $data['items'] = collect([
        //         [
        //             'id' => rand(100, 10000),
        //             'ref' => '123456',
        //             'status' => 'action-required',
        //             'audit' => '20121111',
        //             'findingid' => $findingid,
        //             'parentitemid' => $itemid,
        //             'type' => 'comment',
        //             'icon' => 'a-comment-text',
        //             'date' => '12/05/2018 12:51:38 PM',
        //             'auditor' => [
        //                 'id' => 1,
        //                 'name' => 'Holly Swisher'
        //             ],
        //             'comment' => 'Custom comment based on stuff I saw...',
        //             'stats' => [
        //                 ['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 1],
        //                 ['type' => 'file', 'icon' => 'a-file-plus', 'count' => 2],
        //                 ['type' => 'photo', 'icon' => 'a-picture', 'count' => 3]
        //             ]
        //         ],
        //         [
        //             'id' => rand(100, 10000),
        //             'ref' => '333444',
        //             'status' => 'action-needed',
        //             'audit' => '20121111',
        //             'findingid' => $findingid,
        //             'parentitemid' => $itemid,
        //             'type' => 'followup',
        //             'icon' => 'a-bell-plus',
        //             'duedate' => '12/22/2018',
        //             'date' => '12/22/2018 3:51:38 PM',
        //             'assigned' => ['id' => 3, 'name' => 'PM Name Here'],
        //             'auditor' => [
        //                 'id' => 1,
        //                 'name' => 'Holly Swisher'
        //             ],
        //             'comment' => 'Auto-generated follow-up for SD with tasks and due date auto-set for same day.',
        //             'stats' => [
        //                 ['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 0],
        //                 ['type' => 'file', 'icon' => 'a-file-plus', 'count' => 0],
        //                 ['type' => 'photo', 'icon' => 'a-picture', 'count' => 0]
        //             ]
        //         ],
        //         [
        //             'id' => rand(100, 10000),
        //             'ref' => '123666',
        //             'status' => '',
        //             'audit' => '20121111',
        //             'findingid' => $findingid,
        //             'parentitemid' => $itemid,
        //             'type' => 'photo',
        //             'icon' => 'a-picture',
        //             'date' => '12/05/2018 12:51:38 PM',
        //             'auditor' => [
        //                 'id' => 1,
        //                 'name' => 'Holly Swisher'
        //             ],
        //             'photos' => [
        //                 ['id' => 22, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 2],
        //                 ['id' => 23, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 1],
        //                 ['id' => 24, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 3],
        //                 ['id' => 25, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 4],
        //                 ['id' => 26, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 6],
        //                 ['id' => 27, 'url' => 'http://fpoimg.com/420x300', 'commentscount' => 0]
        //             ],
        //             'comment' => '',
        //             'stats' => [
        //                 ['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 2],
        //                 ['type' => 'photo', 'icon' => 'a-picture', 'count' => 5]
        //             ]
        //         ],
        //         [
        //             'id' => rand(100, 10000),
        //             'ref' => '333444',
        //             'status' => 'action-required',
        //             'audit' => '20121111',
        //             'findingid' => $findingid,
        //             'parentitemid' => $itemid,
        //             'type' => 'file',
        //             'icon' => 'a-file-left',
        //             'duedate' => '12/22/2018',
        //             'date' => '12/22/2018 3:51:38 PM',
        //             'assigned' => ['id' => 3, 'name' => 'PM Name Here'],
        //             'auditor' => [
        //                 'id' => 1,
        //                 'name' => 'Holly Swisher'
        //             ],
        //             'categories' => [
        //                 ['id' => 1, 'name' => 'Category Name 1', 'status' => 'checked'],
        //                 ['id' => 2, 'name' => 'Category Name 2', 'status' => 'checked'],
        //                 ['id' => 3, 'name' => 'Category Name 3', 'status' => 'notchecked'],
        //                 ['id' => 4, 'name' => 'Category Name 4', 'status' => '']
        //             ],
        //             'file' => [
        //                 'id' => 1,
        //                 'name' => 'my_long-filename.pdf',
        //                 'url' => '#',
        //                 'type' => 'pdf',
        //                 'size' => '1.3'
        //             ],
        //             'comment' => '',
        //             'stats' => [
        //                 ['type' => 'comment', 'icon' => 'a-comment-plus', 'count' => 0],
        //                 ['type' => 'file', 'icon' => 'a-file-plus', 'count' => 0],
        //                 ['type' => 'photo', 'icon' => 'a-picture', 'count' => 0]
        //             ]
        //         ]
        // ]);
        // return response()->json($data);
    }

    public function resolveFinding(Request $request, $findingid)
    {
        $finding = Finding::where('id', $findingid)->first();

        $now = Carbon\Carbon::now()->format('Y-m-d H:i:s');

        if ($finding->auditor_approved_resolution != 1) {
            // resolve all followups
            if (count($finding->followups)) {
                foreach ($finding->followups as $followup) {
                    $followup->resolve($now);
                }
            }

            $finding->auditor_approved_resolution = 1;
            $finding->auditor_last_approved_resolution_at = $now;
            $finding->save();
        } else {
            // unresolve
            $finding->auditor_approved_resolution = 0;
            $finding->auditor_last_approved_resolution_at = null;
            $finding->save();
        }

        if ($finding->auditor_last_approved_resolution_at !== null) {
            return formatDate($finding->auditor_last_approved_resolution_at);
        } else {
            return 0;
        }
    }

    public function findingItemPhoto($finding_id, $item_id, $photo_id)
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
                        'name' => 'Holly Swisher',
                    ],
                    'comment' => 'Custom comment based on stuff I saw...',
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
                        'name' => 'Holly Swisher',
                    ],
                    'comment' => 'Second custom comment based on stuff I saw...',
                ],
            ],
        ]);
        return view('modals.photo', compact('photo'));
    }

    // function autosave(Request $request)
    // {
    //     return "done";
    // }
}

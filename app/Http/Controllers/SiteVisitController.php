<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcel;
use App\Http\Requests;
use Gate;
use App\Models\ReimbursementInvoice;
use \DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Entity;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use App\LogConverter;
use Validator;
use App\Models\Device;
use App\Models\VisitLists;
use App\Models\SiteVisits;
use Carbon;

class SiteVisitController extends Controller
{
    //
     public function __construct(){
        $this->allitapc();
    }
    
    public function index()
    {
        if (Auth::user() && Auth::user()->canManageUsers()) {
            $tab = 1;
            $parcelsListFilter = "";
            $loadSVMTab = 0;
            return view('pages.siteVisitManager', compact('tab', 'parcelsListFilter', 'filter', 'loadSVMTab'));
        } else {
            return "Sorry, you need permission to manage users to access the Site Visit Manager.";
        }
    }
    public function wipeDevice(Device $device)
    {
        if (Auth::user() && Auth::user()->canManageUsers()) {
            if (!is_null($device)) {
                $device->remote_wipe = 1;
                $device->wiped_by = Auth::user()->id;
                $device->save();
                return('<h2>Wipe Request Has Been Made</h2><hr /><p>Please refresh the tab by clicking on the "Manage Devices" tab to check on the status.</p><script>$(\'#svm-subtab-3\').trigger(\'click\');</script>');
            } else {
                return('<h2>Device not found!?</h2><hr><p>Weird, the device you tried to wipe can\'t be found in the list... try again? Maybe you were logged out? Maybe Mars is in retrograde?</p>');
            }
        }
    }
    public function deviceUsers(Request $request)
    {
        if (Auth::user() && Auth::user()->canManageUsers()) {
            $deviceId = $request->query('device_id');
            $users = VisitLists::select('users.name', 'users.email', 'users.id')->where('visit_lists.device_id', $deviceId)->groupBy('user_id')->join('users', 'users.id', 'user_id')->get()->all();
        } else {
            $users = collect();
        }

        return view('pages.device_user_list', compact('users', 'deviceId'));
    }
    public function deviceList()
    {
        if (Auth::user() && Auth::user()->canManageUsers()) {
            $devices = Device::where('registered_entity', Auth::user()->entity_id)->get()->all();
        } else {
            $devices = collect();
        }

        return view('pages.device_list', compact('devices'));
    }
    public function visitList(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // determine if they are OHFA or not
            if (Auth::user()->entity_id != 1) {
                // create values for a where clause
                $where_entity_id = Auth::user()->entity_id;
                $where_entity_id_operator = '=';
            } else {
                // they are OHFA - see them all
                $where_entity_id = 0;
                $where_entity_id_operator = '>';
            }

            // Build out the query and store it
            // start with sorting


            /// The sorting column
            $svmSortedBy = $request->query('svm_parcels_sort_by');
            /// Retain the original value submitted through the query
            if (strlen($svmSortedBy)>0) {
                // update the sort by
                session(['svm_parcels_sorted_by_query'=>$svmSortedBy]);
                $svm_parcels_sorted_by_query = $request->session()->get('svm_parcels_sorted_by_query');
            } elseif (!is_null($request->session()->get('svm_parcels_sorted_by_query'))) {
                // use the session value
                
                $svm_parcels_sorted_by_query = $request->session()->get('svm_parcels_sorted_by_query');
            } else {
                // set the default
                session(['svm_parcels_sorted_by_query'=>'12']);
                $svm_parcels_sorted_by_query = $request->session()->get('svm_parcels_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query



            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('svm_parcels_program_filter'))) {
                //Update the session
                session(['svm_parcels_program_filter' => $request->query('svm_parcels_program_filter')]);
                $svmParcelsProgramFilter = $request->session()->get('svm_parcels_program_filter');
                session(['svm_parcels_program_filter_operator' => '=']);
                $svmParcelsProgramFilterOperator = $request->session()->get('svm_parcels_program_filter_operator');
            } elseif (is_null($request->session()->get('svm_parcels_program_filter')) || $request->query('svm_parcels_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['svm_parcels_program_filter' => '%%']);
                $svmParcelsProgramFilter = $request->session()->get('svm_parcels_program_filter');
                session(['svm_parcels_program_filter_operator' => 'LIKE']);
                $svmParcelsProgramFilterOperator = $request->session()->get('svm_parcels_program_filter_operator');
            } else {
                // use values in the session
                $svmParcelsProgramFilter = $request->session()->get('svm_parcels_program_filter');
                $svmParcelsProgramFilterOperator = $request->session()->get('svm_parcels_program_filter_operator');
            }


            if (is_numeric($request->query('svm_parcels_status_filter'))) {
                //Update the session
                session(['svm_parcels_status_filter' => $request->query('svm_parcels_status_filter')]);
                $svmParcelsStatusFilter = $request->session()->get('svm_parcels_status_filter');
                session(['svm_parcels_status_filter_operator' => '=']);
                $svmParcelsStatusFilterOperator = $request->session()->get('svm_parcels_status_filter_operator');
            } elseif (is_null($request->session()->get('svm_parcels_status_filter')) || $request->query('svm_parcels_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['svm_parcels_status_filter' => '%%']);
                $svmParcelsStatusFilter = $request->session()->get('svm_parcels_status_filter');
                session(['svm_parcels_status_filter_operator' => 'LIKE']);
                $svmParcelsStatusFilterOperator = $request->session()->get('svm_parcels_status_filter_operator');
            } else {
                // use values in the session
                $svmParcelsStatusFilter = $request->session()->get('svm_parcels_status_filter');
                if ($request->session()->get('svm_parcels_status_filter_operator') == null) {
                    session(['svm_parcels_status_filter_operator' => '=']);
                }
                $svmParcelsStatusFilterOperator = $request->session()->get('svm_parcels_status_filter_operator');
            }

            if (is_numeric($request->query('svm_hfa_parcels_status_filter'))) {
                //Update the session
                session(['svm_hfa_parcels_status_filter' => $request->query('svm_hfa_parcels_status_filter')]);
                $svmHfaParcelsStatusFilter = $request->session()->get('svm_hfa_parcels_status_filter');
                if ($request->session()->get('svm_hfa_parcels_status_filter_operator') == null) {
                    session(['svm_hfa_parcels_status_filter_operator' => '=']);
                }
                $svmHfaParcelsStatusFilterOperator = $request->session()->get('svm_hfa_parcels_status_filter_operator');
            } elseif (is_null($request->session()->get('svm_hfa_parcels_status_filter')) || $request->query('svm_hfa_parcels_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['svm_hfa_parcels_status_filter' => '%%']);
                $svmHfaParcelsStatusFilter = $request->session()->get('svm_hfa_parcels_status_filter');
                session(['svm_hfa_parcels_status_filter_operator' => 'LIKE']);
                $svmHfaParcelsStatusFilterOperator = $request->session()->get('svm_hfa_parcels_status_filter_operator');
            } else {
                // use values in the session
                $svmHfaParcelsStatusFilter = $request->session()->get('svm_hfa_parcels_status_filter');
                if ($request->session()->get('svm_hfa_parcels_status_filter_operator') == null) {
                    session(['svm_hfa_parcels_status_filter_operator' => '=']);
                }
                $svmHfaParcelsStatusFilterOperator = $request->session()->get('svm_hfa_parcels_status_filter_operator');
            }

            // SET THE FILTER BADGE FOR STATUS
            if (session('svm_parcels_status_filter') != '%%') {
                $svmStatusFiltered = DB::table('property_status_options')->select('option_name')->where('id', '=', session('svm_parcels_status_filter'))->first();
                $svmStatusFiltered = $svmStatusFiltered->option_name;
            } else {
                $svmStatusFiltered = null;
            }

            if (session('svm_hfa_parcels_status_filter') != '%%') {
                $svmHfaStatusFiltered = DB::table('property_status_options')->select('option_name')->where('id', '=', session('svm_hfa_parcels_status_filter'))->first();
                $svmHfaStatusFiltered = $svmHfaStatusFiltered->option_name;
            } else {
                $svmHfaStatusFiltered = null;
            }
            
            // Insert other Filters here
            
            $svmCurrentUser = Auth::user();

            /// determin sort

            if (!is_null($svmSortedBy)) {
                switch ($request->query('svm_parcels_asc_desc')) {
                    case '1':
                        # code...
                        session(['svm_parcels_asc_desc'=> 'desc']);
                        $svmParcelsAscDesc =  $request->session()->get('svm_parcels_asc_desc');
                        session(['svm_parcels_asc_desc_opposite' => ""]);
                        $svmParcelsAscDescOpposite =  $request->session()->get('svm_parcels_asc_desc_opposite');
                        break;
                    
                    default:
                        session(['svm_parcels_asc_desc'=> 'asc']);
                        $svmParcelsAscDesc =  $request->session()->get('svm_parcels_asc_desc');
                        session(['svm_parcels_asc_desc_opposite' => 1]);
                        $svmParcelsAscDescOpposite = $request->session()->get('svm_parcels_asc_desc_opposite');
                        break;
                }
                switch ($svmSortedBy) {
                    case '1':
                        # parcel id
                        session(['svm_parcels_sort_by' => 'parcels.parcel_id']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '2':
                        # Address street
                        session(['svm_parcels_sort_by' => 'street_address']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '3':
                        # Address city
                        session(['svm_parcels_sort_by' => 'city']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '4':
                        # Address state
                        session(['svm_parcels_sort_by' =>'state_acronym']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '5':
                        # Address zip
                        session(['svm_parcels_sort_by' =>'zip']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '6':
                        # program
                        session(['svm_parcels_sort_by' => 'program_name']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '7':
                        # Cost
                        session(['svm_parcels_sort_by' => 'cost_total']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '8':
                        #  Requested
                        session(['svm_parcels_sort_by' => 'requested_total']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '9':
                        #  Approved
                        session(['svm_parcels_sort_by' => 'approved_total']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '10':
                        #  Paid
                        session(['svm_parcels_sort_by' => 'invoiced_total']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '11':
                        #  Paid
                        session(['svm_parcels_sort_by' => 'lb_property_status_name']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '12':
                        #  Date
                        //if it has site visits... order by SiteVisits date
                        // $prelim = SiteVisits::
                        //         join('parcels','parcels.id','site_visits.parcel_id')
                        //         ->where('site_visits.program_id',$svmParcelsProgramFilterOperator,$svmParcelsProgramFilter)
                        //         ->where('landbank_property_status_id',$svmParcelsStatusFilterOperator,$svmParcelsStatusFilter)
                        //         ->where('hfa_property_status_id',$svmHfaParcelsStatusFilterOperator,$svmHfaParcelsStatusFilter)
                        //         ->where('entity_id',$where_entity_id_operator, $where_entity_id)
                        //         ->count();
                        //     if($prelim > 0) {
                                session(['svm_parcels_sort_by' => 'visit_date']);
                                $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                            // }else{
                            //     session(['svm_parcels_sort_by' => 'created_at']);
                            //     $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                            // }
                        
                        break;

                    case '13':
                        #  HFA Status
                        session(['svm_parcels_sort_by' => 'hfa_property_status_name']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    case '14':
                        #  HFA Status
                        session(['svm_parcels_sort_by' => 'target_area_name']);
                        $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        break;
                    default:
                        # code...
                        // $prelim = SiteVisits::
                        //         join('parcels','parcels.id','site_visits.parcel_id')
                        //         ->where('site_visits.program_id',$svmParcelsProgramFilterOperator,$svmParcelsProgramFilter)
                        //         ->where('landbank_property_status_id',$svmParcelsStatusFilterOperator,$svmParcelsStatusFilter)
                        //         ->where('hfa_property_status_id',$svmHfaParcelsStatusFilterOperator,$svmHfaParcelsStatusFilter)
                        //         ->where('entity_id',$where_entity_id_operator, $where_entity_id)
                        //         ->count();
                        //     if($prelim > 0) {
                                session(['svm_parcels_sort_by' => 'visit_date']);
                                $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                            // }else{
                            //     session(['svm_parcels_sort_by' => 'site_visits.created_at']);
                            //     $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                            // }
                        break;
                }
            } elseif (is_null($request->session()->get('svm_parcels_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['svm_parcels_sort_by' => 'site_visits.created_at']);
                $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                session(['svm_parcels_asc_desc' => 'asc']);
                $svmParcelsAscDesc = $request->session()->get('svm_parcels_asc_desc');
                session(['svm_parcels_asc_desc_opposite' => '1']);
                $svmParcelsAscDescOpposite = $request->session()->get('svm_parcels_asc_desc_opposite');
            } else {
                // use values in the session
                $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                $svmParcelsAscDesc = $request->session()->get('svm_parcels_asc_desc');
                $svmParcelsAscDescOpposite = $request->session()->get('svm_parcels_asc_desc_opposite');
            }

            // Check if they are not a HFA or if there is a filter applied
            if ($svmStatusFiltered != null || $svmHfaStatusFiltered != null || Auth::user()->entity_type != "hfa" || $svmParcelsProgramFilterOperator != "LIKE") {
                // $svmParcels = Parcel::with('targetArea','county','state','entity','import_id','program','landbank_property_status','hfa_property_status','import_id.import.imported_by','documents','retainages','unpaidRetainages','dispositions','dispositions.status','site_visits','siteVisitLists')
                //                     ->where('program_id',$svmParcelsProgramFilterOperator,$svmParcelsProgramFilter)
                //                     ->where('landbank_property_status_id',$svmParcelsStatusFilterOperator,$svmParcelsStatusFilter)
                //                     ->where('hfa_property_status_id',$svmHfaParcelsStatusFilterOperator,$svmHfaParcelsStatusFilter)
                //                     ->where('entity_id',$where_entity_id_operator, $where_entity_id)
                //                     ->has('site_visits')
                //                     //->orderBy($svmParcelsSortBy,$svmParcelsAscDesc)
                //                     ->get();
                //                     //->all();
                //                     if($svmParcelsAscDesc == "asc"){
                //                         $svmParcels = $svmParcels->sortBy($svmParcelsSortBy);
                //                     }else{
                //                         $svmParcels = $svmParcels->sortByDesc($svmParcelsSortBy);
                //                     }

                //                     $svmTotalParcels = count($svmParcels);
                $svmParcels = SiteVisits::
                                 join('parcels', 'parcels.id', 'site_visits.parcel_id')
                                 ->join('programs', 'programs.id', 'parcels.program_id')
                                 ->join('states', 'states.id', 'parcels.state_id')
                                 ->join('target_areas', 'target_areas.id', 'parcels.target_area_id')
                                 ->join('property_status_options as hfa_property_status', 'hfa_property_status.id', 'parcels.hfa_property_status_id')
                                 ->join('property_status_options as lb_property_status', 'lb_property_status.id', 'parcels.landbank_property_status_id')
                                 ->join('users', 'site_visits.inspector_id', 'users.id')
                                 ->select('site_visits.*', 'site_visits.id as site_visit_id', 'parcels.*', 'target_areas.target_area_name', 'hfa_property_status.option_name as hfa_property_status_name', 'lb_property_status.option_name as lb_property_status_name', 'states.state_acronym', 'programs.program_name', 'users.name')
                                 //->with('targetArea','county','state','entity','import_id','program','landbank_property_status','hfa_property_status','import_id.import.imported_by','documents','retainages','unpaidRetainages','dispositions','dispositions.status','site_visits','siteVisitLists')
                                ->where('site_visits.program_id', $svmParcelsProgramFilterOperator, $svmParcelsProgramFilter)
                                ->where('parcels.landbank_property_status_id', $svmParcelsStatusFilterOperator, $svmParcelsStatusFilter)
                                ->where('parcels.hfa_property_status_id', $svmHfaParcelsStatusFilterOperator, $svmHfaParcelsStatusFilter)
                                ->where('parcels.entity_id', $where_entity_id_operator, $where_entity_id)
                                ->orderBy($svmParcelsSortBy, $svmParcelsAscDesc)
                                ->get()
                                ->all();

                $svmTotalParcels = count($svmParcels);
            //dd($svmParcels);
            } else {
                $svmParcels = null;
                $svmTotalParcels = 0;
            }
                                
            if (Auth::user()->entity_type == "hfa") {
                $svmPrograms = Parcel::join('programs', 'parcels.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->orderBy('programs.program_name')->get();
            }


            
            
            $svmStatuses = Parcel::join('property_status_options', 'parcels.landbank_property_status_id', '=', 'property_status_options.id')->select('property_status_options.option_name', 'property_status_options.id')->groupBy('property_status_options.id', 'property_status_options.option_name')->orderBy('order')->get();

            $svmHfaStatuses = Parcel::join('property_status_options', 'parcels.hfa_property_status_id', '=', 'property_status_options.id')->select('property_status_options.option_name', 'property_status_options.id')->groupBy('property_status_options.id', 'property_status_options.option_name')->orderBy('order')->get('order');
            $i = 0;

           
            return view('pages.visit_list', compact('i', 'svmParcels', 'svmTotalParcels', 'svmCurrentUser', 'svm_parcels_sorted_by_query', 'svmParcelsAscDesc', 'svmParcelsAscDescOpposite', 'svmPrograms', 'svmStatuses', 'svmParcelsProgramFilter', 'svmParcelsStatusFilter', 'svmStatusFiltered', 'svmHfaStatuses', 'svmHfaParcelsStatusFilter', 'svmHfaStatusFiltered', 'svmParcelsProgramFilterOperator'));
        } else {
            return 'Sorry you do not have access to the build list.';
        }
    }

    public function sitevisitstab(Parcel $parcel, Request $request)
    {
        // determine if they are OHFA or not
        if (Auth::user()->entity_id != 1) {
            // create values for a where clause
            $where_entity_id = Auth::user()->entity_id;
            $where_entity_id_operator = '=';
        } else {
            // they are OHFA - see them all
            $where_entity_id = 0;
            $where_entity_id_operator = '>';
        }

            // Build out the query and store it
            // start with sorting


            /// The sorting column
            $svmSortedBy = $request->query('svm_parcels_sort_by');
            /// Retain the original value submitted through the query
        if (strlen($svmSortedBy)>0) {
            // update the sort by
            session(['svm_parcels_sorted_by_query'=>$svmSortedBy]);
            $svm_parcels_sorted_by_query = $request->session()->get('svm_parcels_sorted_by_query');
        } elseif (!is_null($request->session()->get('svm_parcels_sorted_by_query'))) {
            // use the session value
                
            $svm_parcels_sorted_by_query = $request->session()->get('svm_parcels_sorted_by_query');
        } else {
            // set the default
            session(['svm_parcels_sorted_by_query'=>'12']);
            $svm_parcels_sorted_by_query = $request->session()->get('svm_parcels_sorted_by_query');
        }


            /// If a new sort has been provided
            // Rebuild the query



            // Check if there is a Program Filter Provided
        if (is_numeric($request->query('svm_parcels_program_filter'))) {
            //Update the session
            session(['svm_parcels_program_filter' => $request->query('svm_parcels_program_filter')]);
            $svmParcelsProgramFilter = $request->session()->get('svm_parcels_program_filter');
            session(['svm_parcels_program_filter_operator' => '=']);
            $svmParcelsProgramFilterOperator = $request->session()->get('svm_parcels_program_filter_operator');
        } elseif (is_null($request->session()->get('svm_parcels_program_filter')) || $request->query('svm_parcels_program_filter') == 'ALL') {
            // There is no Program Filter in the Session
            session(['svm_parcels_program_filter' => '%%']);
            $svmParcelsProgramFilter = $request->session()->get('svm_parcels_program_filter');
            session(['svm_parcels_program_filter_operator' => 'LIKE']);
            $svmParcelsProgramFilterOperator = $request->session()->get('svm_parcels_program_filter_operator');
        } else {
            // use values in the session
            $svmParcelsProgramFilter = $request->session()->get('svm_parcels_program_filter');
            $svmParcelsProgramFilterOperator = $request->session()->get('svm_parcels_program_filter_operator');
        }


        if (is_numeric($request->query('svm_parcels_status_filter'))) {
            //Update the session
            session(['svm_parcels_status_filter' => $request->query('svm_parcels_status_filter')]);
            $svmParcelsStatusFilter = $request->session()->get('svm_parcels_status_filter');
            session(['svm_parcels_status_filter_operator' => '=']);
            $svmParcelsStatusFilterOperator = $request->session()->get('svm_parcels_status_filter_operator');
        } elseif (is_null($request->session()->get('svm_parcels_status_filter')) || $request->query('svm_parcels_status_filter') == 'ALL') {
            // There is no Program Filter in the Session
            session(['svm_parcels_status_filter' => '%%']);
            $svmParcelsStatusFilter = $request->session()->get('svm_parcels_status_filter');
            session(['svm_parcels_status_filter_operator' => 'LIKE']);
            $svmParcelsStatusFilterOperator = $request->session()->get('svm_parcels_status_filter_operator');
        } else {
            // use values in the session
            $svmParcelsStatusFilter = $request->session()->get('svm_parcels_status_filter');
            if ($request->session()->get('svm_parcels_status_filter_operator') == null) {
                session(['svm_parcels_status_filter_operator' => '=']);
            }
            $svmParcelsStatusFilterOperator = $request->session()->get('svm_parcels_status_filter_operator');
        }

        if (is_numeric($request->query('svm_hfa_parcels_status_filter'))) {
            //Update the session
            session(['svm_hfa_parcels_status_filter' => $request->query('svm_hfa_parcels_status_filter')]);
            $svmHfaParcelsStatusFilter = $request->session()->get('svm_hfa_parcels_status_filter');
            if ($request->session()->get('svm_hfa_parcels_status_filter_operator') == null) {
                session(['svm_hfa_parcels_status_filter_operator' => '=']);
            }
            $svmHfaParcelsStatusFilterOperator = $request->session()->get('svm_hfa_parcels_status_filter_operator');
        } elseif (is_null($request->session()->get('svm_hfa_parcels_status_filter')) || $request->query('svm_hfa_parcels_status_filter') == 'ALL') {
            // There is no Program Filter in the Session
            session(['svm_hfa_parcels_status_filter' => '%%']);
            $svmHfaParcelsStatusFilter = $request->session()->get('svm_hfa_parcels_status_filter');
            session(['svm_hfa_parcels_status_filter_operator' => 'LIKE']);
            $svmHfaParcelsStatusFilterOperator = $request->session()->get('svm_hfa_parcels_status_filter_operator');
        } else {
            // use values in the session
            $svmHfaParcelsStatusFilter = $request->session()->get('svm_hfa_parcels_status_filter');
            if ($request->session()->get('svm_hfa_parcels_status_filter_operator') == null) {
                session(['svm_hfa_parcels_status_filter_operator' => '=']);
            }
            $svmHfaParcelsStatusFilterOperator = $request->session()->get('svm_hfa_parcels_status_filter_operator');
        }

            // SET THE FILTER BADGE FOR STATUS
        if (session('svm_parcels_status_filter') != '%%') {
            $svmStatusFiltered = DB::table('property_status_options')->select('option_name')->where('id', '=', session('svm_parcels_status_filter'))->first();
            $svmStatusFiltered = $svmStatusFiltered->option_name;
        } else {
            $svmStatusFiltered = null;
        }

        if (session('svm_hfa_parcels_status_filter') != '%%') {
            $svmHfaStatusFiltered = DB::table('property_status_options')->select('option_name')->where('id', '=', session('svm_hfa_parcels_status_filter'))->first();
            $svmHfaStatusFiltered = $svmHfaStatusFiltered->option_name;
        } else {
            $svmHfaStatusFiltered = null;
        }
            
            // Insert other Filters here
            
            $svmCurrentUser = Auth::user();

            /// determin sort

        if (!is_null($svmSortedBy)) {
            switch ($request->query('svm_parcels_asc_desc')) {
                case '1':
                    # code...
                    session(['svm_parcels_asc_desc'=> 'desc']);
                    $svmParcelsAscDesc =  $request->session()->get('svm_parcels_asc_desc');
                    session(['svm_parcels_asc_desc_opposite' => ""]);
                    $svmParcelsAscDescOpposite =  $request->session()->get('svm_parcels_asc_desc_opposite');
                    break;
                    
                default:
                    session(['svm_parecels_asc_desc'=> 'asc']);
                    $svmParcelsAscDesc =  $request->session()->get('svm_parcels_asc_desc');
                    session(['svm_parcels_asc_desc_opposite' => 1]);
                    $svmParcelsAscDescOpposite = $request->session()->get('svm_parcels_asc_desc_opposite');
                    break;
            }
            switch ($svmSortedBy) {
                case '1':
                    # parcel id
                    session(['svm_parcels_sort_by' => 'parcels.parcel_id']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '2':
                    # Address street
                    session(['svm_parcels_sort_by' => 'street_address']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '3':
                    # Address city
                    session(['svm_parcels_sort_by' => 'city']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '4':
                    # Address state
                    session(['svm_parcels_sort_by' =>'state_acronym']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '5':
                    # Address zip
                    session(['svm_parcels_sort_by' =>'zip']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '6':
                    # program
                    session(['svm_parcels_sort_by' => 'program_name']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '7':
                    # Cost
                    session(['svm_parcels_sort_by' => 'cost_total']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '8':
                    #  Requested
                    session(['svm_parcels_sort_by' => 'requested_total']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '9':
                    #  Approved
                    session(['svm_parcels_sort_by' => 'approved_total']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '10':
                    #  Paid
                    session(['svm_parcels_sort_by' => 'invoiced_total']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '11':
                    #  Paid
                    session(['svm_parcels_sort_by' => 'lb_property_status_name']);
                    $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '12':
                    #  Date
                    //if it has site visits... order by SiteVisits date
                    // $prelim = SiteVisits::
                    //         join('parcels','parcels.id','site_visits.parcel_id')
                    //         ->where('site_visits.program_id',$svmParcelsProgramFilterOperator,$svmParcelsProgramFilter)
                    //         ->where('landbank_property_status_id',$svmParcelsStatusFilterOperator,$svmParcelsStatusFilter)
                    //         ->where('hfa_property_status_id',$svmHfaParcelsStatusFilterOperator,$svmHfaParcelsStatusFilter)
                    //         ->where('entity_id',$where_entity_id_operator, $where_entity_id)
                    //         ->count();
                    //     if($prelim > 0) {
                            session(['svm_parcels_sort_by' => 'visit_date']);
                            $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        // }else{
                        //     session(['svm_parcels_sort_by' => 'created_at']);
                        //     $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        // }
                        
                    break;

                case '13':
                     #  HFA Status
                     session(['svm_parcels_sort_by' => 'hfa_property_status_name']);
                     $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                case '14':
                     #  HFA Status
                     session(['svm_parcels_sort_by' => 'target_area_name']);
                     $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                    break;
                default:
                    # code...
                    // $prelim = SiteVisits::
                    //         join('parcels','parcels.id','site_visits.parcel_id')
                    //         ->where('site_visits.program_id',$svmParcelsProgramFilterOperator,$svmParcelsProgramFilter)
                    //         ->where('landbank_property_status_id',$svmParcelsStatusFilterOperator,$svmParcelsStatusFilter)
                    //         ->where('hfa_property_status_id',$svmHfaParcelsStatusFilterOperator,$svmHfaParcelsStatusFilter)
                    //         ->where('entity_id',$where_entity_id_operator, $where_entity_id)
                    //         ->count();
                    //     if($prelim > 0) {
                            session(['svm_parcels_sort_by' => 'visit_date']);
                            $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        // }else{
                        //     session(['svm_parcels_sort_by' => 'site_visits.created_at']);
                        //     $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
                        // }
                    break;
            }
        } elseif (is_null($request->session()->get('svm_parcels_sort_by'))) {
            // no values in the session - then store in simpler variables.
            session(['svm_parcels_sort_by' => 'site_visits.created_at']);
            $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
            session(['svm_parcels_asc_desc' => 'asc']);
            $svmParcelsAscDesc = $request->session()->get('svm_parcels_asc_desc');
            session(['svm_parcels_asc_desc_opposite' => '1']);
            $svmParcelsAscDescOpposite = $request->session()->get('svm_parcels_asc_desc_opposite');
        } else {
            // use values in the session
            $svmParcelsSortBy = $request->session()->get('svm_parcels_sort_by');
            $svmParcelsAscDesc = $request->session()->get('svm_parcels_asc_desc');
            $svmParcelsAscDescOpposite = $request->session()->get('svm_parcels_asc_desc_opposite');
        }

            // Check if they are not a HFA or if there is a filter applied
        if ($svmStatusFiltered != null || $svmHfaStatusFiltered != null || Auth::user()->entity_type != "hfa" || $svmParcelsProgramFilterOperator != "LIKE") {
            // $svmParcels = Parcel::with('targetArea','county','state','entity','import_id','program','landbank_property_status','hfa_property_status','import_id.import.imported_by','documents','retainages','unpaidRetainages','dispositions','dispositions.status','site_visits','siteVisitLists')
            //                     ->where('program_id',$svmParcelsProgramFilterOperator,$svmParcelsProgramFilter)
            //                     ->where('landbank_property_status_id',$svmParcelsStatusFilterOperator,$svmParcelsStatusFilter)
            //                     ->where('hfa_property_status_id',$svmHfaParcelsStatusFilterOperator,$svmHfaParcelsStatusFilter)
            //                     ->where('entity_id',$where_entity_id_operator, $where_entity_id)
            //                     ->has('site_visits')
            //                     //->orderBy($svmParcelsSortBy,$svmParcelsAscDesc)
            //                     ->get();
            //                     //->all();
            //                     if($svmParcelsAscDesc == "asc"){
            //                         $svmParcels = $svmParcels->sortBy($svmParcelsSortBy);
            //                     }else{
            //                         $svmParcels = $svmParcels->sortByDesc($svmParcelsSortBy);
            //                     }

            //                     $svmTotalParcels = count($svmParcels);
            $svmParcels = SiteVisits::
                         join('parcels', 'parcels.id', 'site_visits.parcel_id')
                         ->join('programs', 'programs.id', 'parcels.program_id')
                         ->join('states', 'states.id', 'parcels.state_id')
                         ->join('target_areas', 'target_areas.id', 'parcels.target_area_id')
                         ->join('property_status_options as hfa_property_status', 'hfa_property_status.id', 'parcels.hfa_property_status_id')
                         ->join('property_status_options as lb_property_status', 'lb_property_status.id', 'parcels.landbank_property_status_id')
                         ->join('users', 'site_visits.inspector_id', 'users.id')
                         ->select('site_visits.*', 'site_visits.id as site_visit_id', 'parcels.*', 'target_areas.target_area_name', 'hfa_property_status.option_name as hfa_property_status_name', 'lb_property_status.option_name as lb_property_status_name', 'states.state_acronym', 'programs.program_name', 'users.name')
                         //->with('targetArea','county','state','entity','import_id','program','landbank_property_status','hfa_property_status','import_id.import.imported_by','documents','retainages','unpaidRetainages','dispositions','dispositions.status','site_visits','siteVisitLists')
                        ->where('parcels.id', '=', $parcel->id)
                        ->where('site_visits.program_id', $svmParcelsProgramFilterOperator, $svmParcelsProgramFilter)
                        ->where('parcels.landbank_property_status_id', $svmParcelsStatusFilterOperator, $svmParcelsStatusFilter)
                        ->where('parcels.hfa_property_status_id', $svmHfaParcelsStatusFilterOperator, $svmHfaParcelsStatusFilter)
                        ->where('parcels.entity_id', $where_entity_id_operator, $where_entity_id)
                        ->orderBy($svmParcelsSortBy, $svmParcelsAscDesc)
                        ->get()
                        ->all();

            $svmTotalParcels = count($svmParcels);
            //dd($svmParcels);
        } else {
            $svmParcels = null;
            $svmTotalParcels = 0;
        }
                                
        if (Auth::user()->entity_type == "hfa") {
            $svmPrograms = Parcel::join('programs', 'parcels.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->orderBy('programs.program_name')->get();
        }


            
            
            $svmStatuses = Parcel::join('property_status_options', 'parcels.landbank_property_status_id', '=', 'property_status_options.id')->select('property_status_options.option_name', 'property_status_options.id')->groupBy('property_status_options.id', 'property_status_options.option_name')->orderBy('order')->get();

            $svmHfaStatuses = Parcel::join('property_status_options', 'parcels.hfa_property_status_id', '=', 'property_status_options.id')->select('property_status_options.option_name', 'property_status_options.id')->groupBy('property_status_options.id', 'property_status_options.option_name')->orderBy('order')->get('order');
            $i = 0;

           
            return view('parcels.parcel_site_visits', compact('i', 'svmParcels', 'svmTotalParcels', 'svmCurrentUser', 'svm_parcels_sorted_by_query', 'svmParcelsAscDesc', 'svmParcelsAscDescOpposite', 'svmPrograms', 'svmStatuses', 'svmParcelsProgramFilter', 'svmParcelsStatusFilter', 'svmStatusFiltered', 'svmHfaStatuses', 'svmHfaParcelsStatusFilter', 'svmHfaStatusFiltered', 'svmParcelsProgramFilterOperator'));
    }

    public function viewVisit($site_visit = 0)
    {
        $site_visit = SiteVisits::where('id', $site_visit)->first();
            
        if ($site_visit) {
            //$site_visit = $status = \App\Models\VisitListStatusName::find($site_visit->status);
            return view('pages.site_visit_detail', compact('site_visit'));
        } else {
            return "There are no site visits matching this reference.";
        }
    }
    public function serveImages($file)
    {
        if (Gate::allows('view-all-parcels')) {
            $imagePath = \App\Models\Photo::where('filename', $file)->first();
            $storagePath = storage_path('app/'.$imagePath->file_path);
            if (! \File::exists($storagePath)) {
                return view('errorpages.404');
            } else {
                return \Image::make($storagePath)->response();
            }
        } else {
            return 'Sorry you do not have permission to view that image.';
        }
    }

    public function saveDate(Parcel $parcel, SiteVisits $sitevisit, Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            $forminputs = $request->get('inputs');
            parse_str($forminputs, $forminputs);

            if (!isset($forminputs['visit-date'])) {
                $forminputs['visit-date'] = null;
            }
            if ($forminputs['visit-date']) {
                $forminput_date_entered = $forminputs['visit-date'];
                $visit_date = Carbon\Carbon::createFromFormat('Y-m-d', $forminput_date_entered)->format('Y-m-d H:i:s');
            } else {
                $visit_date = null;
            }

            $sitevisit->update([
                'visit_date' => $visit_date
            ]);

            return 1;
        } else {
            return 0;
        }
    }
}

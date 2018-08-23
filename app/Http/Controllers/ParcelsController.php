<?php

namespace App\Http\Controllers;

use App\Models\DocumentRule;
use Auth;
use App\Models\Retainage;
use Gate;
use File;
use Storage;
use App\Models\Programs;
use Illuminate\Http\Request;
use DB;
use App\Models\Parcel;
use App\Models\Helpers\GeoData;
use App\LogConverter;
use App\Models\ExpenseCategory;
use App\Models\GuideStep;
use App\Models\User;
use App\Models\CostItem;
use App\Models\ReimbursementRule;
use App\Models\ProgramRule;

ini_set('max_execution_time', 600);

class ParcelsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        //Auth::onceUsingId(5);
    }
   
    public function payRetainage(Retainage $retainage, Request $request)
    {
        if (is_null($retainage->id)) {
            $output['error'] = 1;
            $output['message'] = "OOPS! I can\'t find what you\'re looking for...";
            return $output;
        } else {
            // get cost info
            $costItem = DB::table('cost_items')->select('*')->where('id', $retainage->cost_item_id)->first();
            if (Auth::user()->entity_type == 'landbank') {
                //check if they own the parcel
                if (Auth::user()->entity_type != $costItem->entity_id) {
                    $output['error'] = 1;
                    $output['message'] = "OOPS! Doesn\'t look like you are allowed to do this... contact your admin for help.";
                    return $output;
                } else {
                    $go = 1;
                }
            } else {
                $go = 1;
            }
        }
        if ($go == 1) {
            DB::table('retainages')->where('id', $retainage->id)->update(['paid'=>1]);
            $output['error'] = 0;
            $output['message'] = "<h1>Consider it Paid.</h1><p>Feels good, doesn\'t it?</p>";
            return $output;
        }
    }
    public function removeRetainage(Retainage $retainage, Request $request)
    {
        if (is_null($retainage->id)) {
            $output['error'] = 1;
            $output['message'] = "OOPS! I can\'t find what you\'re looking for...";
            return $output;
        } else {
            // get cost info
            $costItem = DB::table('cost_items')->select('*')->where('id', $retainage->cost_item_id)->first();
            if (Auth::user()->entity_type == 'landbank') {
                //check if they own the parcel
                if (Auth::user()->entity_type != $costItem->entity_id) {
                    $output['error'] = 1;
                    $output['message'] = "OOPS! Doesn\'t look like you are allowed to do this... contact your admin for help.";
                    return $output;
                } else {
                    $go = 1;
                }
            } else {
                $go = 1;
            }
        }
        if ($go == 1) {
            DB::table('retainages')->where('id', $retainage->id)->delete();
            $output['error'] = 0;
            $output['message'] = "<h1>Consider it Removed.</h1><p>Like a clean slate!";
            return $output;
        }
    }
    public function storeRetainage(Parcel $parcel, Request $request)
    {
        if (is_null($parcel->id)) {
            $output['error'] = 1;
            $output['message'] = "OOPS! I can\'t find what you\'re looking for...";
            return $output;
        } else {
            $costItem = DB::table('cost_items')->select('*')->where('id', $request->cost_id)->first();

            if (Auth::user()->entity_type == 'landbank') {
                //check if they own the parcel
                if (Auth::user()->entity_type != $parcel->entity_id) {
                    $output['error'] = 1;
                    $output['message'] = "OOPS! Doesn\'t look like you are allowed to do this... contact your admin for help.";
                    return $output;
                } else {
                    $go = 1;
                }
            } else {
                $go = 1;
            }
        }
        if ($go == 1) {
            DB::table('retainages')->insert([
                                            'vendor_id'=>$costItem->vendor_id,
                                            'expense_category_id'=>$costItem->expense_category_id,
                                            'parcel_id'=>$parcel->id,
                                            'cost_item_id'=>$request->cost_id,
                                            'retainage_amount'=>$request->retainage_amount
                                            ]);
            $output['message'] = "<h1>Consider it Retained.</h1><p>You\'re an unstopable force of nature!</p>";
            ;
            return $output;
        }
    }
    public function toggleSteetViewMatch(Parcel $parcel)
    {
        if (is_null($parcel->id)) {
            return "<script>alert('OOPS! I can\'t find what you\'re looking for...');</script>";
        } else {
            if (Auth::user()->entity_type == 'landbank') {
                //check if they own the parcel
                if (Auth::user()->entity_type != $parcel->entity_id) {
                    return "<script>alert('OOPS! Doesn\'t look like you are allowed to do this... contact your admin for help.');</script>";
                } else {
                    $go = 1;
                }
            } else {
                $go = 1;
            }
        }
        if ($go == 1) {
            $update = 1;
            if ($parcel->matches_street_view == 1) {
                $update = 0;
            }
            DB::table('parcels')->where('id', $parcel->id)->update(['matches_street_view'=>$update]);
            return "<script>UIkit.modal.alert('<h1>Done!</h1><p>Easy peasy... peezee? You know what I mean!</p>');</script>";
        }
    }
    public function togglePretty(Parcel $parcel)
    {
        if (is_null($parcel->id)) {
            return "<script>alert('OOPS! I can\'t find what you\'re looking for...');</script>";
        } else {
            if (Auth::user()->entity_type == 'landbank') {
                //check if they own the parcel
                if (Auth::user()->entity_type != $parcel->entity_id) {
                    return "<script>alert('OOPS! Doesn\'t look like you are allowed to do this... contact your admin for help.');</script>";
                } else {
                    $go = 1;
                }
            } else {
                $go = 1;
            }
        }
        if ($go == 1) {
            $update = 1;
            if ($parcel->pretty_lot == 1) {
                $update = 0;
            }
            DB::table('parcels')->where('id', $parcel->id)->update(['pretty_lot'=>$update]);
            if ($update == 1) {
                return "<script>UIkit.modal.alert('<h1>Done!</h1><p>It is a pretty one isn\'t it?</p>');</script>";
            } else {
                return "<script>UIkit.modal.alert('<h1>OK!</h1><p>We won\'t use this one.</p>');</script>";
            }
        }
    }

    public function toggleUgly(Parcel $parcel)
    {
        if (is_null($parcel->id)) {
            return "<script>alert('OOPS! I can\'t find what you\'re looking for...');</script>";
        } else {
            if (Auth::user()->entity_type == 'landbank') {
                //check if they own the parcel
                if (Auth::user()->entity_type != $parcel->entity_id) {
                    return "<script>alert('OOPS! Doesn\'t look like you are allowed to do this... contact your admin for help.');</script>";
                } else {
                    $go = 1;
                }
            } else {
                $go = 1;
            }
        }
        if ($go == 1) {
            $update = 1;
            if ($parcel->ugly_house == 1) {
                $update = 0;
            }
            DB::table('parcels')->where('id', $parcel->id)->update(['ugly_house'=>$update]);
            if ($update == 1) {
                return "<script>UIkit.modal.alert('<h1>Done!</h1><p>It was an ugly one wasn\'t it!</p>');</script>";
            } else {
                return "<script>UIkit.modal.alert('<h1>OK!</h1><p>We won\'t use this one.</p>');</script>";
            }
        }
    }

    public function validateParcels(Request $request)
    {
        $lc = new LogConverter('parcel', 'validate');
        $import = \App\Models\Import::find(intval($request->query('import_id')));
        
        if ($request->query('resetValidation')==1 && isset($import->id)) {
            $lc->setFrom(Auth::user())->setTo($import)->setDesc(Auth::user()->email . ' Started to run a re-validation for import '.intval($request->query('import_id')).'.')->save();
        } elseif (isset($import->id)) {
            $lc->setFrom(Auth::user())->setTo($import)->setDesc(Auth::user()->email . ' Started validation for import '.intval($request->query('import_id')).'.')->save();
        }
        
        
        if ($request->query('resetValidation') == 1) {
            DB::table('imports')->where('id', intval($request->query('import_id')))->update(['validated'=>0]);
        }
        // Get imports
        if (Auth::user()->entity_type == 'hfa') {
            $importList = DB::table('imports')->join('import_rows', 'import_id', '=', 'imports.id')->leftJoin('users', 'imports.user_id', 'users.id')->leftJoin('entities', 'imports.entity_id', 'entities.id')->select('imports.id', 'imports.created_at', 'users.name', 'entity_name')->where('import_rows.table_name', 'parcels')->where('validated', 0)->groupBy('imports.id', 'imports.created_at')->orderBy('imports.created_at', 'desc')->get()->all();
        } else {
            $importList = DB::table('imports')->join('import_rows', 'import_id', '=', 'imports.id')->leftJoin('users', 'imports.user_id', 'users.id')->leftJoin('entities', 'imports.entity_id', 'entities.id')->select('imports.id', 'imports.created_at', 'users.name', 'entity_name')->where('import_rows.table_name', 'parcels')->where('imports.entity_id', auth()->user()->entity_id)->where('validated', 0)->groupBy('imports.id', 'imports.created_at')->orderBy('imports.created_at', 'desc')->get()->all();
        }

        
        // Reset session variables on reload
        session(['validationLastRow'=> 0]);
        // Only do this request once this way.
        $totalCount = DB::table('import_rows')->where('import_id', intval($request->query('list')))->count();
        session(['validation_totalCount'=>$totalCount]);
        session(['validation_addressCount' => 0]);
        session(['validation_ohSenateCount' => 0]);
        session(['validation_ohHouseCount' => 0]);
        session(['validation_usHouseCount' => 0]);
        session(['validation_processedCount' => 0]);
        session(['validation_percentComplete' => 0]);
        session(['validation_identicalCount' => 0]);
        session(['validation_historicCount' => 0]);
        session(['validation_hhfCount' => 0]);
        $importId = $request->import_id;
        if ($importId > 0) {
            session(['disablePacer'=>1]);
            // disable pacer so they can watch the page load
        }


        return view('pages.import.validate_parcels', compact('importList', 'importId'));
    }
    public function validateParcel(Request $request)
    {
        $debugMessage = "";
        /// determine if they are HFA or LB
        if (Auth::user()->entity_type == 'hfa') {
            $entity_evaluator = "LIKE";
            $entity = "%%";
        } else {
            $entity_evaluator = "=";
            $entity = Auth::user()->entity_id;
        }
        ///// set runGeoUpdate to zero
        $updateTotals = [];
        $geoDataUpdate = [];
        $geoDataUpdateCorrection = [];
        $runGeoUpdate = 0;
        $waiver = intval($request->query('waiver'));
        $useGISAddress = intval($request->query('useGISAddress'));
        $updateAddress = intval($request->query('updateAddress'));
        $useProvidedAddress = intval($request->query('useProvidedAddress'));
        $lb_validated = 0;
        /// do the resolution.
        $resolutionId = intval($request->query('resolution_id'));
        $resolutionNote = $request->query('resolution');
        $resolutionAction = intval($request->query('action'));
        // Set base values for Retention and existing blight matching property count
        $blightCount = 0;
        $retentionCount = 0;
        $insertedValidationResolution = 0;

        $parcelLandBankStatus = 46;
        $parcelStatusReason = "This parcel was set to Ready for Costs because validation did not determine a previous status.";
        $withdraw = 0;
        // unless it encounters errors the parcel will be marked as ready for costs.
       
        ///// get the parcel info for the next parcel on the import.
        $row = intval($request->query('rowNum')) + 1;

        if ($request->query('newRequest') == 1 || $waiver == 1 || $useGISAddress == 1 || $updateAddress == 1 || $resolutionId > 0 || $useProvidedAddress == 1) {
            session(['validationLastRow'=> 0]);
            // Only do this request once this way.
            $totalCount = DB::table('import_rows')->where('import_id', intval($request->query('list')))->count();
            session(['validation_totalCount'=>$totalCount]);
            session(['validation_addressCount' => 0]);
            session(['validation_ohSenateCount' => 0]);
            session(['validation_ohHouseCount' => 0]);
            session(['validation_usHouseCount' => 0]);
            session(['validation_processedCount' => 0]);
            session(['validation_percentComplete' => 0]);
            session(['validation_identicalCount' => 0]);
            session(['validation_historicCount' => 0]);
            session(['validation_hhfCount' => 0]);
        }

        

        if ($row <= session('validation_totalCount') && $waiver != 1 && $useGISAddress != 1 && $updateAddress != 1 && $resolutionId < 1 && $useProvidedAddress != 1) {
            $parcel = DB::table('import_rows')
                            ->join('parcels', 'parcels.id', '=', 'import_rows.row_id')
                            ->join('states', 'parcels.state_id', '=', 'states.id')
                            ->select('parcels.*', 'states.state_name')
                            ->where('import_rows.import_id', intval($request->query('list')))
                            ->offset(intval($request->query('rowNum')))
                            ->limit(1)
                            ->first();
            //Determine if it has a previous status that is not the import status:
            if ($parcel->landbank_property_status_id != 5 || $parcel->landbank_property_status_id != 43 || $parcel->landbank_property_status_id != 44 || $parcel->landbank_property_status_id != 11) {
                $parcelLandBankStatus = $parcel->landbank_property_status_id;
                $parcelStatusReason = "This parcel was revalidated and retained it's previous status.";
            }
        } elseif ($request->query('waiver') != 1 && $useGISAddress != 1 && $updateAddress != 1 && $resolutionId < 1 && $useProvidedAddress != 1) {
            /// we're done!
            return "<script>UIkit.modal.alert('<h2>I finished the validation!</h2> <p>Wasn\'t that quick?</p><p>If you had any issues, please review them in the results as you can fix most right here.</p><p>If you want to review them later, you can view them in your \"Imported - Unable to Validate\" queue.</p>');</script>";
        } elseif ($waiver == 1) {
            /// This is a waiver update.
            /// first update the parcel to say they have the waiver.
            try {
                $updateWaiver = DB::table('parcels')->where('id', intval($request->query('parcelId')))->update(['historic_waiver_approved'=>1]);
            } catch (\Exception $e) {
                return "<script>UIkit.modal.alert('<h2>Unable to Update Waiver Status</h2><p>The system parcel id ".intval($request->query('parcelId'))."  was not able to be updated. Please notify your friendly neighborhood spiderma... I mean IT guy that update waiver said ".$e."</p>');</script>";
            }
            
            
            $parcel = DB::table('parcels')
                            ->join('states', 'parcels.state_id', '=', 'states.id')
                            ->select('parcels.*', 'states.state_name')
                            ->where('parcels.id', intval($request->query('parcelId')))
                            ->where('parcels.entity_id', $entity_evaluator, $entity)
                            ->first();
            if ($parcel->landbank_property_status_id != 5 || $parcel->landbank_property_status_id != 43 || $parcel->landbank_property_status_id != 44 || $parcel->landbank_property_status_id != 11) {
                $parcelLandBankStatus = $parcel->landbank_property_status_id;
                $parcelStatusReason = "This parcel was revalidated and retained it's previous status.";
            }
        } elseif ($updateAddress == 1 || $useGISAddress == 1 || $resolutionId > 0 || $useProvidedAddress == 1) {
            /// This is a address update or a resolution
            /// We will tell the store to go ahead since
            /// we just want to update this parcel though.
            
            $parcel = DB::table('parcels')
                            ->join('states', 'parcels.state_id', '=', 'states.id')
                            ->select('parcels.*', 'states.state_name')
                            ->where('parcels.id', intval($request->query('parcelId')))
                            ->where('parcels.entity_id', $entity_evaluator, $entity)
                            ->first();
            if ($parcel->landbank_property_status_id != 5 || $parcel->landbank_property_status_id != 43 || $parcel->landbank_property_status_id != 44 || $parcel->landbank_property_status_id != 11) {
                $parcelLandBankStatus = $parcel->landbank_property_status_id;
                $parcelStatusReason = "This parcel was revalidated and retained it's previous status.";
            }
        }
        if (Auth::user()->entity_type!='hfa') {
            // Check they own this parcel to do this update to it.
            if (Auth::user()->entity_id != $parcel->entity_id) {
                return "Sorry- you don't have access to this parcel.";
            }
        }

        /// Check if it is withdrawn
        if ($parcel->landbank_property_status_id == 48 || $parcel->landbank_property_status_id == 11) {
            /// it is either withdrawn or declined - skip the rest of the validation.
            $withdraw = 1;
        }

        /////////////////////////////////////////////////////////////////////////////////
        ////////////// Make sure not to overwrite the status of withdrawn
        if ($parcel->landbank_property_status_id == 48) {
            $parcelLandBankStatus = 48;
            $parcelStatusReason = "This parcel's withdrawn status was retained during validation. A withdrawn status can only be overturned by resubmitting the parcel on the parcel detail page.";
        } elseif ($parcel->landbank_property_status_id == 11) {
            $parcelLandBankStatus = 11;
            $parcelStatusReason = "This parcel's decline status was retained during validation. A declined status can only be changed by the HFA.";
        }
        if ($updateAddress == 1 && $withdraw != 1) {
            ////////////////////////////////////////////////////////////////////////////////
            /////////////// We're updating the address so we need to clear it's validation
            /////// resolutions as these are likely to change with a new address.
            DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->delete();

            ///////////////////////////////////////////////////////////////////////////////
            ////////////// Update the parcel with the new address. Use bindings to sanitize

            DB::table('parcels')    ->where('id', $parcel->id)
                                    ->update([
                                                'street_address'=>$request->get('street_address'),
                                                'city'=>$request->get('city'),
                                                'state_id'=>$request->get('state_id'),
                                                'zip'=>$request->get('zip')
                                            ]);

            ///////////////////////////////////////////////////////////////////////////
            //////////////  Update the parcel values by pulling the cleaned values back out.
            /////// While this is another query - it is safer than using the the GET values.
            $parcel = DB::table('parcels')
                            ->join('states', 'parcels.state_id', '=', 'states.id')
                            ->select('parcels.*', 'states.state_name')
                            ->where('parcels.id', intval($request->query('parcelId')))
                            ->where('parcels.entity_id', $entity_evaluator, $entity)
                            ->first();
            if ($parcel->landbank_property_status_id != 5 || $parcel->landbank_property_status_id != 43 || $parcel->landbank_property_status_id != 44 || $parcel->landbank_property_status_id != 11) {
                $parcelLandBankStatus = $parcel->landbank_property_status_id;
                $parcelStatusReason = "This parcel was revalidated and retained it's previous status.";
            }
        }
        if ($useGISAddress == 1 && $withdraw != 1) {
            ////////////////////////////////////////////////////////////////////////////////
            /////////////// We're updating the address so we need to clear it's validation
            /////// resolutions as these are likely to change with a new address.
            DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->delete();


            /// GET GIS ADDRESS
            $gisAddress = new GeoData;
            $parcelAddress = $parcel->street_address
                                .", ".$parcel->city
                                ." ".$parcel->state_name
                                ." ".$parcel->zip;
            $geodataForUpdate = $gisAddress->getGeoData($parcelAddress);
            ///////////////////////////////////////////////////////////////////////////////
            ////////////// Update the parcel with the new address. Use bindings to sanitize

            if (isset($geodataForUpdate['street_number'])) {
                $streetAddress = $geodataForUpdate['street_number'].' '.$geodataForUpdate['route'];
            } else {
                $streetAddress =  $geodataForUpdate['route'];
            }

            $stateIdforUpdate = DB::table('states')->where('state_name', $geodataForUpdate['administrative_area_level_1'])->first();
            DB::table('parcels')    ->where('id', $parcel->id)
                                    ->update([
                                                'street_address'=>$streetAddress,
                                                'city'=>$geodataForUpdate['locality'],
                                                'state_id'=>$stateIdforUpdate->id,
                                                'zip'=>$geodataForUpdate['postal_code']
                                            ]);

            ///////////////////////////////////////////////////////////////////////////
            //////////////  Update the parcel values by pulling the cleaned values back out.
            /////// While this is another query - it is safer than using the the GET values.
            $parcel = DB::table('parcels')
                            ->join('states', 'parcels.state_id', '=', 'states.id')
                            ->select('parcels.*', 'states.state_name')
                            ->where('parcels.id', intval($request->query('parcelId')))
                            ->where('parcels.entity_id', $entity_evaluator, $entity)
                            ->first();
            if ($parcel->landbank_property_status_id != 5 || $parcel->landbank_property_status_id != 43 || $parcel->landbank_property_status_id != 44 || $parcel->landbank_property_status_id != 11) {
                $parcelLandBankStatus = $parcel->landbank_property_status_id;
                $parcelStatusReason = "This parcel was revalidated and retained it's previous status.";
            }
        }
        if ($useProvidedAddress == 1 && $withdraw != 1) {
            ////////////////////////////////////////////////////////////////////////////////
            /////////////// We're updating the address so we need to clear it's validation
            /////// resolutions as these are likely to change with a new address.
            DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->delete();


            /// USE PROVIDED ADDRESS
            
            $parcelAddress = $parcel->street_address
                                .", ".$parcel->city
                                ." ".$parcel->state_name
                                ." ".$parcel->zip;
            $stateIdforUpdate = DB::table('states')->where('state_name', $geodataForUpdate['administrative_area_level_1'])->first();
            DB::table('parcels')    ->where('id', $parcel->id)
                                    ->update([
                                                'street_address'=>$geodataForUpdate['street_number'].' '.$geodataForUpdate['route'],
                                                'city'=>$geodataForUpdate['locality'],
                                                'state_id'=>$stateIdforUpdate->id,
                                                'zip'=>$geodataForUpdate['postal_code']
                                            ]);

            ///////////////////////////////////////////////////////////////////////////
            //////////////  Update the parcel values by pulling the cleaned values back out.
            /////// While this is another query - it is safer than using the the GET values.
            $parcel = DB::table('parcels')
                            ->join('states', 'parcels.state_id', '=', 'states.id')
                            ->select('parcels.*', 'states.state_name')
                            ->where('parcels.id', intval($request->query('parcelId')))
                            ->where('parcels.entity_id', $entity_evaluator, $entity)
                            ->first();
            if ($parcel->landbank_property_status_id != 5 || $parcel->landbank_property_status_id != 43 || $parcel->landbank_property_status_id != 44 || $parcel->landbank_property_status_id != 11) {
                $parcelLandBankStatus = $parcel->landbank_property_status_id;
                $parcelStatusReason = "This parcel was revalidated and retained it's previous status.";
            }
        }
        if ($resolutionId > 0 && $withdraw != 1) {
            ////////////////////////////////////////////////////////////////////////////////
            /////////////// We're updating the resolution
            /////// We will need to take the action following. Actions are coded by numbers.
            DB::table('validation_resolutions')->where('id', $resolutionId)
                ->update([
                        'resolution_lb_notes'=>$resolutionNote,
                        'lb_resolved'=>1,
                        'lb_resolved_at'=>date('Y-m-d H:i:s', time())

                    ]);

            switch ($resolutionAction) {
                case '1':
                    # Not the matched parcel - no action needed...
                    break;
                case '2':
                    # Create a Group of the matched parcel...
                    $sharedParcelId = DB::table('shared_parcel')->insertGetId([
                        'program_id'=>$parcel->program_id,
                        'created_at'=>date('Y-m-d H:i:s', time())
                    ]);
                    $matchedParcel = DB::table('validation_resolutions')->select('resolution_id')->where('id', $resolutionId)->first();
                // add parcels to table so they can be grouped.
                    $groupData = [
                        [
                        'created_at'=>date('Y-m-d H:i:s', time()),
                        'shared_parcel_id'=>$sharedParcelId,
                        'reference_letter'=>'a',
                        'parcel_id'=>$parcel->id
                        ],
                        [ //8
                        'created_at'=>date('Y-m-d H:i:s', time()),
                        'shared_parcel_id'=>$sharedParcelId,
                        'reference_letter'=>'b',
                        'parcel_id'=>$matchedParcel->resolution_id
                        ]
                    ];
                    DB::table('shared_parcel_to_parcels')->insert($groupData);

                    break;
                case '3':
                    # Withdraw parcel...
                    $Delete = \App\Models\Parcel::finde($parcel->id);

                    $Delete->deleteParcel();
                        
                    // # Delete the other validations
                    // DB::table('validation_resolutions')->where('parcel_id',$parcel->id)->delete();
                    // DB::table('validation_resolutions')->where('resolution_id',$parcel->id)->where('resolution_type','parcels')->delete();
                    // # If this was added to a parcel group - remove it
                    // $groupMember = DB::table('shared_parcel_to_parcels')->where('parcel_id',$parcel->id)->delete();
                    // $withdraw = 1;
                    exit("<script>alert('Parcel Removed, from this and your import. Rerun Validation.');</script>");

                        break;
                case '4':
                    # Add to a group of matched parcels...
                    // GET Shared Parcel Id
                    $resolutionParcel = DB::table('validation_resolutions')->select('resolution_id')->where('id', $resolutionId)->first();
                    $sharedParcelId = DB::table('shared_parcel_to_parcels')->select('shared_parcel_id')->where('parcel_id', $resolutionParcel->resolution_id)->first();
                    //Get last letter reference used in group
                    $lastLetter = DB::table('shared_parcel_to_parcels')->select('reference_letter')->where('shared_parcel_id', $sharedParcelId->shared_parcel_id)->orderBy('reference_letter', 'desc')->first();
                    $referencLetter = $lastLetter->reference_letter;
                    //get next letter
                    $referencLetter = ++$referencLetter;
                    //WHEW - Now we have what we need to store the parcel into the group.
                    //BUT - We need to make sure it doesn't exist in there first - better safe than sorry right?
                    $dupeCheck = DB::table('shared_parcel_to_parcels')->where('parcel_id', $parcel->id)->count();
                    if ($dupeCheck < 1) {
                        DB::table('shared_parcel_to_parcels')->insert([
                            'created_at'=>date('Y-m-d H:i:s', time()),
                            'shared_parcel_id'=>$sharedParcelId->shared_parcel_id,
                            'reference_letter'=>$referencLetter,
                            'parcel_id'=>$parcel->id
                        ]);
                        // Add a note stating this was done.
                        DB::table('notes')->insert([
                            'created_at'=>date('Y-m-d H:i:s', time()),
                            'parcel_id'=>$parcel->id,
                            'owner_id'=>Auth::user()->id,
                            'note'=>'Added parcel to a shared parcel group as a part of validation. It is now a part of Parcel Group Number '.$sharedParcelId->shared_parcel_id.' with reference letter '.$referencLetter.'.'
                        ]);
                    } else {
                        DB::table('notes')->insert([
                            'created_at'=>date('Y-m-d H:i:s', time()),
                            'parcel_id'=>$parcel->id,
                            'owner_id'=>Auth::user()->id,
                            'note'=>'Attempted to add the parcel to a shared parcel group as a part of validation, however - it was already in a group. So, Allita ignored the request. The group I attempted to add it to was Shared Parcel Group '.$sharedParcelId->shared_parcel_id.', and would have had the reference letter '.$referencLetter.'.'
                        ]);
                    }
                    //
                        

                    break;
                case '5':
                    # Not a match to HHF - do nothing...
                    break;
                case '6':
                    # Matched to the HHF...Withdraw parcel
                    DB::table('parcels')->where('id', $parcel->id)->update([
                        'hfa_property_status_id'=>37,
                        'landbank_property_status_id'=>48,
                        'retention_validated'=>0,
                        'address_validated'=>0,
                        'date_lb_validated'=>date('Y-m-d H:i:s', time())
                    ]);
                # Add a note to the notes table
                    DB::table('notes')->insert([
                        'created_at'=>date('Y-m-d H:i:s', time()),
                        'parcel_id'=>$parcel->id,
                        'owner_id'=>Auth::user()->id,
                        'note'=>'Withdrew parcel due to matching an previously HHF funded parcel. See parcel validation resolutions on parcel detail tab.'
                    ]);
                # Cancel the other validations
                    DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->where('lb_resolved', 0)->delete();
                    DB::table('validation_resolutions')->where('resolution_id', $parcel->id)->where('resolution_type', 'parcels')->delete();
                # If this was added to a parcel group - remove it
                    $groupMember = DB::table('shared_parcel_to_parcels')->where('parcel_id', $parcel->id)->delete();
                    if ($groupMember) {
                        // it was a member of a group - add a note to the parcel that it was removed.
                        DB::table('notes')->insert([
                        'created_at'=>date('Y-m-d H:i:s', time()),
                        'parcel_id'=>$parcel->id,
                        'owner_id'=>Auth::user()->id,
                        'note'=>'This parcel was removed from a parcel group automatically as a part of its withdraw process as a result of a validation resolution for parcel '.$parcel->parcel_id.'.'
                        ]);
                    }
                    $parcelLandBankStatus = 48;
                    $parcelStatusReason = "This parcel was elected to be withdrawn manually as a part of the validation process for parcel $parcel->parcel_id";
                    $withdraw = 1;
                    break;
                    
                default:
                    # Do nothing
                    break;
            }
        }



        if (is_null($parcel) && $waiver != 1 && $useGISAddress != 1 && $updateAddress != 1 && $resolutionId < 1) {
            /// on the off chance an imported parcel gets deleted
            return "<script>UIkit.modal.alert('<p>I wasn\'t able to lookup a parcel for row number ".$row." of import number ".intval($request->query('list')).".<br />Sorry, but I have to abort this validation.</p><p><strong>Please notify support</strong> of the row number and import number so they can investigate what happened to this parcel.</p>');</script>";
        } elseif (is_null($parcel) && $request->query('waiver') == 1 || is_null($parcel) && $request->query('useGISAddress') == 1  && $updateAddress == 1) {
            /// this is a waiver or GIS update request that was null.
            return "<script>UIkit.modal.alert('<p>I wasn\'t able to lookup a parcel for your requested id of ".intval($request->query('parcel_id')).".<br />Sorry. It is possible this parcel was either deleted, or ownership was changed from you to another landbank.</p>');</script>";
        }
        




        /// Validate address
        if ($parcel->address_validated == 0 && $withdraw != 1) {
            $addressValidator = new GeoData;
            $parcelAddress = $parcel->street_address
                                .", ".$parcel->city
                                ." ".$parcel->state_name
                                ." ".$parcel->zip;
            $geodata = $addressValidator->getGeoData($parcelAddress);

            //dd($geodata);
            if (!isset($geodata['Congressional'])) {
                session(['validation_usHouseCount' => session('validation_usHouseCount') + 1]);
                $geoDataUpdate['us_house_district'] = null;
                $runGeoUpdate = 1;
            } else {
                $geoDataUpdate['us_house_district'] = $geodata['Congressional'];
                $runGeoUpdate = 1;
            }
            if (!isset($geodata['OH House'])) {
                session(['validation_ohHouseCount' => session('validation_ohHouseCount') + 1]);
                $geoDataUpdate['oh_house_district']=null;
                $runGeoUpdate = 1;
            } else {
                $geoDataUpdate['oh_house_district']=$geodata['OH House'];
                $runGeoUpdate = 1;
            }
            if (!isset($geodata['OH Senate'])) {
                session(['validation_ohSenateCount' => session('validation_ohSenateCount') + 1]);
                $geoDataUpdate['oh_senate_district'] = null;
                $runGeoUpdate = 1;
            } else {
                $geoDataUpdate['oh_senate_district'] = $geodata['OH Senate'];
                $runGeoUpdate = 1;
            }
            if (isset($geodata['lat'])) {
                $geoDataUpdate['latitude'] = $geodata['lat'];
                $runGeoUpdate = 1;
            } else {
                $geoDataUpdate['latitude'] = null;
                $runGeoUpdate = 1;
            }
            if (isset($geodata['lng'])) {
                $geoDataUpdate['longitude'] = $geodata['lng'];
                $runGeoUpdate = 1;
            } else {
                $geoDataUpdate['longitude'] = null;
                $runGeoUpdate = 1;
            }
            if (isset($geodata['google_maps_link'])) {
                $geoDataUpdate['google_map_link'] = $geodata['google_maps_link'];
                $runGeoUpdate = 1;
            } else {
                $geoDataUpdate['google_map_link'] = null;
                $runGeoUpdate = 1;
            }
            if ((isset($geodata['geoWarning']) || isset($geodata['geoError'])) && $useGISAddress != 1) {
                session(['validation_addressCount' => session('validation_addressCount') + 1]);
                $runGeoUpdate = 0;
                $parcelLandBankStatus = 44;
                $parcelStatusReason = "Unable to validate because this parcel has an invalid GIS address.";
                $debugMessage .= "Geowarning or Error <br />";
            }
                
            //dd($geoDataUpdate, $geodata);
                
            /// store / send the geodata's version of the address
            if (isset($geodata['street_number'])) {
                $geoDataUpdateCorrection['street_number'] = $geodata['street_number'];
            } else {
                $geoDataUpdateCorrection['street_number'] = null;
            }
            if (isset($geodata['route'])) {
                $geoDataUpdateCorrection['street_name'] = $geodata['route'];
            } else {
                $geoDataUpdateCorrection['street_name'] = null;
            }
            if (isset($geodata['locality'])) {
                $geoDataUpdateCorrection['city'] = $geodata['locality'];
            } else {
                $geoDataUpdateCorrection['city'] = null;
            }
            if (isset($geodata['administrative_area_level_1'])) {
                $geoDataUpdateCorrection['state_name'] = $geodata['administrative_area_level_1'];
            } else {
                $geoDataUpdateCorrection['state_name'] = null;
            }
            if (isset($geodata['postal_code'])) {
                $geoDataUpdateCorrection['zip'] = $geodata['postal_code'];
            } else {
                $geoDataUpdateCorrection['zip'] = null;
            }
            if (isset($geodata['google_maps_link'])) {
                $geoDataUpdateCorrection['google_map_link'] = $geodata['google_maps_link'];
            } else {
                $geoDataUpdateCorrection['google_map_link'] = null;
            }
        } else {
            $geoDataUpdate['latitude'] = $parcel->latitude;
           
            $geoDataUpdate['longitude'] = $parcel->longitude;

            $geoDataUpdate['us_house_district'] = $parcel->us_house_district;
            $geoDataUpdate['oh_house_district'] = $parcel->oh_house_district;
            $geoDataUpdate['oh_senate_district'] = $parcel->oh_senate_district;
            $geoDataUpdate['google_map_link'] = $parcel->google_map_link;
        }

        

            


        // Find retention parcels within 500 feet
        if ($geoDataUpdate['latitude'] != null && $withdraw != 1) {
            $retentionFuzzy = DB::select("select * from (select *, 3956 * acos( cos( radians(?) ) *
                                cos( radians( `latitude` ) )
                                * cos( radians( `longitude` ) - (radians(?))
                                ) + sin( radians(?) ) *
                                sin( radians( `latitude` ) ) )
                              AS distance from sdo_parcels) as derivedTable WHERE distance < ? OR distance = 0 OR distance = NULL ORDER BY distance", [$geoDataUpdate['latitude'], $geoDataUpdate['longitude'], $geoDataUpdate['latitude'],  '.0094697']);

            $retentionCount = count($retentionFuzzy);
        }
        $hhf = 0;
        // if there are any - need to store them in the resolve match table
        if ($retentionCount > 0) {
            foreach ($retentionFuzzy as $data) {
                $hhf++;
                $distance = 0;
                // prevent duplicate entries
                $countValidation = DB::table('validation_resolutions')
                                            ->select('id', 'lb_resolved')
                                            ->where('parcel_id', $parcel->id)
                                            ->where('resolution_id', $data->id)
                                            ->where('resolution_type', 'sdo_parcels')
                                            ->first();
                /// does it make sense to get its status?

                if (count($countValidation) == 0) {
                    session(['validation_hhfCount' => session('validation_hhfCount')+ 1]);
                    // do insert as this one has not been reported yet.
                    $distance = $data->distance * 5280;
                    DB::table('validation_resolutions')
                                    ->insert([
                                        'parcel_id'=>$parcel->id,
                                        'resolution_type'=>'sdo_parcels',
                                        'resolution_id'=>$data->id,
                                        'resolution_system_notes'=>'A match of previously funded parcel was found within '.intval($distance).' feet. Please confirm the parcel is not the matched parcel. If the parcel is the matched parcel - this property is not eligible for reimbursement. A note from Allita\'s parcel controller at 1998.',
                                        'requires_hfa_resolution'=> 1,
                                        'created_at' => date("Y-m-d H:i:s", time()),
                                        'updated_at' => date("Y-m-d H:i:s", time())
                                        ]);
                    $parcelLandBankStatus = 44;
                    $parcelStatusReason = "Unable to validate because this parcel has a new unresolved HHF validation conflict.";
                    $insertedValidationResolution++;
                    $lb_validated = 0;
                } elseif (isset($countValidation->lb_resolved)) {
                    // issue was already reported - check its status
                    if ($countValidation->lb_resolved == 0) {
                        // issue has not been resolved - set status to 44
                        session(['validation_hhfCount' => session('validation_hhfCount')+ 1]);
                        $parcelLandBankStatus = 44;
                        $parcelStatusReason = "Unable to validate because this parcel has an unresolved HHF validation conflict.";
                    } else {
                        /// status is resolved - leave status unchanged.
                        /// BUT we need to reduce the HHF count
                        $hhf--;
                    }
                }
            }
        } else {
            /// store the variable to decalre this clear
            $geoDataUpdate['retention_validated'] = 1;
        }

        if ($geoDataUpdate['latitude'] != null && $withdraw != 1) {
            $blightFuzzy = DB::select("select * from (select *, 3956 * acos( cos( radians(?) ) *
                                cos( radians( `latitude` ) )
                                * cos( radians( `longitude` ) - (radians(?))
                                ) + sin( radians(?) ) *
                                sin( radians( `latitude` ) ) )
                              AS distance from parcels) as derivedTable WHERE distance < ? OR distance = 0 OR distance = NULL AND landbank_property_status_id != 48 AND landbank_property_status_id != 11 ORDER BY distance", [$geoDataUpdate['latitude'], $geoDataUpdate['longitude'], $geoDataUpdate['latitude'],  '.0094697']);
            /// .0947 is 500ft

            $blightCount = count($blightFuzzy);
        }
        $unique = 0;
        // if there are any - need to store them in the resolve match table
        if ($blightCount > 0) {
            $debugMessage .= "Blight Count = $blightCount <br />";
            foreach ($blightFuzzy as $data) {
                $unique++;
                $distance = 0;
                $countValidation = [];
                $countValidation = DB::table('validation_resolutions')
                                            ->select('id', 'lb_resolved')
                                            ->where('parcel_id', $parcel->id)
                                            ->where('resolution_id', $data->id)
                                            ->where('resolution_type', 'parcels')
                                            ->first();
                        
                        
                if (count($countValidation) == 0 && $data->id != $parcel->id) {
                    $debugMessage .= "CountValidation == 0 <br /> found id = $data->id and this parcel id = $parcel->id";
                    /// did not find a issue already reported ---
                    /// or it was not matched to itself.
                    /// Increment for each matched parcel found.
                    session(['validation_identicalCount' => session('validation_identicalCount')+ 1]);
                    $distance = $data->distance * 5280;
                    DB::table('validation_resolutions')
                                    ->insert([
                                        'parcel_id'=>$parcel->id,
                                        'resolution_type'=>'parcels',
                                        'resolution_id'=>$data->id,
                                        'resolution_system_notes'=>'Match to a blight parcel was found within '.intval($distance).' feet. Please confirm the parcel is not the matched parcel, or is another structure. Reimbursement will require confirmation by the HFA as well. This property may not be eligible for reimbursement.',
                                        'requires_hfa_resolution'=> 1,
                                        'created_at' => date("Y-m-d H:i:s", time()),
                                        'updated_at' => date("Y-m-d H:i:s", time())
                                        ]);
                    $insertedValidationResolution++;
                    $parcelLandBankStatus = 44;
                    $parcelStatusReason = "Unable to validate because this parcel has a new unresolved validation conflict.";
                    $lb_validated = 0;
                    $geoDataUpdate['lb_validated'] = 0;
                    $geoDataUpdate['validated_unique'] = 0;
                } elseif (isset($countValidation->lb_resolved)) {
                    // issue was already reported - check its status
                    if ($countValidation->lb_resolved == 0) {
                        $debugMessage .= "CountValidation = ".count($countValidation)." <br /> found id = $data->id and this parcel id = $parcel->id and lb_resolved = 0";
                        // issue has not been resolved - set status to 44
                        session(['validation_identicalCount' => session('validation_identicalCount')+ 1]);
                        $parcelLandBankStatus = 44;
                        if (session('validation_identicalCount') > 1) {
                            $parcelStatusReason = "Unable to validate because this parcel has an unresolved validation conflict.";
                        } else {
                            $parcelStatusReason = "Unable to validate because this parcel has an unresolved validation conflict.";
                        }
                    } else {
                        $debugMessage .= "CountValidation = ".count($countValidation)." <br /> found id = $data->id and this parcel id = $parcel->id and lb_resolved != 0";
                        /// status is resolved - leave status unchanged.
                              $unique = $unique - 1; // reduce the unique count so not to count this record.
                    }
                } elseif ($data->id != $parcel->id) {
                    // its not matched to itself but still not good.
                    $debugMessage .= "CountValidation = ".count($countValidation)." <br /> found id = $data->id and this parcel id = $parcel->id and lb_resolved was not set at all.";
                    $geoDataUpdate['validated_unique'] = 0;
                    $parcelLandBankStatus = 44;
                    $parcelStatusReason = "Unable to validate because this parcel still has unresolved validations.";
                } else {
                    // its matched to itself
                    $unique = $unique - 1;
                }
            }
        } else {
            /// declare this unique and store it in the database
            $geoDataUpdate['validated_unique'] = 1;
        }

        /// Check if it was claimed as historic
        if ($parcel->historic_significance_or_district == 1 && $parcel->historic_waiver_approved == 0 && $withdraw != 1) {
            session(['validation_historicCount' => session('validation_historicCount')+ 1]);
            $parcelLandBankStatus = 44;
            $parcelStatusReason = "Unable to validate because this does not have a historic waiver recorded.";
        }

        /// STORE GEO DATA

        // Ensure we are dealing with a parcel object.
        $parcel = \App\Models\Parcel::find($parcel->id);
        if ($runGeoUpdate == 1 && $parcel->address_validated == 0 && $withdraw != 1) {
            // No errors - go ahead and put in the geo data.j
            // Mark it as address validated
            // Add in status and validation to 1;
            $geoDataUpdate['address_validated'] = 1;

            //$geoDataUpdate['lb_validated'] = 1;
            // $geoDataUpdate['landbank_property_status_id'] = $parcelLandBankStatus; // ready to enter costs.
            DB::table('parcels')->where('id', $parcel->id)->update($geoDataUpdate);
            $parcel->address_validated = 1;
            updateStatus("parcel", $parcel, 'landbank_property_status_id', $parcelLandBankStatus, 0, $parcelStatusReason);
        } elseif ($parcel->address_validated == 0 && $withdraw != 1) {
            // update status to unable to validate
            DB::table('parcels')->where('id', $parcel->id)->update(['lb_validated'=>$lb_validated,'date_lb_validated'=>date('Y-m-d H:i:s', time())]);
            updateStatus("parcel", $parcel, 'landbank_property_status_id', $parcelLandBankStatus, 0, $parcelStatusReason);
        } elseif ($parcel->address_validated == 1 && $parcelLandBankStatus == 46 && $withdraw != 1) {
            $validated['lb_validated'] = 1;
            $validated['date_lb_validated']= date('Y-m-d H:i:s', time());
            //$validated['landbank_property_status_id'] = $parcelLandBankStatus;
            DB::table('parcels')->where('id', $parcel->id)->update($validated);

            $parcel->lb_validated = 1;
            updateStatus("parcel", $parcel, 'landbank_property_status_id', $parcelLandBankStatus, 0, $parcelStatusReason);
        } elseif ($parcel->address_validated == 1 && $parcelLandBankStatus == 44  && $withdraw != 1) {
            if ($unique > 0) {
                $validated['validated_unique'] = 0;
            }
            $validated['lb_validated'] = 0;
            //$validated['landbank_property_status_id'] = $parcelLandBankStatus;
            DB::table('parcels')->where('id', $parcel->id)->update($validated);
            updateStatus("parcel", $parcel, 'landbank_property_status_id', $parcelLandBankStatus, 0, $parcelStatusReason);
        }
        if ($waiver != 1 && $useGISAddress != 1  && $updateAddress != 1 && $resolutionId < 1) {
            session(['validationLastRow' => session('validationLastRow')+ 1]);
            session(['validation_processedCount' => session('validation_processedCount')+ 1]);
            session(['validation_percentComplete' => (session('validation_processedCount') / session('validation_totalCount'))]);


            $updateTotals = [
                            'list'=> $request->query('list'),
                            'addressCount'=>session('validation_addressCount'),
                            'usHouseCount'=>session('validation_usHouseCount'),
                            'ohHouseCount'=>session('validation_ohHouseCount'),
                            'ohSenateCount'=>session('validation_ohSenateCount'),
                            'identicalCount'=>session('validation_identicalCount'),
                            'historicCount'=>session('validation_historicCount'),
                            'hhfCount'=>session('validation_hhfCount'),
                            'totalCount'=>session('validation_totalCount'),
                            'processedCount'=>session('validation_processedCount'),
                            'percentComplete'=>session('validation_percentComplete'),
                            'rowNum'=>$row
                            ];
        }
        //check if this was the last parcel in the import, and if so, check if the entire import has been validated.
        // We don't state validated on an address update.
        $totalIssues = 0;
        if (session('validation_totalCount') == session('validation_processedCount') && $useGISAddress != 1 && $updateAddress != 1 && $waiver != 1  && $resolutionId < 1) {
            $totalResolutions = DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->where('lb_resolved', 0)->count();
            // Check other "issues" before allowing the validation
            $totalIssues += $totalResolutions;
            $totalIssues += session('validation_addressCount');
                 
            $totalIssues += session('validation_identicalCount');
            $totalIssues += session('validation_historicCount');
            $totalIssues += session('validation_hhfCount');
                 
            if ($totalIssues < 1) {
                //mark this as validated.
                $importId = DB::table('import_rows')->select('import_id')->where('row_id', $parcel->id)->where('table_name', 'parcels')->first();
                    

                // update parcels to status 46 - needing costs
                $parcelsToUpdate = DB::table('import_rows')->select('row_id')->where('import_id', $importId->import_id)->get()->all();
                foreach ($parcelsToUpdate as $parcelToUpdate) {
                    DB::table('parcels')->where('id', $parcel->id)->update(['lb_validated'=>1]);
                    //DB::table('parcels')->where('id',$parcelToUpdate->row_id)->update(['landbank_property_status_id'=>46]);
                }
                DB::table('imports')->where('id', $importId->import_id)->update(['validated'=>1]);
                $validated = 1;
                guide_set_progress($parcel->id, 24, $status = 'completed');
                perform_all_parcel_checks($parcel);
                // find out next step and cache it in db
                guide_next_pending_step(2, $parcel->id);
            } else {
                $validated = 0;
            }
        } else {
            $validated = 0;
        }

        $record = $parcel->id;
        $list = intval($request->query('list'));
        $lat = $geoDataUpdate['latitude'];
        $lon = $geoDataUpdate['longitude'];
        $congressional = $geoDataUpdate['us_house_district'];
        $ohHouse = $geoDataUpdate['oh_house_district'];
        $ohSenate = $geoDataUpdate['oh_senate_district'];
        $googleMapsLink = $geoDataUpdate['google_map_link'];
        $unresolvedLandBankCount = DB::table('validation_resolutions')->where('lb_resolved', '0')->where('parcel_id', $parcel->id)->count();
        $unresolvedHFACount = DB::table('validation_resolutions')->where('hfa_resolved', '0')->where('parcel_id', $parcel->id)->count();
        $resolutionLandBankCount =  DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->count();
        $resolutionHFALandBankCount =  DB::table('validation_resolutions')->where('requires_hfa_resolution', '1')->where('parcel_id', $parcel->id)->count();

        $debugMessage = "";
        ///////////////////////////////////////
        return view('pages.import.validate_parcel_row', compact('validated', 'useGISAddress', 'unresolvedLandBankCount', 'unresolvedHFACount', 'resolutionLandBankCount', 'resolutionHFALandBankCount', 'record', 'updateTotals', 'hhf', 'geoDataUpdateCorrection', 'parcel', 'lat', 'lon', 'congressional', 'ohHouse', 'ohSenate', 'unique', 'list', 'runGeoUpdate', 'parcelLandBankStatus', 'insertedValidationResolution', 'debugMessage', 'waiver', 'updateAddress', 'resolutionId', 'withdraw'));
    }

    public function validateHHFRetentionParcel(Request $request)
    {
        if ($request->query('newRequest') == 1) {
            session(['hhfRetentionValidationLastRow'=> 0]);
            // Only do this request once this way.
            $totalCount = DB::table('sdo_parcels')->where('latitude', null)->count();
            session(['hhf_retention_validation_totalCount'=>$totalCount]);
            session(['hhf_retention_validation_addressCount' => 0]);
            session(['hhf_retention_validation_ohSenateCount' => 0]);
            session(['hhf_retention_validation_ohHouseCount' => 0]);
            session(['hhf_retention_validation_usHouseCount' => 0]);
            session(['hhf_retention_validation_processedCount' => 0]);
            session(['hhf_retention_validation_percentComplete' => 0]);
            //dd($totalCount);
        }
        ///// get the parcel info for the next parcel on the import.
        $row = $request->query('rowNum') + 1;

        if ($row <= session('hhf_retention_validation_totalCount')) {
            $parcel = DB::table('sdo_parcels')->where('latitude', null)
                            ->offset(intval($request->query('rowNum')))
                            ->limit(1)
                            ->first();
        //dd($parcel);
        } else {
            /// we're done!
            return "<script>UIkit.modal.alert('<h2>I finished the validation!</h2> <p>Wasn\'t that quick?</p><p>If you had any issues, please review them in the results. Any invalid addresses should be addressed immediately inside Allita for HHF Rentention, and then reimported.</p>');</script>";
        }
        if (is_null($parcel)) {
            /// on the off chance an imported parcel gets deleted
            return "<script>UIkit.modal.alert('<p>I wasn\'t able to lookup a parcel for row number ".$row."<br />Sorry, but I have to abort this validation.</p><p><strong>This means someone deleted this parcel from the HHF Retention list.</strong> You can reimport the list from a current export from HHF Retention.</p>');</script>";
        }
        
        /// Validate addess
        $addressValidator = new GeoData;
        
        $parcelAddress = $parcel->street_address
                            .", ".$parcel->{"Property City"}
                            ." ".$parcel->{"Property State"}
                            ." ".$parcel->{"Property Zip"};
        $geodata = $addressValidator->getGeoData($parcelAddress);

        $runGeoUpdate = 1;
        $geoDataUpdate = [];
        if (!isset($geodata['Congressional'])) {
            session(['hhf_retention_validation_usHouseCount' => session('hhf_retention_validation_usHouseCount') + 1]);
            $geoDataUpdate['us_house_district'] = null;
            $runGeoUpdate = 1;
        } else {
            $geoDataUpdate['us_house_district'] = $geodata['Congressional'];
            $runGeoUpdate = 1;
        }
        if (!isset($geodata['OH House'])) {
            session(['hhf_retention_validation_ohHouseCount' => session('hhf_retention_validation_ohHouseCount') + 1]);
            $geoDataUpdate['oh_house_district']=null;
            $runGeoUpdate = 1;
        } else {
            $geoDataUpdate['oh_house_district']=$geodata['OH House'];
            $runGeoUpdate = 1;
        }
        if (!isset($geodata['OH Senate'])) {
            session(['hhf_retention_validation_ohSenateCount' => session('hhf_retention_validation_ohSenateCount') + 1]);
            $geoDataUpdate['oh_senate_district'] = null;
            $runGeoUpdate = 1;
        } else {
            $geoDataUpdate['oh_senate_district'] = $geodata['OH Senate'];
            $runGeoUpdate = 1;
        }
        if (isset($geodata['lat'])) {
            $geoDataUpdate['latitude'] = $geodata['lat'];
            $runGeoUpdate = 1;
        } else {
            $geoDataUpdate['latitude'] = null;
            $runGeoUpdate = 1;
        }
        if (isset($geodata['lng'])) {
            $geoDataUpdate['longitude'] = $geodata['lng'];
            $runGeoUpdate = 1;
        } else {
            $geoDataUpdate['longitude'] = null;
            $runGeoUpdate = 1;
        }

        ///// provide the values for the output

        $lat = $geoDataUpdate['latitude'];
        $lon = $geoDataUpdate['longitude'];
        $congressional = $geoDataUpdate['us_house_district'];
        $ohHouse = $geoDataUpdate['oh_house_district'];
        $ohSenate = $geoDataUpdate['oh_senate_district'];
            

        if (isset($geodata['google_maps_link'])) {
            $geoDataUpdate['google_map_link'] = $geodata['google_maps_link'];
            $googleMapsLink = $geoDataUpdate['google_map_link'];
            $runGeoUpdate = 1;
        } else {
            $geoDataUpdate['google_map_link'] = "https://www.google.com/search?q=I+could+not+find+that+house";
            $googleMapsLink = "NA";
        }
        if (isset($geodata['geoWarning']) || isset($geodata['geoError'])) {
            session(['hhf_retention_validation_addressCount' => session('hhf_retention_validation_addressCount') + 1]);
            $runGeoUpdate = 1;
        }

        /*
        // "" => "300"
        // "" => "Marconi Boulevard"
        // "" => "Columbus"
        // "" => "Ohio"

        // "postal_code" => "43215"
        */
        if (isset($geodata['street_number'])) {
            $geoDataUpdateCorrection['street_number'] = $geodata['street_number'];
        } else {
            $geoDataUpdateCorrection['street_number'] = null;
        }
        if (isset($geodata['route'])) {
            $geoDataUpdateCorrection['street_name'] = $geodata['route'];
        } else {
            $geoDataUpdateCorrection['street_name'] = null;
        }
        if (isset($geodata['locality'])) {
            $geoDataUpdateCorrection['city'] = $geodata['locality'];
        } else {
            $geoDataUpdateCorrection['city'] = null;
        }
        if (isset($geodata['administrative_area_level_1'])) {
            $geoDataUpdateCorrection['state_name'] = $geodata['administrative_area_level_1'];
        } else {
            $geoDataUpdateCorrection['state_name'] = null;
        }
        if (isset($geodata['postal_code'])) {
            $geoDataUpdateCorrection['zip'] = $geodata['postal_code'];
        } else {
            $geoDataUpdateCorrection['zip'] = null;
        }
        /// STORE GEO DATA
        if ($runGeoUpdate == 1) {
            //No errors - go ahead and put in the geo data.
            DB::table('sdo_parcels')->where('id', $parcel->id)->update($geoDataUpdate);
            //TODO: Add advanced logging here
        }
        //dd($geoDataUpdate, $geodata);


        
        session(['hhfRetentionValidationLastRow' => session('hhfRetentionValidationLastRow')+ 1]);
        session(['hhf_retention_validation_processedCount' => session('hhf_retention_validation_processedCount')+ 1]);
        session(['hhf_retention_validation_percentComplete' => (session('hhf_retention_validation_processedCount') / session('hhf_retention_validation_totalCount'))]);

        $updateTotals = [
                        'list'=> $request->query('list'),
                        'addressCount'=>session('hhf_retention_validation_addressCount'),
                        'usHouseCount'=>session('hhf_retention_validation_usHouseCount'),
                        'ohHouseCount'=>session('hhf_retention_validation_ohHouseCount'),
                        'ohSenateCount'=>session('hhf_retention_validation_ohSenateCount'),
                        'identicalCount'=>session('hhf_retention_alidation_identicalCount'),
                        'historicCount'=>session('hhf_retention_alidation_historicCount'),
                        'hhfCount'=>session('hhf_retention_validation_hhfCount'),
                        'totalCount'=>session('hhf_retention_validation_totalCount'),
                        'processedCount'=>session('hhf_retention_validation_processedCount'),
                        'percentComplete'=>session('hhf_retention_validation_percentComplete'),
                        
                        'rowNum'=>$row
                        ];

        //session()
                        
        /// DONE?
        //$request->session()->forget('key');
        

       

        return view('pages.import.validate_hhf_retention_parcel_row', compact('updateTotals', 'geoDataUpdateCorrection', 'parcel', 'lat', 'lon', 'congressional', 'ohHouse', 'ohSenate'));
    }

    public function validateImport(Request $request)
    {
        /*$reqs = DB::
                    table('parcels')
                    ->join('programs','parcels.program_id','=','programs.id')
                    ->join('entities','programs.entity_id','=','entities.id')
                    ->join('sf_reimbursements','parcels.sf_parcel_id','=','sf_reimbursements.PropertyIDRecordID')
                    ->join('sf_parcels','parcels.sf_parcel_id','=','sf_parcels.PropertyIDRecordID')
                    ->limit(1)
                    ->select('programs.program_name','entities.entity_name','parcels.sf_batch_id','parcels.parcel_id','sf_reimbursements.PropertyIDParcelID as ReimbursementPropertyIDParcelID','sf_parcels.PropertyIDParcelID as ParcelPropertyIDParcelID','sf_reimbursements.PropertyIDRecordID','sf_reimbursements.TotalRequested','sf_reimbursements.TotalApproved','sf_reimbursements.TotalCost' ,'sf_reimbursements.TotalPaid','sf_reimbursements.ProgramProgramName','sf_reimbursements.id as sf_reimbursements_id','parcels.id as allita_parcel_id','parcels.sf_parcel_id')
                    ->orderBy('parcels.id')
                    ->get()
                    ->all();*/

        $reqs = DB::select("SELECT 
                   pc.id as allita_system_id,
                   sf_parcel_id as sales_force_id,
                   pc.parcel_id as parcel_name,
                   pc.street_address,
                   pc.city,
                   pc.state_id,
                   pc.county_id,
                   st.state_name,
                   ct.county_name,
                   pc.zip,
                   p.program_name,
                   entities.*,
                   p.id as program_id,
                   p.entity_id, 
                   cs.*,
                   rqs.reimbursement_request_id,
                   rs.*,
                   pos.purchase_order_id,
                   pois.*,    
                   inv.reimbursement_invoice_id,
                   invis.*,
                   recapinv.recapture_invoice_id, 
                   dsp.disposition_id,
                   pso.option_name as HFA_Status,
                   sf_reimbursements.*
                   
                    
                   

            FROM programs p, parcels pc

            INNER JOIN property_status_options pso  
                ON pc.hfa_property_status_id = pso.id






            INNER JOIN accounts a
                ON pc.entity_id = a.entity_id






            INNER JOIN (
                SELECT entities.id as entity_id, entity_name, user_id as entity_user_id, address1 as entity_street_address, address2 as entity_street_address_2, city as entity_city, states.state_name entity_state, zip as entity_zip, phone as entity_phone, fax as entity_fax, users.name as entity_contact, users.email as entity_contact_email
                    FROM entities
                    INNER JOIN states
                    ON entities.state_id = states.id
                    
                    INNER JOIN users
                    ON entities.user_id = users.id 
            )entities
                ON pc.entity_id = entities.entity_id

            INNER JOIN states st
                ON pc.state_id = st.id

            INNER JOIN counties ct
                ON pc.county_id = ct.id

            LEFT JOIN (
                SELECT id as disposition_id, parcel_id as disposition_parcel_id
                FROM dispositions
            ) dsp
                ON pc.id = dsp.disposition_parcel_id

            LEFT JOIN (
                SELECT reimbursement_request_id, parcel_id as request_parcel_id
                FROM parcels_to_reimbursement_requests
            ) rqs
                ON pc.id = rqs.request_parcel_id

            LEFT JOIN (
                SELECT purchase_order_id, parcel_id as po_parcel_id
                FROM parcels_to_purchase_orders
            ) pos
                ON pc.id = pos.po_parcel_id

            LEFT JOIN (
                SELECT parcel_id as invoice_parcel_id, reimbursement_invoice_id
                FROM parcels_to_reimbursement_invoices
            ) inv
               ON pc.id = inv.invoice_parcel_id   

            LEFT JOIN (
                SELECT parcel_id as recapture_invoice_parcel_id, recapture_invoice_id
                FROM parcels_to_recapture_invoices
            ) recapinv
               ON pc.id = recapinv.recapture_invoice_parcel_id

               

            LEFT JOIN (
            SELECT c.parcel_id as cost_parcel_id,
                   SUM(CASE WHEN c.expense_category_id = 9 THEN c.amount ELSE 0 END) AS NIPLoanCost,
                       SUM(CASE WHEN c.expense_category_id = 2 THEN c.amount ELSE 0 END) AS AcquisitionCost,
                       SUM(CASE WHEN c.expense_category_id = 3 THEN c.amount ELSE 0 END) AS PreDemoCost,
                       SUM(CASE WHEN c.expense_category_id = 4 THEN c.amount ELSE 0 END) AS DemolitionCost,
                       SUM(CASE WHEN c.expense_category_id = 5 THEN c.amount ELSE 0 END) AS GreeningCost,
                       SUM(CASE WHEN c.expense_category_id = 6 THEN c.amount ELSE 0 END) AS MaintenanceCost,
                       SUM(CASE WHEN c.expense_category_id = 7 THEN c.amount ELSE 0 END) AS AdministrationCost,
                       SUM(CASE WHEN c.expense_category_id = 8 THEN c.amount ELSE 0 END) AS OtherCost,
            COALESCE(SUM(c.amount),0) as TotalCost
            FROM cost_items c
            GROUP BY c.parcel_id
            ) cs
                ON pc.id = cs.cost_parcel_id


            LEFT JOIN (
            SELECT r.parcel_id as request_parcel_id,
                   SUM(CASE WHEN r.expense_category_id = 9 THEN r.amount ELSE 0 END) AS NIPLoanRequest,
                       SUM(CASE WHEN r.expense_category_id = 2 THEN r.amount ELSE 0 END) AS AcquisitionRequest,
                       SUM(CASE WHEN r.expense_category_id = 3 THEN r.amount ELSE 0 END) AS PreDemoRequest,
                       SUM(CASE WHEN r.expense_category_id = 4 THEN r.amount ELSE 0 END) AS DemolitionRequest,
                       SUM(CASE WHEN r.expense_category_id = 5 THEN r.amount ELSE 0 END) AS GreeningRequest,
                       SUM(CASE WHEN r.expense_category_id = 6 THEN r.amount ELSE 0 END) AS MaintenanceRequest,
                       SUM(CASE WHEN r.expense_category_id = 7 THEN r.amount ELSE 0 END) AS AdministrationRequest,
                       SUM(CASE WHEN r.expense_category_id = 8 THEN r.amount ELSE 0 END) AS OtherRequest,
            COALESCE(SUM(r.amount),0) as total_requested
            FROM request_items r
            GROUP BY r.parcel_id
            ) rs
                ON pc.id = rs.request_parcel_id

            LEFT JOIN (
            SELECT poi.parcel_id as poi_parcel_id,
                       SUM(CASE WHEN poi.expense_category_id = 9 THEN poi.amount ELSE 0 END) AS NIPLoanApproved,
                       SUM(CASE WHEN poi.expense_category_id = 2 THEN poi.amount ELSE 0 END) AS AcquisitionApproved,
                       SUM(CASE WHEN poi.expense_category_id = 3 THEN poi.amount ELSE 0 END) AS PreDemoApproved,
                       SUM(CASE WHEN poi.expense_category_id = 4 THEN poi.amount ELSE 0 END) AS DemolitionApproved,
                       SUM(CASE WHEN poi.expense_category_id = 5 THEN poi.amount ELSE 0 END) AS GreeningApproved,
                       SUM(CASE WHEN poi.expense_category_id = 6 THEN poi.amount ELSE 0 END) AS MaintenanceApproved,
                       SUM(CASE WHEN poi.expense_category_id = 7 THEN poi.amount ELSE 0 END) AS AdministrationApproved,
                       SUM(CASE WHEN poi.expense_category_id = 8 THEN poi.amount ELSE 0 END) AS OtherApproved,
            COALESCE(SUM(poi.amount),0) as total_approved
            FROM po_items poi
            GROUP BY poi.parcel_id
            ) pois
                ON pc.id = pois.poi_parcel_id


            LEFT JOIN (
            SELECT invi.parcel_id as invoice_parcel_id,
                       SUM(CASE WHEN invi.expense_category_id = 9 THEN invi.amount ELSE 0 END) AS NIPLoanInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 2 THEN invi.amount ELSE 0 END) AS AcquisitionInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 3 THEN invi.amount ELSE 0 END) AS PreDemoInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 4 THEN invi.amount ELSE 0 END) AS DemolitionInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 5 THEN invi.amount ELSE 0 END) AS GreeningInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 6 THEN invi.amount ELSE 0 END) AS MaintenanceInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 7 THEN invi.amount ELSE 0 END) AS AdministrationInvoiced,
                       SUM(CASE WHEN invi.expense_category_id = 8 THEN invi.amount ELSE 0 END) AS OtherInvoiced,
            COALESCE(SUM(invi.amount),0) as total_invoiced
            FROM invoice_items invi
            GROUP BY invi.parcel_id
            ) invis
                ON pc.id = invis.invoice_parcel_id

            LEFT JOIN sf_reimbursements
            ON pc.sf_parcel_id = sf_reimbursements.PropertyIDRecordID


            WHERE  a.id = pc.account_id
            AND p.entity_id = pc.entity_id
            ORDER BY pc.id
            ");


               
        $process = session('process');
        // Reset session for process
        session(['process'=>'']);
        return view('parcels.sf_reimbursements', compact('process', 'reqs'));
    }

    
    public function show(Parcel $parcel)
    {
        // make sure user belongs to the parcel's entity
        if (!Auth::user()->isFromEntity($parcel->entity_id) && !Auth::user()->isFromEntity(1)) {
            return "You are not allowed to view this parcel.";
        }

        $lc = new LogConverter('parcel', 'view');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' Viewed parcel')->save();

        //return view('parcels.parcel_tabs',compact('parcel'));
        return view('parcels.parcel_tabs', compact('parcel'));
    }
    public function detail(Parcel $parcel, Request $request)
    {
        // make sure user belongs to the parcel's entity
        if (!Auth::user()->isFromEntity($parcel->entity_id) && !Auth::user()->isFromEntity(1)) {
            return "You are not allowed to view this parcel.";
        }
        
        $lc = new LogConverter('parcel', 'view');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' Viewed detailed parcel')->save();
        
        $costs = DB::table("cost_items")->join('expense_categories', 'expense_categories.id', '=', 'cost_items.expense_category_id')->join('vendors', 'vendors.id', 'cost_items.vendor_id')->select('cost_items.id', 'amount', 'expense_category_name', 'expense_category_id', 'vendor_name', 'vendor_id', 'cost_items.description')->where('cost_items.parcel_id', $parcel->id)->get()->all();
        
        //$retainage = DB::table("retainages")->join('cost_items','cost_item_id','=','cost_items.id')->join('expense_categories','expense_categories.id','=','retainages.expense_category_id')->join('vendors','vendors.id','retainages.vendor_id')->select('retainages.*','expense_category_name','description','vendor_name','amount')->where('retainages.parcel_id',$parcel->id)->get()->all();
        $retainage = $parcel->retainages;

        $parcelData = DB::select(DB::raw("SELECT
       pc.id as allita_system_id,
       sf_parcel_id as sales_force_id,
       pc.parcel_id as parcel_name,
       pc.street_address,
       pc.city,
       pc.state_id,
       pc.county_id,
       pc.matches_street_view,
       pc.landbank_property_status_id,
       st.state_name,
       ct.county_name,
       pc.zip,
       pc.units,
       pgr.id as program_rules_id,
       pgr.rules_name,
        pgr.hfa,
        pgr.rules_name,
        pgr.acquisition_advance,
        pgr.acquisition_max_advance,
        pgr.pre_demo_advance,
        pgr.pre_demo_max_advance,
        pgr.demolition_max_advance,
        pgr.demolition_advance,
        pgr.greening_advance,
        pgr.greening_max_advance,
        pgr.maintenance_advance,
        pgr.maintenance_max_advance,
        pgr.administration_advance,
        pgr.administration_max_advance,
        pgr.other_advance,
        pgr.other_max_advance,
        pgr.nip_loan_payoff_advance,
        pgr.nip_loan_payoff_max_advance,
        pgr.acquisition_max,
        pgr.pre_demo_max,
        pgr.demolition_max,
        pgr.greening_max,
        pgr.maintenance_max,
        pgr.admin_max_percent,
        pgr.other_max,
        pgr.nip_loan_payoff_max,
        pgr.acquisition_min,
        pgr.pre_demo_min,
        pgr.demolition_min,
        pgr.greening_min,
        pgr.maintenance_min,
        pgr.admin_min,
        pgr.other_min,
        pgr.nip_loan_payoff_min,

        pgr.parcel_total_max,
        pgr.maintenance_recap_pro_rate,
        pgr.imputed_cost_per_parcel,
        pgr.notes,

       p.program_name,
       entities.*,
       p.id as program_id,
       p.entity_id,
       cs.*,
       rqs.reimbursement_request_id,
       rs.*,
       pos.purchase_order_id,
       pois.*,
       inv.reimbursement_invoice_id,
       invis.*,
       recapinv.recapture_invoice_id,
       dsp.disposition_id,
       hfapso.option_name as HFA_Status,
       lbpso.option_name as LB_Status,
       hao.how_acquired_option_name as how_acquired,
       ta.target_area_name
       #sf_reimbursements.*
        FROM programs p, parcels pc

        INNER JOIN program_rules pgr
            ON pc.program_rules_id = pgr.id

        INNER JOIN property_status_options lbpso
            ON pc.landbank_property_status_id = lbpso.id

        INNER JOIN property_status_options hfapso
            ON pc.hfa_property_status_id = hfapso.id

        INNER JOIN target_areas ta
            ON pc.target_area_id = ta.id


        INNER JOIN how_acquired_options hao
            ON pc.how_acquired_id = hao.id






        INNER JOIN accounts a
            ON pc.entity_id = a.entity_id

        INNER JOIN (
            SELECT entities.id as entity_id, entity_name, user_id as entity_user_id, address1 as entity_street_address, address2 as entity_street_address_2, city as entity_city, states.state_name entity_state, zip as entity_zip, phone as entity_phone, fax as entity_fax, users.name as entity_contact, users.email as entity_contact_email
                FROM entities
                INNER JOIN states
                ON entities.state_id = states.id

                INNER JOIN users
                ON entities.user_id = users.id
        )entities
            ON pc.entity_id = entities.entity_id

        INNER JOIN states st
            ON pc.state_id = st.id

        INNER JOIN counties ct
            ON pc.county_id = ct.id

        LEFT JOIN (
            SELECT id as disposition_id, parcel_id as disposition_parcel_id
            FROM dispositions
        ) dsp
            ON pc.id = dsp.disposition_parcel_id

        LEFT JOIN (
            SELECT reimbursement_request_id, parcel_id as request_parcel_id
            FROM parcels_to_reimbursement_requests
        ) rqs
            ON pc.id = rqs.request_parcel_id

        LEFT JOIN (
            SELECT purchase_order_id, parcel_id as po_parcel_id
            FROM parcels_to_purchase_orders
        ) pos
            ON pc.id = pos.po_parcel_id

        LEFT JOIN (
            SELECT parcel_id as invoice_parcel_id, reimbursement_invoice_id
            FROM parcels_to_reimbursement_invoices
        ) inv
           ON pc.id = inv.invoice_parcel_id

        LEFT JOIN (
            SELECT parcel_id as recapture_invoice_parcel_id, recapture_invoice_id
            FROM parcels_to_recapture_invoices
        ) recapinv
           ON pc.id = recapinv.recapture_invoice_parcel_id



        LEFT JOIN (
        SELECT c.parcel_id as cost_parcel_id,
                   SUM(CASE WHEN c.expense_category_id = 9 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS NIPLoanCost,
                   SUM(CASE WHEN c.expense_category_id = 9 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS NIPLoanAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 2 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS AcquisitionCost,
                   SUM(CASE WHEN c.expense_category_id = 2 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS AcquisitionAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 3 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS PreDemoCost,
                   SUM(CASE WHEN c.expense_category_id = 3 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS PreDemoAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 4 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS DemolitionCost,
                   SUM(CASE WHEN c.expense_category_id = 4 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS DemolitionAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 5 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS GreeningCost,
                   SUM(CASE WHEN c.expense_category_id = 5 AND c.breakout_type = 3 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS GreeningAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 6 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS MaintenanceCost,
                   SUM(CASE WHEN c.expense_category_id = 6 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS MaintenanceAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 7 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS AdministrationCost,
                   SUM(CASE WHEN c.expense_category_id = 7 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS AdministrationAdvanceCost,

                   SUM(CASE WHEN c.expense_category_id = 8 AND c.breakout_type = 1 THEN c.amount ELSE 0 END) AS OtherCost,
                   SUM(CASE WHEN c.expense_category_id = 8 AND c.breakout_type = 3 THEN c.amount ELSE 0 END) AS OtherAdvanceCost,

        COALESCE(SUM(c.amount),0) as total_cost
        FROM cost_items c
        GROUP BY c.parcel_id
        ) cs
            ON pc.id = cs.cost_parcel_id


        LEFT JOIN (
        SELECT r.parcel_id as request_parcel_id, r.req_id,
                   SUM(CASE WHEN r.expense_category_id = 9 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS NIPLoanRequested,
                   SUM(CASE WHEN r.expense_category_id = 9 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS NIPLoanAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 2 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS AcquisitionRequested,
                   SUM(CASE WHEN r.expense_category_id = 2 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS AcquisitionAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 3 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS PreDemoRequested,
                   SUM(CASE WHEN r.expense_category_id = 3 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS PreDemoAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 4 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS DemolitionRequested,
                   SUM(CASE WHEN r.expense_category_id = 4 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS DemolitionAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 5 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS GreeningRequested,
                   SUM(CASE WHEN r.expense_category_id = 5 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS GreeningAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 6 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS MaintenanceRequested,
                   SUM(CASE WHEN r.expense_category_id = 6 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS MaintenanceAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 7 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS AdministrationRequested,
                   SUM(CASE WHEN r.expense_category_id = 7 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS AdministrationAdvanceRequested,

                   SUM(CASE WHEN r.expense_category_id = 8 AND r.breakout_type = 1 THEN r.amount ELSE 0 END) AS OtherRequested,
                   SUM(CASE WHEN r.expense_category_id = 8 AND r.breakout_type = 3 THEN r.amount ELSE 0 END) AS OtherAdvanceRequested,

        COALESCE(SUM(r.amount),0) as total_requested
        FROM request_items r
        GROUP BY r.parcel_id, r.req_id
        ) rs
            ON pc.id = rs.request_parcel_id



        LEFT JOIN (
        SELECT poi.parcel_id as poi_parcel_id,
                    poi.po_id,
                   SUM(CASE WHEN poi.expense_category_id = 9 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS NIPLoanApproved,
                   SUM(CASE WHEN poi.expense_category_id = 9 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS NIPLoanAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 2 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS AcquisitionApproved,
                   SUM(CASE WHEN poi.expense_category_id = 2 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS AcquisitionAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 3 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS PreDemoApproved,
                   SUM(CASE WHEN poi.expense_category_id = 3 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS PreDemoAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 4 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS DemolitionApproved,
                   SUM(CASE WHEN poi.expense_category_id = 4 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS DemolitionAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 5 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS GreeningApproved,
                   SUM(CASE WHEN poi.expense_category_id = 5 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS GreeningAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 6 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS MaintenanceApproved,
                   SUM(CASE WHEN poi.expense_category_id = 6 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS MaintenanceAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 7 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS AdministrationApproved,
                   SUM(CASE WHEN poi.expense_category_id = 7 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS AdministrationAdvanceApproved,

                   SUM(CASE WHEN poi.expense_category_id = 8 AND poi.breakout_type = 1 THEN poi.amount ELSE 0 END) AS OtherApproved,
                   SUM(CASE WHEN poi.expense_category_id = 8 AND poi.breakout_type = 3 THEN poi.amount ELSE 0 END) AS OtherAdvanceApproved,

        COALESCE(SUM(poi.amount),0) as total_approved
        FROM po_items poi
        GROUP BY poi.parcel_id,poi.po_id
        ) pois
            ON pc.id = pois.poi_parcel_id


        LEFT JOIN (
        SELECT invi.parcel_id as invoice_parcel_id,
                    invi.invoice_id,
                   SUM(CASE WHEN invi.expense_category_id = 9 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS NIPLoanInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 9 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS NIPLoanAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 2 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS AcquisitionInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 2 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS AcquisitionAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 3 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS PreDemoInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 3 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS PreDemoAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 4 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS DemolitionInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 4 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS DemolitionAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 5 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS GreeningInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 5 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS GreeningAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 6 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS MaintenanceInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 6 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS MaintenanceAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 7 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS AdministrationInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 7 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS AdministrationAdvanceInvoiced,

                   SUM(CASE WHEN invi.expense_category_id = 8 AND invi.breakout_type = 1 THEN invi.amount ELSE 0 END) AS OtherInvoiced,
                   SUM(CASE WHEN invi.expense_category_id = 8 AND invi.breakout_type = 3 THEN invi.amount ELSE 0 END) AS OtherAdvanceInvoiced,

        COALESCE(SUM(invi.amount),0) as total_invoiced
        FROM invoice_items invi
        GROUP BY invi.parcel_id, invi.invoice_id
        ) invis
            ON pc.id = invis.invoice_parcel_id

        #LEFT JOIN sf_reimbursements
        #ON pc.sf_parcel_id = sf_reimbursements.PropertyIDRecordID


        WHERE   p.entity_id = pc.entity_id
        AND pc.id = ".$parcel->id));
        
        //dd($parcel);
        $hasResolutions = 0;
        if (isset($parcelData[0])) {
            $parcelData = $parcelData[0];
        } else {
            //$parcelData = "I don't have any parcel data. This is weird.";
            return "I am unable to find this parcel's data.";
        }
        //dd($parcelData);
        $program_owner = DB::table('programs')
        ->join('entities', 'programs.owner_id', '=', 'entities.id')
        ->join('users', 'entities.owner_id', '=', 'users.id')
        ->join('states', 'states.id', '=', 'entities.state_id')
        ->join('counties', 'counties.id', '=', 'programs.county_id')
        ->where('programs.id', $parcelData->program_id)->first();
        //dd($program_owner);


        //dd($parcelData);
        $rules = DB::table('program_rules')->select('*')->where('active', '1')->orWhere('for_parcel', $parcel->id)->orderBy('for_parcel', 'DESC')->get()->all();

        // get associated program_id
        $associated_id = Parcel::where('id', $parcel->id)->pluck('program_rules_id');

        //get minimum amounts
        $minimums = DocumentRule::where('program_rules_id', $associated_id)->get();

        $minimumRules = [];
        $minimumRules['acquisition']=null;
        $minimumRules['pre_demo']=null;
        $minimumRules['demolition']=null;
        $minimumRules['greening']=null;
        $minimumRules['maintenance']=null;
        $minimumRules['administration']=null;
        $minimumRules['other']=null;
        $minimumRules['nip']=null;

        foreach ($minimums as $minimumRule) {
            if ($minimumRule->expense_category_id == 2) {
                $minimumRules['acquisition'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 3) {
                $minimumRules['pre_demo'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 4) {
                $minimumRules['demolition'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 5) {
                $minimumRules['greening'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 6) {
                $minimumRules['maintenance'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 7) {
                $minimumRules['administration'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 8) {
                $minimumRules['other'] = $minimumRule;
            }
            if ($minimumRule->expense_category_id == 9) {
                $minimumRules['nip'] = $minimumRule;
            }
        }

        //get min/max units rules
        $reimbursement_rules = ReimbursementRule::where('program_rules_id', $associated_id)->first();
        $within_unit_limits = 1;
        if ($reimbursement_rules) {
            $min_units = $reimbursement_rules->minimum_units;
            $max_units = $reimbursement_rules->maximum_units;
            $max_reimbursement = $reimbursement_rules->maximum_reimbursement;

            if ($parcelData->units < $min_units || $parcelData->units > $max_units) {
                $within_unit_limits = 0;
            }
        }
       
        if ($parcel->lb_validated != 1 && strlen($parcel->sf_parcel_id) < 1) {
            // this parcel is not validated - update its status.
            updateStatus("parcel", $parcel, 'landbank_property_status_id', 43, 0, "Status updated because the parcel wasn't validated.");
            // Parcel::where('id',$parcel->id)->update(['landbank_property_status_id'=>43]);
            $parcel->landbank_property_status_id = 43;
        }
        $hasResolutions = DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->count();

        $guide_steps = GuideStep::where('guide_step_type_id', '=', 2)->get();
        $guide_help = [];
        $guide_name = [];
        foreach ($guide_steps as $guide_step) {
            $guide_help[$guide_step->id] = $guide_step->step_help;
            $guide_name[$guide_step->id]['name'] = $guide_step->name;
            $guide_name[$guide_step->id]['name_completed'] = $guide_step->name_completed;
        }

        $current_user = User::where('id', '=', Auth::user()->id)->first();

        // parcel guide steps checks (independent checks)
        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        $expense_categories = ExpenseCategory::where('parent_id', 1)->get();
        return view('parcels.parcel_detail', compact('hasResolutions', 'rules', 'parcelData', 'parcel', 'program_owner', 'minimumRules', 'expense_categories', 'retainage', 'costs', 'guide_name', 'guide_help', 'current_user', 'within_unit_limits', 'reimbursement_rules'));
    }

    public function quickLookup(Request $request)
    {
        if (Auth::user()->entity_type == 'hfa') {
            $parcels = Parcel::join('states', 'parcels.state_id', 'states.id')
            ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
            ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
            ->leftJoin('import_rows', 'import_rows.row_id', 'parcels.id')
            ->leftJoin('imports', 'imports.id', 'import_rows.import_id')
            ->leftJoin('users', 'users.id', 'imports.user_id')
            ->select('street_address', 'city', 'state_acronym', 'parcels.parcel_id', 'parcels.id', 'lb_status.option_name as lb_status_name', 'import_rows.import_id', 'users.name', 'imports.created_at', 'imports.validated')
            ->where('parcel_id', 'LIKE', '%'.$request->search.'%')
            ->orWhere('street_address', 'like', '%'.$request->search.'%')
            ->orWhere('city', 'like', '%'.$request->search.'%')->take(20)->get()->all();
        } else {
            $parcels = Parcel::join('states', 'parcels.state_id', 'states.id')
                        ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
                        ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
                        ->leftJoin('import_rows', 'import_rows.row_id', 'parcels.id')
                        ->leftJoin('imports', 'imports.id', 'import_rows.import_id')
                        ->leftJoin('users', 'users.id', 'imports.user_id')
                        ->select('street_address', 'city', 'state_acronym', 'parcels.parcel_id', 'parcels.id', 'lb_status.option_name as lb_status_name', 'import_rows.import_id', 'users.name', 'imports.created_at', 'imports.validated')

                        ->where('parcels.entity_id', Auth::user()->entity_id)
                        ->where(function ($q) use ($request) {
                            //$request = Request::input();
                            $q->where('parcel_id', 'LIKE', '%'.$request->search.'%')
                            ->orWhere('street_address', 'like', '%'.$request->search.'%')

                            ->orWhere('city', 'like', '%'.$request->search.'%');
                        })->take(20)->get()->all();
        }
        $i = 0;
        foreach ($parcels as $data) {
            $parcels[$i]->created_at_formatted = date('n/j/y \a\t g:h a', strtotime($data->created_at));
            $i++;
        }
        $results = json_encode($parcels);
        
        return response($results);
    }

    public function autocomplete(Request $request)
    {
        if (Auth::user()->entity_type == 'hfa') {
            $parcels = Parcel::join('states', 'parcels.state_id', 'states.id')
            ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
            ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
            ->leftJoin('import_rows', 'import_rows.row_id', 'parcels.id')
            ->leftJoin('imports', 'imports.id', 'import_rows.import_id')
            ->leftJoin('users', 'users.id', 'imports.user_id')
            ->select('street_address', 'city', 'state_acronym', 'parcels.parcel_id', 'parcels.id', 'lb_status.option_name as lb_status_name', 'hfa_status.option_name as hfa_status_name', 'import_rows.import_id', 'users.name', 'imports.created_at', 'imports.validated')
            ->where('parcel_id', 'LIKE', '%'.$request->search.'%')
            ->orWhere('city', 'like', '%'.$request->search.'%')
            ->orWhere('street_address', 'like', '%'.$request->search.'%')->take(20)->get()->all();
        } else {
            $parcels = Parcel::join('states', 'parcels.state_id', 'states.id')
                        ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
                        ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
                        ->leftJoin('import_rows', 'import_rows.row_id', 'parcels.id')
                        ->leftJoin('imports', 'imports.id', 'import_rows.import_id')
                        ->leftJoin('users', 'users.id', 'imports.user_id')
                        ->select('street_address', 'city', 'state_acronym', 'parcels.parcel_id', 'parcels.id', 'lb_status.option_name as lb_status_name', 'hfa_status.option_name as hfa_status_name', 'import_rows.import_id as import_id', 'users.name as name', 'imports.created_at', 'imports.validated')

                        ->where('parcels.entity_id', Auth::user()->entity_id)
                        ->where(function ($q) use ($request) {
                            //$request = Request::input();
                            $q->where('parcel_id', 'LIKE', '%'.$request->search.'%')
                            ->orWhere('city', 'like', '%'.$request->search.'%')
                            ->orWhere('street_address', 'like', '%'.$request->search.'%');
                        })->take(20)->get()->all();
        }
        $i = 0;
        $results=[];
        foreach ($parcels as $data) {
            $parcels[$i]->created_at_formatted = date('n/j/y \a\t g:h a', strtotime($data->created_at));
            $results[] = [
                        $data->street_address,
                        $data->city,
                        $data->state_acronym,
                        $data->parcel_id,
                        $data->id,
                        $data->lb_status_name,
                        $data->hfa_status_name,
                        $data->import_id,
                        $data->name,
                        $data->created_at,
                        $data->validated,
                        $parcels[$i]->created_at_formatted];
            $i++;
        }
        
        
        $results = json_encode($results);
        return $results;
    }

    public function changeToValidate(Parcel $parcel)
    {
        //this can only be done for parcels that are withdrawn
        if ($parcel->landbank_property_status_id == 48) {
            //check that this person owns it or is hfa
            if ($parcel->entity_id == Auth::user()->entity_id || Auth::user()->entity_id == "hfa") {
                // reset the parcel to Needs Validated and
                $parcel = updateStatus("parcel", $parcel, 'landbank_property_status_id', 43, 0, "I set parcel to this status because it was previously withdrawn and it was requested to revalidate it.");
                $parcel = updateStatus("parcel", $parcel, 'hfa_property_status_id', 39, 0, "I set parcel to this status because it was previously withdawn and it was requested to revalidate it.");
                //$parcel->update(['landbank_property_status_id'=>43,'hfa_property_status_id'=>39]);
                // reset the parcel's import to be unvalidated to re-run
                $import = DB::table('import_rows')->select('import_id')->where('row_id', $parcel->id)->where('table_name', 'parcels')->first();
                DB::table('imports')->where('id', $import->import_id)->update(['validated'=>0]);
                // remove all the resolutions for this parcel
                DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->delete();
                session(['systemMessage'=>'I reset this parcel to be revalidated. To do this I reset its import so you can re-validate it and also removed all the previous validations.']);
                // take them to the validation page
                session(['disablePacer'=>1]);
                /// disable pacer so they can see the page load.
                return redirect('/validate_parcels?import_id='.$importId->id);
            } else {
                return "I am sorry but you don't have permission to do this :(";
            }
        } else {
            return "I am sorry but you can only do this to parcels that are either declined or withdrawn.";
        }
    }
    public function deleteParcel(Parcel $parcel)
    {
        if (Auth::user()->canDeleteParcels()) {
            $deleted = $parcel->deleteParcel();
            if ($deleted == 1) {
                $output['message'] = "I've deleted the parcel ".$parcel->parcel_id;
                return $output;
            } else {
                $output['message'] = "I wasn't able to completely delete the parcel and its associated items. Pleae check your activity log for details.";
                return $output;
            }
        } else {
            $output['message'] = "Sorry, only users with the role of Can Delete Parcels can perform this task.";
            return $output;
        }
    }
    public function reassignParcel(Parcel $parcel, Request $request)
    {
        if (Auth::user()->canReassignParcels()) {
            $assigned = $parcel->reassignParcel($request->requestId);
            if ($assigned == 1) {
                $output['message'] = "I've assigned the parcel ".$parcel->parcel_id." to request ".intval($request->requestId);
                return $output;
            } else {
                $output['message'] = "I wasn't able to assign the parcel. Please make sure it was already a part of a different request.";
                return $output;
            }
        } else {
            $output['message'] = "Sorry, only users with the role of Can Reassign Parcels are allowed to reassing parcel requests.";
            return $output;
        }
    }
    public function forceValidate(Request $request)
    {
        // get the parcel
        $parcel = Parcel::findOrFail(intval($request->query('parcelId')));
        // update its validation
        $parcel->lb_validated = 1;
        $parcel->save();
        // determine if it has costs and request amounts
        $parcelCosts = \App\Models\CostItem::where('parcel_id', $parcel->id)->count();
        $parcelRequests = \App\Models\RequestItem::where('parcel_id', $parcel->id)->count();
        if ($parcelCosts > 0 && ($parcelCosts == $parcelRequests)) {
            // Set status to ready for submission
            updateStatus("parcel", $parcel, 'landbank_property_status_id', 7, 0, "Forced Valid by ".Auth::user()->name.". All costs have requested amounts - so I set this to a status of Internal Ready for Submission.");
            guide_set_progress($parcel->id, 24, $status = 'completed');
            perform_all_parcel_checks($parcel);
            // find out next step and cache it in db
            guide_next_pending_step(2, $parcel->id);
        } else {
            // Either there are no costs, or some costs do not have request amounts
            updateStatus("parcel", $parcel, 'landbank_property_status_id', 46, 0, "Forced Valid by ".Auth::user()->name.". Either there are no costs or there are costs without requested amounts - so I set this to a status of Imported Needs Costs.");
            guide_set_progress($parcel->id, 24, $status = 'completed');
            perform_all_parcel_checks($parcel);
            // find out next step and cache it in db
            guide_next_pending_step(2, $parcel->id);
        }
        $record = $parcel->id;
        $list = null;
        $lat = $parcel->latitude;
        $lon = $parcel->longitude;
        $congressional = $parcel->us_house_district;
        $ohHouse = $parcel->oh_house_district;
        $ohSenate = $parcel->oh_senate_district;
        $googleMapsLink = $parcel->google_map_link;
        $unresolvedLandBankCount = DB::table('validation_resolutions')->where('lb_resolved', '0')->where('parcel_id', $parcel->id)->count();
        $unresolvedHFACount = DB::table('validation_resolutions')->where('hfa_resolved', '0')->where('parcel_id', $parcel->id)->count();
        $resolutionLandBankCount =  DB::table('validation_resolutions')->where('parcel_id', $parcel->id)->count();
        $resolutionHFALandBankCount =  DB::table('validation_resolutions')->where('requires_hfa_resolution', '1')->where('parcel_id', $parcel->id)->count();

        $debugMessage = "";
        $validated = null;
        $useGISAddress = 1; // doing this so it loads the TDs only and not the TR
        $updateTotals = 0;
        $hhf = null;
        $geoDataUpdateCorrection = null;
        $unique = 0;
        $runGeoUpdate = 0;
        $parcelLandBankStatus = $parcel->landbank_property_status_id;
        $insertedValidationResolution = 0;
        $debugMessage = "";
        $waiver = null;
        $updateAddress = null;
        $resolutionId = null;
        $withdraw = null;

        ///////////////////////////////////////
        return view('pages.import.validate_parcel_row', compact('validated', 'useGISAddress', 'unresolvedLandBankCount', 'unresolvedHFACount', 'resolutionLandBankCount', 'resolutionHFALandBankCount', 'record', 'updateTotals', 'hhf', 'geoDataUpdateCorrection', 'parcel', 'lat', 'lon', 'congressional', 'ohHouse', 'ohSenate', 'unique', 'list', 'runGeoUpdate', 'parcelLandBankStatus', 'insertedValidationResolution', 'debugMessage', 'waiver', 'updateAddress', 'resolutionId', 'withdraw'));
    }

    public function guide_validate_parcel_info_hfa(Parcel $parcel, Request $request)
    {
        if (Auth::user()->entity_type != 'hfa') {
            return null;
        }

        if (guide_check_step(33, $parcel->id)) {
            guide_set_progress($parcel->id, 33, $status = 'started');
            $data['validated'] = 0;
        } else {
            guide_set_progress($parcel->id, 33, $status = 'completed');
            $data['validated'] = 1;
        }
        
        return $data;
    }

    public function guide_mark_advance_paid_hfa(Parcel $parcel, Request $request)
    {
        if (Auth::user()->entity_type != 'hfa') {
            return null;
        }

        // find the advance cost_item from cost_item_id
        $advance_id = intval($request->get('advance_id'));
        $advance_item = CostItem::where('id', '=', $advance_id)->where('parcel_id', '=', $parcel->id)->first();

        if (count($advance_item) == 1) {
            if ($advance_item->advance_paid == 1) {
                $advance_item->update([
                    "advance_paid" => 0,
                ]);
                $data['paid'] = 0;
            } else {
                $advance_item->update([
                    "advance_paid" => 1,
                ]);
                $data['paid'] = 1;
                //$data['log'] = "test ".$advance_item->advance_paid;
            }
        } else {
            return null;
        }
        
        return $data;
    }

    public function guide_mark_retainage_paid_hfa(Parcel $parcel, Request $request)
    {
        if (Auth::user()->entity_type != 'hfa') {
            return null;
        }

        // find the retainage
        $retainage_id = intval($request->get('retainage_id'));
        $retainage_item = Retainage::where('id', '=', $retainage_id)->where('parcel_id', '=', $parcel->id)->first();

        if (count($retainage_item) == 1) {
            if ($retainage_item->paid == 1) {
                $retainage_item->update([
                    "paid" => 0,
                ]);
                $data['paid'] = 0;
            } else {
                $retainage_item->update([
                    "paid" => 1,
                ]);
                $data['paid'] = 1;
            }
        } else {
            return null;
        }
        
        return $data;
    }
}

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Parcel;
use App\DispositionType;
use App\ReimbursementInvoice;
use App\ParcelsToReimbursementInvoice;
use App\Disposition;
use App\VisitLists;
use App\Document;
use App\DocumentCategory;
use App\Helpers\GeoData;
use App\Http\Controllers;
//use App\Photo;
use App\Correction;
use App\State;
use App\County;
use App\TargetArea;
use App\HowAcquired;
use App\RecaptureItem;
use App\BreakOutType;
use App\ExpenseCategory;
use App\Device;
use Carbon\Carbon;
use App\Models\Audit;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Building;
use App\Models\Amenity;
use App\Models\ProjectAmenity;
use App\Models\BuildingAmenity;
use App\Models\UnitAmenity;
use App\Models\AuditAuditor;
use App\Models\User;
use App\Models\OrderingAmenity;
use App\Models\OrderingBuilding;
use App\Models\OrderingUnit;
use App\Models\CachedAmenity;
use App\Models\People;
use App\Models\AmenityInspection;
use App\Models\Finding;
use App\Models\Comment;
use App\Models\FindingType;
use App\Models\AmenityHud;
use App\Models\HudFindingType;
use App\Models\Followup;
use App\Models\SiteVisits;
use App\Models\UnitInspection;
use App\Models\BuildingInspection;
use App\Models\UnitProgram;
use App\Models\Program;
use App\Models\Unit;
use App\Models\Photo;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/postReturnTest', function (Request $request) {
    return $request->all();
});

Route::get('/users/verify_user', function (Request $request) {

    try {
        $user_verified = false;

        $device = Device::where('device_id', $request->query('device_id'))->first();
        if (is_null($device)) {
            $device = new Device();
            $device->device_id = $request->query('device_id');
            if (!is_null($request->query('device_name'))) {
                $device->device_name = $request->query('device_name');
            }
            $device->save();
        } else if (is_null($device->device_name) && !is_null($request->query('device_name'))) {
            $device->update(['device_name' => $request->query('device_name')]);
        }

        $email = $request->query("email");
        $password = $request->query("password");
        $key = $request->query("api_key");

        if (Auth::attempt(['email'=> $email, 'password' => $password])) {
            $user_verified = $key == Auth::user()->api_token;
        }

        if ($user_verified) {
            return response(Auth::user()->id, 200);
        } else {
            return response("0", 200);
        }
    } catch (Exception $e) {
        throw $e;
    }
});


    Route::get('/device/wiped', function (Request $request) {
        try {
            //wipe check
            if (!is_null($request->query('device_id')) && $request->query('wiped') == 1) {
                //check to see if device should be wiped
                $device = Device::where('device_id', $request->query('device_id'))->first();
                if (count($device) > 0) {
                    $wipedTime = Carbon::now();
                    $device->last_wiped = $wipedTime;
                    Auth::logout();
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    });

    Route::get('/device/connection_check', function (Request $request) {
        try {
            //wipe check
            if (!is_null($request->query('device_id'))) {
                //check to see if device should be wiped
                $device = Device::where('device_id', $request->query('device_id'))->first();
                if (is_null($device)) {
                    $device = new Device();
                    $device->device_id = $request->query('device_id');
                    if (!is_null($request->query('device_name'))) {
                        $device->device_name = $request->query('device_name');
                    }
                    $device->save();
                    // a return of 2 will signal the device to wipe itself
                    return response("1", 200);
                } else if (is_null($device->device_name) && !is_null($request->query('device_name'))) {
                    $device->update(['device_name' => $request->query('device_name')]);
                }
                if (!is_null($device)) {
                    if ($device->remote_wipe == 1) {
                        // a return of 2 will signal the device to wipe itself
                        return response("2", 200);
                    } else {
                        // a return of 1 will simply state the connection is live
                        return response("1", 200);
                    }
                }
            } else {
                return response("1", 200);
            }
        } catch (Exception $e) {
            throw $e;
        }
    });




    Route::get('/test/url', function (Request $request) {

        try {
            return response($request->getPathInfo(), 200);
        } catch (Exception $e) {
            throw $e;
        }
    });

    Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {

        Route::get('/breakouts/parcel/{parcel}/{format?}', 'ParcelsPTController@breakouts');

        Route::get('/parcels', function (Request $request) {

            try {
                $user_entity = Auth::user()->entity_id;
                $id = $request->query("id");
                if ($id != "") {
                    $parcels = Parcel::where('id', $id)->get();
                    foreach ($parcels as $parcel) {
                        $parcelLat = $parcel->latitude;
                        $parcelLon = $parcel->longitude;




                        $parcel->distance = 0;

                        $parcel->locked = VisitLists::select('id')->where('parcel_id', $parcel->id)->where('status', '1')->count() != 0;

                        $parcel->acquired_by = HowAcquired::select('how_acquired_option_name')->where('id', $parcel->how_acquired_id)->first()->how_acquired_option_name;

                        $parcel->state_acronym = State::select('state_acronym')->where('id', $parcel->state_id)->first()->state_acronym;
                        $parcel->state_name = State::select('state_name')->where('id', $parcel->state_id)->first()->state_name;
                        $parcel->county = County::select('county_name')->where('id', $parcel->county_id)->first()->county_name;
                        $parcel->targetArea = TargetArea::select('target_area_name')->where('id', $parcel->target_area_id)->first()->target_area_name;


                        $invoice_id = ParcelsToReimbursementInvoice::select("reimbursement_invoice_id")->where("parcel_id", $parcel->id)->first()->reimbursement_invoice_id;
                        $invoice = ReimbursementInvoice::where("id", $invoice_id)->first();

                        if ($invoice == null) {
                            $parcel->paid = null;
                        } else {
                            $transactions = $invoice->transactions()->get();
                            $transactions = $transactions->sortByDesc('date_cleared');
                            $parcel->paid = $transactions->first()->date_cleared;
                        }

                        $site_visits = SiteVisits::where("parcel_id", $parcel->id)->where("status", "<", "3")->orderBy("visit_date", "DESC")->get();
                        $visit_count = $site_visits->count();
                        if ($visit_count > 0) {
                            $visit_date = $site_visits->first()->visit_date;
                        } else {
                            $visit_date = null;
                        }

                        $parcel->visit_count = $visit_count;
                        $parcel->visit_date = $visit_date;

                        $disposition =  Disposition::where("parcel_id", $parcel->id)->first();
                        if ($disposition != null) {
                            $parcel->disposition_date = $disposition->date_approved;
                            $parcel->disposition_type_id = $disposition->disposition_type_id;
                        } else {
                            $parcel->disposition_date = null;
                            $parcel->disposition_type_id = null;
                        }

                        $parcel->incomplete_site_visits = SiteVisits::where('parcel_id', $parcel->id)->where('status', '<', '2')->count();
                        $parcel->uncorrected_corrections = Correction::where('parcel_id', $parcel->id)->where('corrected', '0')->count();
                    }
                    return response()->json($parcels);
                }

                $max = $request->query("max");
                $program_id = $request->query("program_id");
                //$lat = $request->query("lat");
                //$lon = $request->query("lon");
                $distance = $request->query("distance");
                $disposition = $request->query("disposition");
                $last_visited = $request->query("last_visited");

                //With GPS Distance
                if ($program_id != "") {
                //&& $lat != ""
                //&& $lon != ""
                //&& $distance != "")
                    $paid_invoices = ReimbursementInvoice::select('id')->where('program_id', $program_id)->where('status_id', '6')->where('active', '1')->get();
                    $parcels_in_invoices = ParcelsToReimbursementInvoice::select('parcel_id')->whereIn('reimbursement_invoice_id', $paid_invoices)->get();

                    $randomParcel = Parcel::
                    where('program_id', $program_id)
                    ->whereIn('id', $parcels_in_invoices)
                    ->inRandomOrder()
                    ->first();

                    $parcels = Parcel::
                    where('program_id', $program_id)
                    ->whereIn('id', $parcels_in_invoices)
                    ->get(["id", "parcel_id","program_id","street_address","latitude","longitude","city","state_id","county_id","target_area_id","zip","sale_price","how_acquired_id","how_acquired_explanation","units","oh_house_district","oh_senate_district","us_house_district","historic_significance_or_district"]);


                    $lat = $randomParcel["latitude"];
                    $lon = $randomParcel["longitude"];

                    foreach ($parcels as $parcel) {
                        $parcelLat = $parcel->latitude;
                        $parcelLon = $parcel->longitude;


                        $earthRadius = 3959;
                        $latFrom = deg2rad($lat);
                        $lonFrom = deg2rad($lon);
                        $latTo = deg2rad($parcelLat);
                        $lonTo = deg2rad($parcelLon);

                        $latDelta = $latTo - $latFrom;
                        $lonDelta = $lonTo - $lonFrom;

                        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

                        $parcelDistance = $angle * $earthRadius;

                        $parcel->distance = $parcelDistance;

                        $parcel->locked = VisitLists::select('id')->where('parcel_id', $parcel->id)->where('status', '1')->count() != 0;

                        $parcel->acquired_by = HowAcquired::select('how_acquired_option_name')->where('id', $parcel->how_acquired_id)->first()->how_acquired_option_name;

                        $parcel->state_acronym = State::select('state_acronym')->where('id', $parcel->state_id)->first()->state_acronym;
                        $parcel->state_name = State::select('state_name')->where('id', $parcel->state_id)->first()->state_name;
                        $parcel->county = County::select('county_name')->where('id', $parcel->county_id)->first()->county_name;
                        //$parcel->status = $parcel->status();
                        //$parcel->acquisitionTotal = $parcel->acquisitionTotal();
                        $parcel->targetArea = TargetArea::select('target_area_name')->where('id', $parcel->target_area_id)->first()->target_area_name;


                        $invoice_id = ParcelsToReimbursementInvoice::select("reimbursement_invoice_id")->where("parcel_id", $parcel->id)->first()->reimbursement_invoice_id;
                        $invoice = ReimbursementInvoice::where("id", $invoice_id)->first();

                        if ($invoice == null) {
                                $parcel->paid = null;
                        } else {
                                $transactions = $invoice->transactions()->get();
                                $transactions = $transactions->sortByDesc('date_cleared');
                                $parcel->paid = $transactions->first()->date_cleared;
                        }



                        $site_visits = SiteVisits::where("parcel_id", $parcel->id)->where("status", "<", "3")->orderBy("visit_date", "DESC")->get();
                        $visit_count = $site_visits->count();
                        if ($visit_count > 0) {
                                $visit_date = $site_visits->first()->visit_date;
                        } else {
                                $visit_date = null;
                        }

                        $parcel->visit_count = $visit_count;
                        $parcel->visit_date = $visit_date;

                        $disposition =  Disposition::where("parcel_id", $parcel->id)->first();
                        if ($disposition != null) {
                                $parcel->disposition_date = $disposition->date_approved;
                                $parcel->disposition_type_id = $disposition->disposition_type_id;
                        } else {
                                $parcel->disposition_date = null;
                                $parcel->disposition_type_id = null;
                        }

                        $parcel->incomplete_site_visits = SiteVisits::where('parcel_id', $parcel->id)->where('status', '<', '2')->count();
                        $parcel->uncorrected_corrections = Correction::where('parcel_id', $parcel->id)->where('corrected', '0')->count();
                    }

                    //$parcels = $parcels->sortBy('distance');


                    if ($max == "") {
                        return response()->json($parcels);
                    } else {
                        return response()->json(compact($parcels, $max));
                    }
                }

                return "Please use the query string id or program_id to return filtered Parcel results";
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/programs', function (Request $request) {
            try {
                $programs = Program::get(["id","program_name"]);
                return response()->json($programs);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/disposition_types', function (Request $request) {
            try {
                $dispositions = DispositionType::where("active", "1")->get(["id","disposition_type_name"]);
                return response()->json($dispositions);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/site_visits', function (Request $request) {
            try {
                $parcel_id = $request->query("parcel_id");
                $rows = SiteVisits::where("parcel_id", $parcel_id)->get();
                return response()->json($rows);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/test', function (Request $request) {
            try {
                $invoice_id = ParcelsToReimbursementInvoice::select("id")->where("parcel_id", "1065")->first()->id;
                $invoice = ReimbursementInvoice::where("id", $invoice_id)->first()->transactions()->select("date_cleared")->orderBy("date_cleared", "DESC")->get();
                return response()->json($invoice);
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/add_visit_list', function (Request $request) {
            try {
                $device_id = $request->query("device_id");
                $added_date = $request->query("added_date");
                $parcel_id = $request->query("parcel_id");
                //$device_id = $request->input('device_id');
                //$added_date = $request->input('added_date');
                //$parcel_id = $request->input('parcel_id');
                // check that device is on registered list
                $device = Device::where('device_id', $device_id)->first();
                if (is_null($device)) {
                    // device needs to be added to the registry
                    $device = new Device;
                    $device->device_id = $device_id;
                    $device->save();
                }


                $exists = VisitLists::where('parcel_id', $parcel_id)->where('status', 1)->where('device_id', $device_id)->where('user_id', $request->user()->id)->first();

                if ($exists) {
                    $parcel = Parcel::select('parcel_id')->where('id', $parcel_id)->first();
                    $exists->parcel = $parcel->parcel_id;
                    $reply = $exists;
                } else {
                    $visit = new VisitLists();
                    $visit->user_id = $request->user()->id;
                    $visit->device_id = $device_id;
                    $visit->parcel_id = $parcel_id;
                    $visit->added_date = $added_date;
                    $visit->updated_date = $added_date;
                    $visit->status = 1;
                    $visit->save();

                    $parcel = Parcel::select('parcel_id')->where('id', $parcel_id)->first();
                    $visit->parcel = $parcel->parcel_id;

                    $reply = $visit;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/update_visit_list', function (Request $request) {
            try {
                $visit_id = $request->query("visit_id");
                $device_id = $request->query("device_id");
                $status = $request->query("status");
                $updated_date = $request->query("updated_date");
                // check that device is on registered list
                if (count(Device::where('device_id', $device_id)->count()) < 1) {
                    // device needs to be added to the registry
                    $device = new Device;
                    $device->device_id = $device_id;
                    $device->save();
                }

                $visit = VisitLists::where('id', $visit_id)->first();

                if ($visit) {
                    if ($visit->updated_date < $updated_date) {
                        $visit->updated_date = $updated_date;
                        $visit->user_id = $request->user()->id;
                        $visit->device_id = $device_id;
                        $visit->status = $status;
                        $visit->save();
                        $reply = $visit;
                    } else {
                        $reply = null;
                    }
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_visit_list', function (Request $request) {
            try {
                $user_id = $request->query("user_id");

                $visits = VisitLists::where('user_id', $user_id)->where('status', '1')->get();

                if ($visits) {
                    $reply = $visits;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_visit', function (Request $request) {
            try {
                $visit_id = $request->query("visit_id");

                $visit = VisitLists::where('id', $visit_id)->first();

                if ($visit) {
                    $reply = $visit;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/add_site_visit', function (Request $request) {
            try {
                $device_id =  $request->query('device_id');
                $visit_date = $request->query('visit_date');
                $parcel_id = $request->query('parcel_id');


                $exists = SiteVisits::where('parcel_id', $parcel_id)->where('status', 1)->first();

                if ($exists) {
                    $reply = $exists;
                } else {
                    $parcel = Parcel::where('id', $parcel_id)->first();

                    $visit = new SiteVisits();
                    $visit->visit_date = $visit_date;
                    $visit->inspector_id = $request->user()->id;
                    $visit->parcel_id = $parcel_id;
                    $visit->sf_parcel_id = $parcel->sf_parcel_id;
                    $visit->program_id = $parcel->program_id;
                    $visit->status = 1;
                    $visit->save();

                    $reply = $visit;
                }
                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });



        Route::post('/update_site_visit', function (Request $request) {
            try {
                $site_visit_id =  $request->input('site_visit_id');
                $visit_date = $request->input('visit_date');
                $parcel_id = $request->input('parcel_id');
                $inspector_id = $request->input('inspector_id');
                $status = $request->input('status');

                $all_structures_removed = $request->input('all_structures_removed');
                $construction_debris_removed = $request->input('construction_debris_removed');
                $other_notes = $request->input('other_notes');
                $corrective_action_required = $request->input('corrective_action_required');
                $retainage_released_to_contractor = $request->input('retainage_released_to_contractor');
                $is_a_recap_of_maint_funds_required = $request->input('is_a_recap_of_maint_funds_required');
                $amount_of_maint_recapture_due = $request->input('amount_of_maint_recapture_due');
                $was_the_property_graded_and_seeded = $request->input('was_the_property_graded_and_seeded');
                $is_there_any_signage = $request->input('is_there_any_signage');
                $is_grass_growing_consistently_across = $request->input('is_grass_growing_consistently_across');
                $is_grass_mowed_weeded = $request->input('is_grass_mowed_weeded');
                $was_the_property_landscaped = $request->input('was_the_property_landscaped');
                $nuisance_elements_or_code_violations = $request->input('nuisance_elements_or_code_violations');
                $are_there_environmental_conditions = $request->input('are_there_environmental_conditions');


                $visit = SiteVisits::where('id', $site_visit_id)->first();

                if ($visit) {
                    $parcel = Parcel::where('id', $parcel_id)->first();

                    $visit->visit_date = $visit_date;
                    $visit->inspector_id = $inspector_id;
                    $visit->parcel_id = $parcel_id;
                    $visit->sf_parcel_id = $parcel->sf_parcel_id;
                    $visit->program_id = $parcel->program_id;
                    $visit->status = $status;

                    $visit->all_structures_removed = $all_structures_removed == "null" ? null : $all_structures_removed;
                    $visit->construction_debris_removed = $construction_debris_removed == "null" ? null : $construction_debris_removed;
                    $visit->other_notes = $other_notes == "null" ? null : $other_notes;
                    $visit->corrective_action_required = $corrective_action_required == "null" ? null : $corrective_action_required;
                    $visit->retainage_released_to_contractor = $retainage_released_to_contractor == "null" ? null : $retainage_released_to_contractor;
                    $visit->is_a_recap_of_maint_funds_required = $is_a_recap_of_maint_funds_required == "null" ? null : $is_a_recap_of_maint_funds_required;
                    $visit->amount_of_maint_recapture_due = $amount_of_maint_recapture_due == "null" ? null : $amount_of_maint_recapture_due;
                    $visit->was_the_property_graded_and_seeded = $was_the_property_graded_and_seeded == "null" ? null : $was_the_property_graded_and_seeded;
                    $visit->is_there_any_signage = $is_there_any_signage == "null" ? null : $is_there_any_signage;
                    $visit->is_grass_growing_consistently_across = $is_grass_growing_consistently_across == "null" ? null : $is_grass_growing_consistently_across;
                    $visit->is_grass_mowed_weeded = $is_grass_mowed_weeded == "null" ? null : $is_grass_mowed_weeded;
                    $visit->was_the_property_landscaped = $was_the_property_landscaped == "null" ? null : $was_the_property_landscaped;
                    $visit->nuisance_elements_or_code_violations = $nuisance_elements_or_code_violations == "null" ? null : $nuisance_elements_or_code_violations;
                    $visit->are_there_environmental_conditions = $are_there_environmental_conditions == "null" ? null : $are_there_environmental_conditions;


                    $visit->save();

                    $reply = $visit;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/user', function (Request $request) {

            try {
                $id = $request->query("id");

                $user = DB::table('users')->where('id', $id)->first(['id','name']);

                if ($user) {
                    $reply = $user;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/program_stats', function (Request $request) {

            try {
                $programs = Program::get(["id","program_name"]);

                $results = new Collection();



                foreach ($programs as $program) {
                    $program_info = new StdClass;

                    $parcels_visited_count = DB::table('site_visits')
                     ->select('id')
                     ->where('program_id', $program->id)
                     ->where('status', '2')
                     ->count();

                    $parcels_in_program_count = Parcel::select('id')->where('program_id', $program->id)->count();


                    //$parcels_in_program = Parcel::select('id')->where('program_id',$program->id)->get();

                    //$parcels_in_program_count = $parcels_in_program->count();

                    //$parcels_visited_count = DB::table('site_visits')
                    // ->select('id')
                    // ->whereIn('parcel_id', $parcels_in_program)
                    // ->where('status','2')
                    // ->groupBy('parcel_id')
                    // ->count();

                    $program_info->id = $program->id;
                    $program_info->program_name = $program->program_name;
                    $program_info->parcels_in_program = $parcels_in_program_count;
                    $program_info->parcels_visited = $parcels_visited_count;

                    $results->push($program_info);
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });



        Route::get('/documents', function (Request $request) {

            try {
                $parcel_id = $request->query("parcel_id");

                $documents = Document::where('parcel_id', $parcel_id)->get();

                if ($documents) {
                    $reply = $documents;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_document', function (Request $request) {

            try {
                $parcel_id = $request->query("parcel_system_id");
                $document_id = $request->query("document_id");

                $parcel = Parcel::where('id', $parcel_id)->first();
                $document = Document::where('id', $document_id)->first();


                $filepath =  $document->file_path;
                $storage_path = storage_path();

                $fullpath = $filepath;

                if (Storage::exists($fullpath)) {
                    $file = Storage::get($fullpath);
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.$document->filename);
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: '. Storage::size($fullpath));

                    return $file;
                    //return Response::download($file, "123.PDF");
                } else {
                    // Error
                    exit('Requested file does not exist on our server! '.$fullpath);
                }
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/document_categories', function (Request $request) {
            try {
                $categories = DocumentCategory::where("active", "1")->get(["id","document_category_name"]);
                return response()->json($categories);
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::post('/upload_document', function (Request $request) {
            try {
                $parcel_id =  $request->get('parcel_id');
                $user_id =  $request->get('user_id');
                //$api_token =  $request->input('api_token');

                //$user = User::where('api_token',$api_token)->first();

                //if($user)
                //{
                $parcel = Parcel::where("id", $parcel_id)->first();

                if ($parcel) {
                    if ($request->hasFile('files')) {
                        $files = $request->file('files');
                        $file_count = count($files);
                        $uploadcount = 0; // counter to keep track of uploaded files
                        $document_ids = '';

                        $categories = explode(",", $request->get('categories'));
                        $categories_json = json_encode($categories, true);

                        //$user = Auth::user();

                        if (in_array(47, $categories)) {
                            $is_advance = 1;
                        } else {
                            $is_advance = 0;
                        }
                        if (in_array(9, $categories)) {
                            $is_retainage = 1;
                        } else {
                            $is_retainage = 0;
                        }
                        $file = $files;
                        //foreach($files as $file){
                        // Create filepath
                        $folderpath = 'documents/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';

                        // sanitize filename
                        $characters = [' ','�','`',"'",'~','"','\'','\\','/'];
                        $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                        // Create a record in documents table
                        $document = new Document([
                        'user_id' => $user_id,
                        'parcel_id' => $parcel->id,
                        'categories' => $categories_json,
                        'filename' => $original_filename
                        ]);

                        $document->save();

                        // Save document ids in an array to return
                        if ($document_ids!='') {
                                $document_ids = $document_ids.','.$document->id;
                        } else {
                                $document_ids = $document->id;
                        }

                        // Sanitize filename and append document id to make it unique
                        // documents/entity_0/program_0/parcel_0/0_filename.ext
                        $filename = $document->id . '_' . $original_filename;
                        $filepath = $folderpath . $filename;

                        $document->update([
                            'file_path' => $filepath,
                        ]);
                        //$lc=new LogConverter('document','create');
                        //$lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                        // store original file
                        Storage::put($filepath, File::get($file));

                        if ($is_retainage) {
                                // if only one retainage in database, then no need to display the modal with the select form
                            if ($parcel->retainages) {
                                if (count($parcel->retainages) == 1) {
                                    // assign to retainage
                                    $retainage = $parcel->retainages->first();
                                    $retainage->documents()->attach($document->id);
                                } elseif (count($parcel->retainages) == 0) {
                                    $is_retainage = 0;
                                }
                            }
                        }
                        if ($is_advance) {
                                // if only one advance in database, then no need to display the modal with the select form
                            if ($parcel->costItemsWithAdvance) {
                                if (count($parcel->costItemsWithAdvance) == 1) {
                                    // assign to cost item
                                    $advance = $parcel->costItemsWithAdvance->first();
                                    $advance->documents()->attach($document->id);
                                } elseif (count($parcel->costItemsWithAdvance) == 0) {
                                    $is_advance = 0;
                                }
                            }
                        }


                        $uploadcount++;
                        //}
                        if ($is_retainage) {
                            if ($parcel->retainages) {
                                if (count($parcel->retainages) == 1) {
                                    $is_retainage = 0;
                                }
                            }
                        }
                        if ($is_advance) {
                            if ($parcel->costItemsWithAdvance) {
                                if (count($parcel->costItemsWithAdvance) == 1) {
                                    $is_advance = 0;
                                }
                            }
                        }

                        if ($uploadcount != $file_count) {
                                // something went wrong
                        }


                        $data = [];
                        $data['document_ids'] = $document_ids;
                        $data['is_retainage'] = $is_retainage;
                        $data['is_advance'] = $is_advance;

                        return response()->json($data);
                    } else {
                        // shouldn't happen - UIKIT shouldn't send empty files
                        // nothing to do here

                        return response($request->all());
                    }
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/photos', function (Request $request) {

            try {
                $parcel_id = $request->query("parcel_id");

                $photos = Photo::where('parcel_id', $parcel_id)->where('deleted', '0')->get();

                if ($photos) {
                    $reply = $photos;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_photo', function (Request $request) {

            try {
                $document_id = $request->query("photo_id");
                $photo = Photo::where('id', $document_id)->first();


                $filepath =  $photo->file_path;
                $storage_path = storage_path();

                $fullpath = $filepath;

                if (Storage::exists($fullpath)) {
                    $file = Storage::get($fullpath);
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.$photo->filename);
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Pragma: public');
                    header('Content-Length: '. Storage::size($fullpath));

                    return $file;
                    //return Response::download($file, "123.PDF");
                } else {
                    // Error
                    exit('Requested file does not exist on our server! '.$fullpath);
                }
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::post('/upload_photo', function (Request $request) {
            try {
                $uid =  $request->get('uid');
                $audit_id =  $request->get('audit_id');
                $recorded_date =  $request->get('recorded_date');
                $finding_id =  $request->get('finding_id');
                $notes =  $request->get('notes');
                $latitude =  $request->get('latitude');
                $longitude =  $request->get('longitude');
                //$correction_id =  $request->get('correction_id');
                $comment_id =  $request->get('comment_id');
                $deleted =  $request->get('deleted');
                $user_id =  $request->get('user_id');

                //$user = User::where('api_token',$api_token)->first();

                //if($user)
                //{
                $audit = CachedAudit::where("audit_id", $audit_id)->first();

                if ($audit) {
                    if ($request->hasFile('photo')) {
                        //$file = $request->file('photo');

                        //$photo_id = '';


                        //$user = Auth::user();

                        // Create filepath
                        // $folderpath = 'photos/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';

                        // // sanitize filename
                        // $characters = [' ','�','`',"'",'~','"','\'','\\','/'];
                        // $original_filename = str_replace($characters, '_', $file->getClientOriginalName());
                        // $original_ext =  $file->getClientOriginalExtension();

                        // Create a record in photos table
                        // $photo = new Photo([
                        // 'uid' => $uid,
                        // 'user_id' => $user_id,
                        // 'parcel_id' => $parcel->id,
                        // 'recorded_date' =>  $recorded_date,
                        // 'site_visit_id' =>  $site_visit_id,
                        // 'notes' =>  $notes,
                        // 'latitude' =>  $latitude ,
                        // 'longitude' =>  $longitude,
                        // 'deleted' =>  false
                        // ]);

                        
                            $data = [];
                            $user = User::find($user_id);
                            $files = $request->file('photo');

                            foreach ($files as $file) {
                                $selected_audit = $audit;

                                $folderpath = 'photos/project_' . $audit->project->project_number . '/audit_' . $selected_audit->audit_id . '/';
                                $characters = [' ', '´', '`', "'", '~', '"', '\'', '\\', '/'];
                                $original_filename = str_replace($characters, '_', $file->getClientOriginalName());
                                $file_extension = $file->getClientOriginalExtension();
                                $filename = pathinfo($original_filename, PATHINFO_FILENAME);
                                $photo = new Photo([
                                    'user_id' => $user->id,
                                    'project_id' => $project->id,
                                    'audit_id' => $selected_audit->id,
                                    'notes' => $request->comment,
                                    'finding_id' => $request->finding_id,
                                ]);
                                $photo->save();

                                // Sanitize filename and append document id to make it unique
                                $filename = snake_case(strtolower($filename)) . '_' . $photo->id . '.' . $file_extension;
                                $filepath = $folderpath . $filename;
                                $photo->update([
                                    'file_path' => $filepath,
                                    'filename' => $filename,
                                ]);

                                // store original file
                                Storage::put($filepath, File::get($file));
                                $data[] = [
                                    'id' => $photo->id,
                                    'filename' => $filename,
                                ];
                            }
                            return json_encode($data);
                        

                        
                    } else {
                        return response($request->all());
                    }
                } else {
                    return response('No Audit');
                }
                
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::post('/add_comment', function (Request $request) {
            try {
                $uid =  $request->input('uid');
                $parcel_id =  $request->input('parcel_id');
                $recorded_date =  $request->input('recorded_date');
                $site_visit_id =  $request->input('site_visit_id');
                $notes =  $request->input('comment');
                $deleted =  $request->input('deleted');
                $user_id =  $request->input('user_id');

                $parcel = Parcel::where("id", $parcel_id)->first();

                if ($parcel) {
                    // Create a record in documents table
                    $comment = new Comment([
                    'uid' => $uid,
                    'user_id' => $user_id,
                    'parcel_id' => $parcel->id,
                    'recorded_date' =>  $recorded_date,
                    'site_visit_id' =>  $site_visit_id,
                    'comment' =>  $notes,
                    'deleted' =>  false
                    ]);

                    $comment->save();


                    return response()->json($comment);
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::post('/add_correction', function (Request $request) {
            try {
                $uid =  $request->input('uid');
                $parcel_id =  $request->input('parcel_id');
                $recorded_date =  $request->input('recorded_date');
                $site_visit_id =  $request->input('site_visit_id');
                $notes =  $request->input('notes');
                $corrected =  $request->input('corrected');
                $corrected_site_visit_id =  $request->input('corrected_site_visit_id');
                $corrected_user_id =  $request->input('corrected_user_id');
                $corrected_date =  $request->input('corrected_date');
                $deleted =  $request->input('deleted');
                $user_id =  $request->input('user_id');

                $parcel = Parcel::where("id", $parcel_id)->first();

                if ($parcel) {
                    // Create a record in documents table
                    $correction = new Correction([
                    'uid' => $uid,
                    'user_id' => $user_id,
                    'parcel_id' => $parcel->id,
                    'recorded_date' =>  $recorded_date,
                    'site_visit_id' =>  $site_visit_id,
                    'notes' =>  $notes,
                    'corrected' =>  $corrected ,
                    'corrected_site_visit_id' =>  $corrected_site_visit_id,
                    'corrected_user_id' =>  $corrected_user_id,
                    'corrected_date' =>  $corrected_date,
                    'deleted' =>  false
                    ]);

                    $correction->save();


                    return response()->json($correction);
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::post('/add_recapture', function (Request $request) {
            try {
                $parcel_id =  $request->input('parcel_id');
                $breakout_type =  $request->input('breakout_type');
                $recapture_invoice_id =  $request->input('recapture_invoice_id');
                $expense_category_id =  $request->input('expense_category_id');
                $amount =  $request->input('amount');
                $description =  $request->input('description');
                $notes =  $request->input('notes');

                $parcel = Parcel::where("id", $parcel_id)->first();

                if ($parcel) {
                    // Create a record in documents table
                    $recapture = new RecaptureItem([
                    'breakout_type' => $breakout_type,
                    'recapture_invoice_id' => $recapture_invoice_id,
                    'parcel_id' => $parcel->id,
                    'program_id' =>  $parcel->program_id,
                    'entity_id' =>  $parcel->entity_id,
                    'account_id' =>  $parcel->account_id,
                    'expense_category_id' =>  $expense_category_id ,
                    'amount' =>  $amount,
                    'description' =>  $description,
                    'notes' =>  $notes
                    ]);

                    $recapture->save();


                    return response()->json($recapture);
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/recaptures', function (Request $request) {

            try {
                $parcel_id = $request->query("parcel_id");

                $recaptures = RecaptureItem::where('parcel_id', $parcel_id)->get();

                if ($recaptures) {
                    foreach ($recaptures as $recapture) {
                        $recapture->breakout_type_name = BreakOutType::where('id', $recapture->breakout_type)->first()->breakout_type_name;
                        $recapture->expense_category_name = ExpenseCategory::where('id', $recapture->expense_category_id)->first()->expense_category_name;
                    }

                    $reply = $recaptures;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/comments', function (Request $request) {

            try {
                $parcel_id = $request->query("parcel_id");

                $comments = Comment::where('parcel_id', $parcel_id)->where('deleted', '0')->get();

                if ($comments) {
                    $reply = $comments;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/corrections', function (Request $request) {

            try {
                $parcel_id = $request->query("parcel_id");

                $corrections = Correction::where('parcel_id', $parcel_id)->where('deleted', '0')->get();

                if ($corrections) {
                    $reply = $corrections;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });




        Route::get('/update_parcel_location', function (Request $request) {
            try {
                $parcel_id =  $request->query('parcel_id');
                $latitude = $request->query('latitude');
                $longitude = $request->query('longitude');


                $parcel = Parcel::where('id', $parcel_id)->first();

                if ($parcel) {
                    $parcel->latitude = $latitude;
                    $parcel->longitude = $longitude;

                    $parcel->save();

                    $reply = $parcel;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });

        Route::post('/update_correction', function (Request $request) {
            try {
                $correction_id =  $request->input('correction_id');
                $notes = $request->input('notes');
                $corrected = $request->input('corrected');
                $corrected_site_visit_id = $request->input('corrected_site_visit_id');
                $corrected_user_id = $request->input('corrected_user_id');
                $corrected_date = $request->input('corrected_date');


                $correction = Correction::where('id', $correction_id)->first();

                if ($correction) {
                    $correction->notes = $notes;
                    $correction->corrected = $corrected;
                    $correction->corrected_site_visit_id = $corrected_site_visit_id;
                    $correction->corrected_user_id = $corrected_user_id;
                    $correction->corrected_date = $corrected_date;

                    $correction->save();

                    $reply = $correction;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });



        Route::get('/parcel_lookup', 'ParcelsController@quickLookup');


        Route::get('/get_cached_amenities', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = CachedAmenity::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = CachedAmenity::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_cached_audits', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = CachedAudit::where('step_id','>=','55')->where('step_id','<=','65')->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = CachedAudit::where('step_id','>=','55')->where('step_id','<=','65')->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_cached_buildings', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                $auditsIdForApp = CachedAudit::where('step_id','>=','55')->where('step_id','<=','65')->select('audit_id')->get();
                if($lastEdited != null)
                    $results = CachedBuilding::whereIn('audit_id',$auditsIdForApp)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = CachedBuilding::whereIn('audit_id',$auditsIdForApp)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_cached_units', function (Request $request) {

            try {
            
                $lastEdited = $request->query("updated_at");
                $auditsIdForApp = CachedAudit::where('step_id','>=','55')->where('step_id','<=','65')->select('audit_id')->get();
                if($lastEdited != null)
                    $results = CachedUnit::whereIn('audit_id',$auditsIdForApp)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = CachedUnit::whereIn('audit_id',$auditsIdForApp)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_units', function (Request $request) {

            try {
            
                $lastEdited = $request->query("updated_at");
                $auditsIdForApp = CachedAudit::where('step_id','>=','55')->where('step_id','<=','65')->select('audit_id')->get();
                if($lastEdited != null)
                    $results = Unit::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Unit::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_audits', function (Request $request) {

            try {

                $cached_audits = CachedAudit::select('audit_id')->get();

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Audit::whereIn('id', $cached_audits)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Audit::whereIn('id', $cached_audits)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_buildings', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Building::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Building::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_users', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = User::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = User::get();

                    
                foreach ($results as $user) {
                    if(pin != null)
                    {
                        $user->pin = decrypt($user->pin); 
                    }
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_auditor_users', function (Request $request) {

            try {


                $auditors = AuditAuditor::select('user_id')->get();

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = User::whereIn('id', $auditors)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = User::whereIn('id', $auditors)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_auditor_people', function (Request $request) {

            try {


                $auditors = AuditAuditor::select('user_id')->get();

                $people_ids = User::whereIn('id', $auditors)->select('person_id')->get();

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = People::where('updated_at', '>', $lastEdited)->whereIn('id', $people_ids)->get();
                else
                    $results = People::whereIn('id', $people_ids)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_auditor_phone_numbers', function (Request $request) {

            try {


                $auditors = AuditAuditor::select('user_id')->get();

                $people_ids = User::whereIn('id', $auditors)->select('person_id')->get();

                $phone_number_ids = People::whereIn('id', $people_ids)->select('default_phone_number_key')->get();

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = PhoneNumbers::where('updated_at', '>', $lastEdited)->whereIn('phone_number_key', $phone_number_ids)->get();
                else
                    $results = PhoneNumbers::whereIn('phone_number_key', $phone_number_ids)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_audit_auditors', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = AuditAuditor::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = AuditAuditor::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_amenities', function (Request $request) {

            try {
                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Amenity::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Amenity::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_project_amenities', function (Request $request) {

            try {
            
                $lastEdited = $request->query("updated_at");
                $project_ids = CachedAudit::select('project_id')->get();

                if($lastEdited != null)
                    $results = ProjectAmenity::whereIn('project_id',$project_ids)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = ProjectAmenity::whereIn('project_id',$project_ids)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });



        Route::get('/get_unit_amenities', function (Request $request) {

            try {
                                
                $lastEdited = $request->query("updated_at");
                $units = CachedUnit::select('unit_id')->get();
                
                if($lastEdited != null)
                    $results = UnitAmenity::whereIn('unit_id',$units)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = UnitAmenity::whereIn('unit_id',$units)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_building_amenities', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                $building_ids = CachedBuilding::select('building_id')->get();

                if($lastEdited != null)
                    $results = BuildingAmenity::whereIn('building_id',$building_ids)->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = BuildingAmenity::whereIn('building_id',$building_ids)->get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_ordering_amenities', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = OrderingAmenity::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = OrderingAmenity::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_ordering_buildings', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = OrderingBuilding::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = OrderingBuilding::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });


        Route::get('/get_ordering_units', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = OrderingUnit::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = OrderingUnit::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        Route::get('/get_amenity_inspections', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = AmenityInspection::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = AmenityInspection::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_finding_types', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = FindingType::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = FindingType::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_comments', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Comment::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Comment::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_findings', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Finding::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Finding::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });

        Route::get('/set_pin', function (Request $request) {

            try {
            
                $pin = $request->query("pin");
                
                $user_id = Auth::user()->id;

                $user = User::where('id',$user_id)->first();
                $user->pin = encrypt($pin);
                $user->save();
                $result = '1';

                if ($result) {
                    $reply = $result;
                } else {
                    $reply = null;
                }

                return response($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        
        Route::get('/get_amenity_huds', function (Request $request) {

            try {

                $results = AmenityHud::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        

        
        Route::get('/get_hud_finding_types', function (Request $request) {

            try {

                $results = HudFindingType::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        Route::get('/get_followups', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Followup::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Followup::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        Route::get('/get_unit_inspections', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = UnitInspection::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = UnitInspection::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        Route::get('/get_building_inspections', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = BuildingInspection::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = BuildingInspection::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        Route::get('/get_unit_programs', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = UnitProgram::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = UnitProgram::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        Route::get('/get_programs', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Program::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Program::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }
        });
        
        
        Route::get('/get_units', function (Request $request) {

            try {

                $lastEdited = $request->query("updated_at");
                if($lastEdited != null)
                    $results = Unit::where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Unit::get();

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            }
            catch (Exception $e) {
                throw $e;
            }

        });
        
        Route::post('/add-finding', function (Request $request){
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

                $date = Carbon::createFromFormat('Y-m-d', $inputs['date'])->format('Y-m-d H:i:s');

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
                        return $finding->id;
                    } else {
                        return '<h2>I added the finding but...</h2>
                            <p>One or more of the default follow-ups had erors- please see below and send this information to your admin.</p>
                            ' . $errors;
                    }
                }
            }
        });

        
        Route::post('/edit-finding', function (Request $request){
            
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
                $date = Carbon::createFromFormat('F j, Y', $inputs['date'])->format('Y-m-d H:i:s');

                $finding = Finding::where('id', '=', $inputs['finding_id'])->first();
                $finding->date_of_finding = $date;
                $finding->finding_type_id = $findingType->id;
                $finding->level = $inputs['level'];
                $finding->save();

                return 1;

            }

        });

        
        Route::post('/save-reply-finding', function (Request $request){
            $inputs = $request->input('inputs');
            parse_str($inputs, $inputs);

            $date = Carbon::now()->format('Y-m-d H:i:s');
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
                if(array_key_exists('local_photos', $inputs)){
                    $local_photos = $inputs['local_photos'];
                }else{
                    $local_photos = null;
                }

                // foreach local document, save finding_id and followup_id
                if($local_photos){
                    if($fromtype == 'followup'){ 
                        foreach($local_photos as $local_photo_id){
                            Photo::where('id', '=', $local_photo_id)->update([
                                'followup_id' => $from->id,
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }elseif($fromtype == 'finding'){ 
                        foreach($local_photos as $local_photo_id){
                            Photo::where('id', '=', $local_photo_id)->update([
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }
                }
                return 1;

            } elseif ($inputs['type'] == 'followup') {

                $due = $inputs['due'];
                $duration = $inputs['duration'];
                // due date
                $due_date = Carbon\Carbon::now();
                if($duration == "hours"){
                    $due_date->addHours($due);
                }elseif($duration == "days"){
                    $due_date->addDays($due);
                }elseif($duration == "weeks"){
                    $due_date->addWeeks($due);
                }elseif($duration == "months"){
                    $due_date->addMonths($due);
                }

                if($inputs['assignee']){
                    $assignee = $inputs['assignee'];
                }else{
                    $assignee = null;
                }
                $description = $inputs['description'];
                $comment = (array_key_exists('comment', $inputs)) ? 1 : 0;
                $photo = (array_key_exists('photo', $inputs)) ? 1 : 0;
                $doc = (array_key_exists('doc', $inputs)) ? 1 : 0;
                
                if(array_key_exists('categories', $inputs)){
                    $categories = $inputs['categories'];
                }else{
                    $categories = null;
                }

                Followup::create([
                    'created_by_user_id' => Auth::user()->id,
                    'assigned_to_user_id' => $assignee,
                    'date_due' => $due_date,
                    'finding_id' => $finding_id,
                    'project_id' => $from->project_id,
                    'audit_id' => $from->audit_id,
                    'comment_type' => $comment,
                    'document_type' => $doc,
                    'document_categories' => json_encode($categories),
                    'photo_type' => $photo,
                    'description' => $description
                ]);
                return 1;
            } elseif ($inputs['type'] == 'document') {

                if(array_key_exists('local_documents', $inputs)){
                    $local_documents = $inputs['local_documents'];
                }else{
                    $local_documents = null;
                }

                // foreach local document, save finding_id and followup_id
                if($local_documents){
                    if($fromtype == 'followup'){ 
                        foreach($local_documents as $local_document_id){
                            Document::where('id', '=', $local_document_id)->update([
                                'followup_id' => $from->id,
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }elseif($fromtype == 'finding'){ 
                        foreach($local_documents as $local_document_id){
                            Document::where('id', '=', $local_document_id)->update([
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }
                }
                return 1;

            }
        });

        Route::post('/findings/reply', function (Request $request){
            $inputs = $request->input('inputs');
            parse_str($inputs, $inputs);

            $date = Carbon::now()->format('Y-m-d H:i:s');
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
                if(array_key_exists('local_photos', $inputs)){
                    $local_photos = $inputs['local_photos'];
                }else{
                    $local_photos = null;
                }

                // foreach local document, save finding_id and followup_id
                if($local_photos){
                    if($fromtype == 'followup'){ 
                        foreach($local_photos as $local_photo_id){
                            Photo::where('id', '=', $local_photo_id)->update([
                                'followup_id' => $from->id,
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }elseif($fromtype == 'finding'){ 
                        foreach($local_photos as $local_photo_id){
                            Photo::where('id', '=', $local_photo_id)->update([
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }
                }
                return 1;

            } elseif ($inputs['type'] == 'followup') {

                $due = $inputs['due'];
                $duration = $inputs['duration'];
                // due date
                $due_date = Carbon\Carbon::now();
                if($duration == "hours"){
                    $due_date->addHours($due);
                }elseif($duration == "days"){
                    $due_date->addDays($due);
                }elseif($duration == "weeks"){
                    $due_date->addWeeks($due);
                }elseif($duration == "months"){
                    $due_date->addMonths($due);
                }

                if($inputs['assignee']){
                    $assignee = $inputs['assignee'];
                }else{
                    $assignee = null;
                }
                $description = $inputs['description'];
                $comment = (array_key_exists('comment', $inputs)) ? 1 : 0;
                $photo = (array_key_exists('photo', $inputs)) ? 1 : 0;
                $doc = (array_key_exists('doc', $inputs)) ? 1 : 0;
                
                if(array_key_exists('categories', $inputs)){
                    $categories = $inputs['categories'];
                }else{
                    $categories = null;
                }

                Followup::create([
                    'created_by_user_id' => Auth::user()->id,
                    'assigned_to_user_id' => $assignee,
                    'date_due' => $due_date,
                    'finding_id' => $finding_id,
                    'project_id' => $from->project_id,
                    'audit_id' => $from->audit_id,
                    'comment_type' => $comment,
                    'document_type' => $doc,
                    'document_categories' => json_encode($categories),
                    'photo_type' => $photo,
                    'description' => $description
                ]);
                return 1;
            } elseif ($inputs['type'] == 'document') {

                if(array_key_exists('local_documents', $inputs)){
                    $local_documents = $inputs['local_documents'];
                }else{
                    $local_documents = null;
                }

                // foreach local document, save finding_id and followup_id
                if($local_documents){
                    if($fromtype == 'followup'){ 
                        foreach($local_documents as $local_document_id){
                            Document::where('id', '=', $local_document_id)->update([
                                'followup_id' => $from->id,
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }elseif($fromtype == 'finding'){ 
                        foreach($local_documents as $local_document_id){
                            Document::where('id', '=', $local_document_id)->update([
                                'finding_id' => $finding_id
                            ]);
                        }
                        
                    }
                }
                return 1;

            }
        });

        Route::post('/findings/{findingid}/cancel', function (Request $request, $findingid){
                $finding = Finding::where('id', '=', $findingid)->first();
                $date = Carbon\Carbon::now()->format('Y-m-d H:i:s');

                $finding->cancelled_at = $date;
                $finding->save();

                return 1;
            });

            Route::post('/findings/{findingid}/restore', function (Request $request, $findingid){
                $finding = Finding::where('id', '=', $findingid)->first();

                $finding->cancelled_at = null;
                $finding->save();

                return 1;
            });
        
        Route::post('/findings/{findingid}/resolve', function(Request $request, $findingid){
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
            });
            
        Route::get('/get_photos', function (Request $request) {

            try {
            
                $lastEdited = $request->query("updated_at");
                $project_ids = CachedBuilding::select('project_id')->get();

                if($lastEdited != null)
                    $results = Photo::whereIn('project_id',$project_ids)->where('deleted', '0')->where('updated_at', '>', $lastEdited)->get();
                else
                    $results = Photo::whereIn('project_id',$project_ids)->where('deleted', '0')->get();
                    
                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (Exception $e) {
                throw $e;
            }
        });



    });

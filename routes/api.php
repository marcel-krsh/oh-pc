<?php

use App\BreakOutType;
use App\Correction;
use App\County;
use App\Device;
use App\Disposition;
use App\DispositionType;
use App\Document;
use App\DocumentCategory;
use App\ExpenseCategory;
use App\Helpers\GeoData;
use App\HowAcquired;
use App\Http\Controllers;
use App\Models\Amenity;
use App\Models\AmenityInspection;
use App\Models\Audit;
use App\Models\AuditAuditor;
use App\Models\Building;
use App\Models\BuildingAmenity;
use App\Models\CachedAmenity;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Comment;
use App\Models\Finding;
use App\Models\FindingType;
use App\Models\OrderingAmenity;
use App\Models\OrderingBuilding;
use App\Models\OrderingUnit;
use App\Models\People;
use App\Models\ProjectAmenity;
use App\Models\UnitAmenity;
use App\Models\User;
use App\Parcel;
use App\ParcelsToReimbursementInvoice;
use App\Photo;
use App\Program;
use App\RecaptureItem;
use App\ReimbursementInvoice;
use App\SiteVisits;
use App\State;
use App\TargetArea;
use App\VisitLists;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
            if (! is_null($request->query('device_name'))) {
                $device->device_name = $request->query('device_name');
            }
            $device->save();
        } elseif (is_null($device->device_name) && ! is_null($request->query('device_name'))) {
            $device->update(['device_name' => $request->query('device_name')]);
        }

        $email = $request->query('email');
        $password = $request->query('password');
        $key = $request->query('api_key');

        if (Auth::attempt(['email'=> $email, 'password' => $password])) {
            $user_verified = $key == Auth::user()->api_token;
        }

        if ($user_verified) {
            return response(Auth::user()->id, 200);
        } else {
            return response('0', 200);
        }
    } catch (\Exception $e) {
        throw $e;
    }
});

    Route::get('/device/wiped', function (Request $request) {
        try {
            //wipe check
            if (! is_null($request->query('device_id')) && $request->query('wiped') == 1) {
                //check to see if device should be wiped
                $device = Device::where('device_id', $request->query('device_id'))->first();
                if (count($device) > 0) {
                    $wipedTime = Carbon::now();
                    $device->last_wiped = $wipedTime;
                    Auth::logout();
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    });

    Route::get('/device/connection_check', function (Request $request) {
        try {
            //wipe check
            if (! is_null($request->query('device_id'))) {
                //check to see if device should be wiped
                $device = Device::where('device_id', $request->query('device_id'))->first();
                if (is_null($device)) {
                    $device = new Device();
                    $device->device_id = $request->query('device_id');
                    if (! is_null($request->query('device_name'))) {
                        $device->device_name = $request->query('device_name');
                    }
                    $device->save();
                    // a return of 2 will signal the device to wipe itself
                    return response('1', 200);
                } elseif (is_null($device->device_name) && ! is_null($request->query('device_name'))) {
                    $device->update(['device_name' => $request->query('device_name')]);
                }
                if (! is_null($device)) {
                    if ($device->remote_wipe == 1) {
                        // a return of 2 will signal the device to wipe itself
                        return response('2', 200);
                    } else {
                        // a return of 1 will simply state the connection is live
                        return response('1', 200);
                    }
                }
            } else {
                return response('1', 200);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    });

    Route::get('/test/url', function (Request $request) {
        try {
            return response($request->getPathInfo(), 200);
        } catch (\Exception $e) {
            throw $e;
        }
    });

    Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
        Route::get('/breakouts/parcel/{parcel}/{format?}', 'ParcelsPTController@breakouts');

        Route::get('/parcels', function (Request $request) {
            try {
                $user_entity = Auth::user()->entity_id;
                $id = $request->query('id');
                if ($id != '') {
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

                        $invoice_id = ParcelsToReimbursementInvoice::select('reimbursement_invoice_id')->where('parcel_id', $parcel->id)->first()->reimbursement_invoice_id;
                        $invoice = ReimbursementInvoice::where('id', $invoice_id)->first();

                        if ($invoice == null) {
                            $parcel->paid = null;
                        } else {
                            $transactions = $invoice->transactions()->get();
                            $transactions = $transactions->sortByDesc('date_cleared');
                            $parcel->paid = $transactions->first()->date_cleared;
                        }

                        $site_visits = SiteVisits::where('parcel_id', $parcel->id)->where('status', '<', '3')->orderBy('visit_date', 'DESC')->get();
                        $visit_count = $site_visits->count();
                        if ($visit_count > 0) {
                            $visit_date = $site_visits->first()->visit_date;
                        } else {
                            $visit_date = null;
                        }

                        $parcel->visit_count = $visit_count;
                        $parcel->visit_date = $visit_date;

                        $disposition = Disposition::where('parcel_id', $parcel->id)->first();
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

                $max = $request->query('max');
                $program_id = $request->query('program_id');
                //$lat = $request->query("lat");
                //$lon = $request->query("lon");
                $distance = $request->query('distance');
                $disposition = $request->query('disposition');
                $last_visited = $request->query('last_visited');

                //With GPS Distance
                if ($program_id != '') {
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
                    ->get(['id', 'parcel_id', 'program_id', 'street_address', 'latitude', 'longitude', 'city', 'state_id', 'county_id', 'target_area_id', 'zip', 'sale_price', 'how_acquired_id', 'how_acquired_explanation', 'units', 'oh_house_district', 'oh_senate_district', 'us_house_district', 'historic_significance_or_district']);

                    $lat = $randomParcel['latitude'];
                    $lon = $randomParcel['longitude'];

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

                        $invoice_id = ParcelsToReimbursementInvoice::select('reimbursement_invoice_id')->where('parcel_id', $parcel->id)->first()->reimbursement_invoice_id;
                        $invoice = ReimbursementInvoice::where('id', $invoice_id)->first();

                        if ($invoice == null) {
                            $parcel->paid = null;
                        } else {
                            $transactions = $invoice->transactions()->get();
                            $transactions = $transactions->sortByDesc('date_cleared');
                            $parcel->paid = $transactions->first()->date_cleared;
                        }

                        $site_visits = SiteVisits::where('parcel_id', $parcel->id)->where('status', '<', '3')->orderBy('visit_date', 'DESC')->get();
                        $visit_count = $site_visits->count();
                        if ($visit_count > 0) {
                            $visit_date = $site_visits->first()->visit_date;
                        } else {
                            $visit_date = null;
                        }

                        $parcel->visit_count = $visit_count;
                        $parcel->visit_date = $visit_date;

                        $disposition = Disposition::where('parcel_id', $parcel->id)->first();
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

                    if ($max == '') {
                        return response()->json($parcels);
                    } else {
                        return response()->json(compact($parcels, $max));
                    }
                }

                return 'Please use the query string id or program_id to return filtered Parcel results';
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/programs', function (Request $request) {
            try {
                $programs = Program::get(['id', 'program_name']);

                return response()->json($programs);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/disposition_types', function (Request $request) {
            try {
                $dispositions = DispositionType::where('active', '1')->get(['id', 'disposition_type_name']);

                return response()->json($dispositions);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/site_visits', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');
                $rows = SiteVisits::where('parcel_id', $parcel_id)->get();

                return response()->json($rows);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/test', function (Request $request) {
            try {
                $invoice_id = ParcelsToReimbursementInvoice::select('id')->where('parcel_id', '1065')->first()->id;
                $invoice = ReimbursementInvoice::where('id', $invoice_id)->first()->transactions()->select('date_cleared')->orderBy('date_cleared', 'DESC')->get();

                return response()->json($invoice);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/add_visit_list', function (Request $request) {
            try {
                $device_id = $request->query('device_id');
                $added_date = $request->query('added_date');
                $parcel_id = $request->query('parcel_id');
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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/update_visit_list', function (Request $request) {
            try {
                $visit_id = $request->query('visit_id');
                $device_id = $request->query('device_id');
                $status = $request->query('status');
                $updated_date = $request->query('updated_date');
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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_visit_list', function (Request $request) {
            try {
                $user_id = $request->query('user_id');

                $visits = VisitLists::where('user_id', $user_id)->where('status', '1')->get();

                if ($visits) {
                    $reply = $visits;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_visit', function (Request $request) {
            try {
                $visit_id = $request->query('visit_id');

                $visit = VisitLists::where('id', $visit_id)->first();

                if ($visit) {
                    $reply = $visit;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/add_site_visit', function (Request $request) {
            try {
                $device_id = $request->query('device_id');
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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/update_site_visit', function (Request $request) {
            try {
                $site_visit_id = $request->input('site_visit_id');
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

                    $visit->all_structures_removed = $all_structures_removed == 'null' ? null : $all_structures_removed;
                    $visit->construction_debris_removed = $construction_debris_removed == 'null' ? null : $construction_debris_removed;
                    $visit->other_notes = $other_notes == 'null' ? null : $other_notes;
                    $visit->corrective_action_required = $corrective_action_required == 'null' ? null : $corrective_action_required;
                    $visit->retainage_released_to_contractor = $retainage_released_to_contractor == 'null' ? null : $retainage_released_to_contractor;
                    $visit->is_a_recap_of_maint_funds_required = $is_a_recap_of_maint_funds_required == 'null' ? null : $is_a_recap_of_maint_funds_required;
                    $visit->amount_of_maint_recapture_due = $amount_of_maint_recapture_due == 'null' ? null : $amount_of_maint_recapture_due;
                    $visit->was_the_property_graded_and_seeded = $was_the_property_graded_and_seeded == 'null' ? null : $was_the_property_graded_and_seeded;
                    $visit->is_there_any_signage = $is_there_any_signage == 'null' ? null : $is_there_any_signage;
                    $visit->is_grass_growing_consistently_across = $is_grass_growing_consistently_across == 'null' ? null : $is_grass_growing_consistently_across;
                    $visit->is_grass_mowed_weeded = $is_grass_mowed_weeded == 'null' ? null : $is_grass_mowed_weeded;
                    $visit->was_the_property_landscaped = $was_the_property_landscaped == 'null' ? null : $was_the_property_landscaped;
                    $visit->nuisance_elements_or_code_violations = $nuisance_elements_or_code_violations == 'null' ? null : $nuisance_elements_or_code_violations;
                    $visit->are_there_environmental_conditions = $are_there_environmental_conditions == 'null' ? null : $are_there_environmental_conditions;

                    $visit->save();

                    $reply = $visit;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/user', function (Request $request) {
            try {
                $id = $request->query('id');

                $user = DB::table('users')->where('id', $id)->first(['id', 'name']);

                if ($user) {
                    $reply = $user;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/program_stats', function (Request $request) {
            try {
                $programs = Program::get(['id', 'program_name']);

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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/documents', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');

                $documents = Document::where('parcel_id', $parcel_id)->get();

                if ($documents) {
                    $reply = $documents;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_document', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_system_id');
                $document_id = $request->query('document_id');

                $parcel = Parcel::where('id', $parcel_id)->first();
                $document = Document::where('id', $document_id)->first();

                $filepath = $document->file_path;
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
                    header('Content-Length: '.Storage::size($fullpath));

                    return $file;
                //return Response::download($file, "123.PDF");
                } else {
                    // Error
                    exit('Requested file does not exist on our server! '.$fullpath);
                }
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/document_categories', function (Request $request) {
            try {
                $categories = DocumentCategory::where('active', '1')->get(['id', 'document_category_name']);

                return response()->json($categories);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/upload_document', function (Request $request) {
            try {
                $parcel_id = $request->get('parcel_id');
                $user_id = $request->get('user_id');
                //$api_token =  $request->input('api_token');

                //$user = User::where('api_token',$api_token)->first();

                //if($user)
                //{
                $parcel = Parcel::where('id', $parcel_id)->first();

                if ($parcel) {
                    if ($request->hasFile('files')) {
                        $files = $request->file('files');
                        $file_count = count($files);
                        $uploadcount = 0; // counter to keep track of uploaded files
                        $document_ids = '';

                        $categories = explode(',', $request->get('categories'));
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
                        $folderpath = 'documents/entity_'.$parcel->entity_id.'/program_'.$parcel->program_id.'/parcel_'.$parcel->id.'/';

                        // sanitize filename
                        $characters = [' ', '�', '`', "'", '~', '"', '\'', '\\', '/'];
                        $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                        // Create a record in documents table
                        $document = new Document([
                        'user_id' => $user_id,
                        'parcel_id' => $parcel->id,
                        'categories' => $categories_json,
                        'filename' => $original_filename,
                        ]);

                        $document->save();

                        // Save document ids in an array to return
                        if ($document_ids != '') {
                            $document_ids = $document_ids.','.$document->id;
                        } else {
                            $document_ids = $document->id;
                        }

                        // Sanitize filename and append document id to make it unique
                        // documents/entity_0/program_0/parcel_0/0_filename.ext
                        $filename = $document->id.'_'.$original_filename;
                        $filepath = $folderpath.$filename;

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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/photos', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');

                $photos = Photo::where('parcel_id', $parcel_id)->where('deleted', '0')->get();

                if ($photos) {
                    $reply = $photos;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_photo', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_system_id');
                $document_id = $request->query('photo_id');

                $parcel = Parcel::where('id', $parcel_id)->first();
                $photo = Photo::where('id', $document_id)->first();

                $filepath = $photo->file_path;
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
                    header('Content-Length: '.Storage::size($fullpath));

                    return $file;
                //return Response::download($file, "123.PDF");
                } else {
                    // Error
                    exit('Requested file does not exist on our server! '.$fullpath);
                }
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/upload_photo', function (Request $request) {
            try {
                $uid = $request->get('uid');
                $parcel_id = $request->get('parcel_id');
                $recorded_date = $request->get('recorded_date');
                $site_visit_id = $request->get('site_visit_id');
                $notes = $request->get('notes');
                $latitude = $request->get('latitude');
                $longitude = $request->get('longitude');
                $correction_id = $request->get('correction_id');
                $comment_id = $request->get('comment_id');
                $deleted = $request->get('deleted');
                $user_id = $request->get('user_id');

                //$user = User::where('api_token',$api_token)->first();

                //if($user)
                //{
                $parcel = Parcel::where('id', $parcel_id)->first();

                if ($parcel) {
                    if ($request->hasFile('photo')) {
                        $file = $request->file('photo');

                        $photo_id = '';

                        //$user = Auth::user();

                        // Create filepath
                        $folderpath = 'photos/entity_'.$parcel->entity_id.'/program_'.$parcel->program_id.'/parcel_'.$parcel->id.'/';

                        // sanitize filename
                        $characters = [' ', '�', '`', "'", '~', '"', '\'', '\\', '/'];
                        $original_filename = str_replace($characters, '_', $file->getClientOriginalName());
                        $original_ext = $file->getClientOriginalExtension();

                        // Create a record in photos table
                        $photo = new Photo([
                        'uid' => $uid,
                        'user_id' => $user_id,
                        'parcel_id' => $parcel->id,
                        'recorded_date' =>  $recorded_date,
                        'site_visit_id' =>  $site_visit_id,
                        'notes' =>  $notes,
                        'latitude' =>  $latitude,
                        'longitude' =>  $longitude,
                        'deleted' =>  false,
                        ]);

                        if ($comment_id == '') {
                            $photo->comment_id = null;
                        } else {
                            $photo->comment_id = $comment_id;
                        }
                        if ($correction_id == '') {
                            $photo->correction_id = null;
                        } else {
                            $photo->correction_id = $correction_id;
                        }

                        $photo->save();

                        $photo_id = $photo->id;

                        $filename = $uid.'.'.$original_ext;
                        $filepath = $folderpath.$filename;

                        $photo->file_path = $filepath;
                        $photo->filename = $filename;
                        $photo->update();

                        //$photo->update([
                        //    'file_path' => $filepath,
                        //    'filename' =>  $filename
                        //]);

                        Storage::put($filepath, File::get($file));

                        $data = [];
                        $data['photo_id'] = $photo_id;
                        $data['filename'] = $filename;
                        $data['filepath'] = $filepath;
                        $data['comment_id'] = $comment_id;
                        $data['correction_id'] = $correction_id;

                        return response()->json($data);
                    } else {
                        return response($request->all());
                    }
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/add_comment', function (Request $request) {
            try {
                $uid = $request->input('uid');
                $parcel_id = $request->input('parcel_id');
                $recorded_date = $request->input('recorded_date');
                $site_visit_id = $request->input('site_visit_id');
                $notes = $request->input('comment');
                $deleted = $request->input('deleted');
                $user_id = $request->input('user_id');

                $parcel = Parcel::where('id', $parcel_id)->first();

                if ($parcel) {
                    // Create a record in documents table
                    $comment = new Comment([
                    'uid' => $uid,
                    'user_id' => $user_id,
                    'parcel_id' => $parcel->id,
                    'recorded_date' =>  $recorded_date,
                    'site_visit_id' =>  $site_visit_id,
                    'comment' =>  $notes,
                    'deleted' =>  false,
                    ]);

                    $comment->save();

                    return response()->json($comment);
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/add_correction', function (Request $request) {
            try {
                $uid = $request->input('uid');
                $parcel_id = $request->input('parcel_id');
                $recorded_date = $request->input('recorded_date');
                $site_visit_id = $request->input('site_visit_id');
                $notes = $request->input('notes');
                $corrected = $request->input('corrected');
                $corrected_site_visit_id = $request->input('corrected_site_visit_id');
                $corrected_user_id = $request->input('corrected_user_id');
                $corrected_date = $request->input('corrected_date');
                $deleted = $request->input('deleted');
                $user_id = $request->input('user_id');

                $parcel = Parcel::where('id', $parcel_id)->first();

                if ($parcel) {
                    // Create a record in documents table
                    $correction = new Correction([
                    'uid' => $uid,
                    'user_id' => $user_id,
                    'parcel_id' => $parcel->id,
                    'recorded_date' =>  $recorded_date,
                    'site_visit_id' =>  $site_visit_id,
                    'notes' =>  $notes,
                    'corrected' =>  $corrected,
                    'corrected_site_visit_id' =>  $corrected_site_visit_id,
                    'corrected_user_id' =>  $corrected_user_id,
                    'corrected_date' =>  $corrected_date,
                    'deleted' =>  false,
                    ]);

                    $correction->save();

                    return response()->json($correction);
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/add_recapture', function (Request $request) {
            try {
                $parcel_id = $request->input('parcel_id');
                $breakout_type = $request->input('breakout_type');
                $recapture_invoice_id = $request->input('recapture_invoice_id');
                $expense_category_id = $request->input('expense_category_id');
                $amount = $request->input('amount');
                $description = $request->input('description');
                $notes = $request->input('notes');

                $parcel = Parcel::where('id', $parcel_id)->first();

                if ($parcel) {
                    // Create a record in documents table
                    $recapture = new RecaptureItem([
                    'breakout_type' => $breakout_type,
                    'recapture_invoice_id' => $recapture_invoice_id,
                    'parcel_id' => $parcel->id,
                    'program_id' =>  $parcel->program_id,
                    'entity_id' =>  $parcel->entity_id,
                    'account_id' =>  $parcel->account_id,
                    'expense_category_id' =>  $expense_category_id,
                    'amount' =>  $amount,
                    'description' =>  $description,
                    'notes' =>  $notes,
                    ]);

                    $recapture->save();

                    return response()->json($recapture);
                } else {
                    return response('No Parcel');
                }
                //}
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/recaptures', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');

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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/comments', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');

                $comments = Comment::where('parcel_id', $parcel_id)->where('deleted', '0')->get();

                if ($comments) {
                    $reply = $comments;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/corrections', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');

                $corrections = Correction::where('parcel_id', $parcel_id)->where('deleted', '0')->get();

                if ($corrections) {
                    $reply = $corrections;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/update_parcel_location', function (Request $request) {
            try {
                $parcel_id = $request->query('parcel_id');
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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::post('/update_correction', function (Request $request) {
            try {
                $correction_id = $request->input('correction_id');
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
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/parcel_lookup', 'ParcelsController@quickLookup');

        Route::get('/get_cached_amenities', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = CachedAmenity::where('updated_at', '>', $lastEdited)->get();
                } else {
                    $results = CachedAmenity::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_cached_audits', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = CachedAudit::where('updated_at', '>', $lastEdited)->get();
                } else {
                    $results = CachedAudit::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_cached_buildings', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = CachedBuilding::where('updated_at', '>', $lastEdited)->get();
                } else {
                    $results = CachedBuilding::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_cached_units', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = CachedUnit::where('updated_at', '>', $lastEdited)->get();
                } else {
                    $results = CachedUnit::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_audits', function (Request $request) {
            try {
                $cached_audits = CachedAudit::select('audit_id')->get();

                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = Audit::whereIn('id', $cached_audits)->where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = Audit::whereIn('id', $cached_audits)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_buildings', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = Building::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = Building::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_users', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = User::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = User::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_auditor_users', function (Request $request) {
            try {
                $auditors = AuditAuditor::select('user_id')->get();

                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = User::whereIn('id', $auditors)->where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = User::whereIn('id', $auditors)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_auditor_people', function (Request $request) {
            try {
                $auditors = AuditAuditor::select('user_id')->get();

                $people_ids = User::whereIn('id', $auditors)->select('person_id')->get();

                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = People::where('last_edited', '>', $lastEdited)->whereIn('id', $people_ids)->get();
                } else {
                    $results = People::whereIn('id', $people_ids)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_auditor_phone_numbers', function (Request $request) {
            try {
                $auditors = AuditAuditor::select('user_id')->get();

                $people_ids = User::whereIn('id', $auditors)->select('person_id')->get();

                $phone_number_ids = People::whereIn('id', $people_ids)->select('default_phone_number_key')->get();

                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = PhoneNumbers::where('last_edited', '>', $lastEdited)->whereIn('phone_number_key', $phone_number_ids)->get();
                } else {
                    $results = PhoneNumbers::whereIn('phone_number_key', $phone_number_ids)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_audit_auditors', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = AuditAuditor::where('updated_at', '>', $lastEdited)->get();
                } else {
                    $results = AuditAuditor::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_amenities', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = Amenity::where('updated_at', '>', $lastEdited)->get();
                } else {
                    $results = Amenity::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_project_amenities', function (Request $request) {
            try {
                $user_key = $request->query('user_key');

                $audit_ids = AuditAuditor::where('user_key', $user_key)->select('audit_id')->get();

                $projects = CachedAudit::whereIn('audit_id', $audit_ids)->select('project_key')->get();

                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = ProjectAmenity::whereIn('project_key', $projects)->where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = ProjectAmenity::whereIn('project_key', $projects)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_unit_amenities', function (Request $request) {
            try {
                //set_time_limit(300);

                $user_key = $request->query('user_key');

                $audit_ids = AuditAuditor::where('user_key', $user_key)->select('audit_id')->get();

                $units = CachedUnit::whereIn('audit_id', $audit_ids)->select('unit_id')->get();

                $lastEdited = $request->query('last_edited');

                if ($lastEdited != null) {
                    $results = UnitAmenity::whereIn('unit_id', $units)->where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = UnitAmenity::whereIn('unit_id', $units)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_building_amenities', function (Request $request) {
            try {
                $user_key = $request->query('user_key');

                $audit_ids = AuditAuditor::where('user_key', $user_key)->select('audit_id')->get();

                $buildings = CachedBuilding::where('building_key', '!=', null)->whereIn('audit_id', $audit_ids)->select('building_key')->get();

                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = BuildingAmenity::whereIn('building_key', $buildings)->where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = BuildingAmenity::whereIn('building_key', $buildings)->get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_ordering_amenities', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = OrderingAmenity::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = OrderingAmenity::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_ordering_buildings', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = OrderingBuilding::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = OrderingBuilding::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_ordering_units', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = OrderingUnit::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = OrderingUnit::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_amenity_inspections', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = AmenityInspection::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = AmenityInspection::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_finding_types', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = FindingType::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = FindingType::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_comments', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = Comment::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = Comment::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });

        Route::get('/get_findings', function (Request $request) {
            try {
                $lastEdited = $request->query('last_edited');
                if ($lastEdited != null) {
                    $results = Finding::where('last_edited', '>', $lastEdited)->get();
                } else {
                    $results = Finding::get();
                }

                if ($results) {
                    $reply = $results;
                } else {
                    $reply = null;
                }

                return response()->json($reply);
            } catch (\Exception $e) {
                throw $e;
            }
        });
    });

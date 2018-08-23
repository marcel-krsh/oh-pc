<?php

namespace App\Http\Controllers;

use App\Models\DocumentRule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Auth;
use App\Models\DispositionType;
use Gate;
use File;
use Storage;
use App\Models\SfParcel;
use App\Models\Programs;
use Illuminate\Http\Request;
use DB;
use App\Models\Parcel;
use App\Models\SfReimbursements;
use App\Models\Helpers\GeoData;
use App\LogConverter;
use App\Models\ExpenseCategory;
use App\Models\Vendor;
use App\Models\CostItem;
use App\Models\ProgramRule;

class ParcelCostController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function showCostModal(Parcel $parcel)
    {
        $entity_id = $parcel->entity_id;
        $expense_categories = ExpenseCategory::where('id', '!=', 1)->get();
        $vendors = Vendor::where('active', '=', 1)
                    //->join('vendors_to_entities','vendors_to_entities.vendor_id','=','vendors.id')
                    //->where('vendors_to_entities.entity_id','=',$entity_id)
                    ->with('state')
                    ->orderBy('vendor_name', 'ASC')
                    ->get();

        foreach ($vendors as $vendor) {
            $vendor->vendor_name = str_replace(['"',], [''], $vendor->vendor_name);
        }

        // check if a category can have the advance checkbox
        $program_rules = ProgramRule::where('id', '=', $parcel->program_rules_id)->first();
        $advance_rules = [];
        $i = 0;
        foreach ($expense_categories as $expense_category) {
            switch ($expense_category->id) {
                case 2: // acquisition
                    if ($program_rules->acquisition_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Acquisition";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Acquisition";
                    }
                    break;
                case 3: // pre-demo
                    if ($program_rules->pre_demo_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Pre-Demo";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Pre-Demo";
                    }
                    break;
                case 4: // demo
                    if ($program_rules->demolition_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Acquisition";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Demolition";
                    }
                    break;
                case 5: // greening
                    if ($program_rules->greening_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Greening (Post Demo)";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Greening (Post Demo)";
                    }
                    break;
                case 6: // maintenance
                    if ($program_rules->maintenance_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Maintenance";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Maintenance";
                    }
                    break;
                case 7: // admin
                    if ($program_rules->administration_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Administration";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Administration";
                    }
                    break;
                case 8: // other
                    if ($program_rules->other_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "Other";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "Other";
                    }
                    break;
                case 9: // loan payoff
                    if ($program_rules->nip_loan_payoff_advance == 1) {
                        $advance_rules[$i]["advance"] = 1;
                        $advance_rules[$i]["name"] = "NIP Loan Payoff";
                    } else {
                        $advance_rules[$i]["advance"] = 0;
                        $advance_rules[$i]["name"] = "NIP Loan Payoff";
                    }
                    break;
            }
            $i++;
        }

        return view('modals.parcel-costs', compact('parcel', 'expense_categories', 'vendors', 'advance_rules'));
    }

    public function saveCost(Parcel $parcel, Request $request)
    {
        $rows = $request->get('data');
        $program_rules = ProgramRule::where('id', '=', $parcel->program_rules_id)->first();

        $cost_added = 0;

        foreach ($rows as $row) {
            $category_name = $row[0];
            $expense = $row[1];
            if ($row[2] == true) {
                $advance = 1;
                $breakoutType = 3;
            } else {
                $advance = 0;
                $breakoutType = 1;
            }
            $vendor_name = $row[3];
            $cost = $row[4];
            $note = $row[5];

            // get category id
            if ($category_name) {
                $category = ExpenseCategory::where('expense_category_name', '=', $category_name)->first();
                $categoryid = $category->id;
            } else {
                $categoryid = 1;
            }
            
            // get vendor id
            if ($vendor_name) {
                $vendor_name = str_replace(['&amp;',], ['&'], $vendor_name);
                $vendor_name = str_replace(['&#039;',], ["'"], $vendor_name);
                $vendor = Vendor::where('vendor_name', 'LIKE', '%'.$vendor_name.'%')->first();
                $vendorid = $vendor->id;
            } else {
                $vendorid = 1;
            }
            
            // check program rules to make sure we can save advance
            switch ($categoryid) {
                case 2: // acquisition
                    if ($program_rules->acquisition_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 3: // pre-demo
                    if ($program_rules->pre_demo_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 4: // demo
                    if ($program_rules->demolition_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 5: // greening
                    if ($program_rules->greening_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 6: // maintenance
                    if ($program_rules->maintenance_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 7: // admin
                    if ($program_rules->administration_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 8: // other
                    if ($program_rules->other_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
                case 9: // loan payoff
                    if ($program_rules->nip_loan_payoff_advance != 1) {
                        $advance = 0;
                        $breakoutType = 1;
                    }
                    break;
            }

            // add new cost
            if ($cost > 0) {
                $cost_item = new CostItem([
                    'breakout_type' => $breakoutType,
                    'entity_id' => $parcel->entity_id,
                    'program_id' => $parcel->program_id,
                    'account_id' => $parcel->account_id,
                    'parcel_id' => $parcel->id,
                    'expense_category_id' => $categoryid,
                    'amount' => $cost,
                    'vendor_id' => $vendorid,
                    'description' => $expense,
                    'notes' => $note,
                    'advance' => $advance
                ]);
                $cost_item->save();
                $cost_added = 1;
            }
        }

        // special case when invoice already exist, update statuses
        if ($cost_added) {
            $parcel->load('associatedInvoice');
            $parcel->load('associatedPo');
            $parcel->load('associatedRequest');


            if ($parcel->associatedInvoice || $parcel->associatedPo || $parcel->associatedRequest) {
                // now we have a new cost item, the request, po and invoices are no longer valid
                // uncheck Mark as paid
                guide_set_progress($parcel->id, 54, $status = 'started', 0); // mark as paid
                guide_set_progress($parcel->id, 53, $status = 'started', 0); // step 6

                // remove approvals in invoice & fiscal agent notification
                if ($parcel->associatedInvoice) {
                    $parcel->associatedInvoice->invoice->first()->resetApprovals();
                }
                guide_set_progress($parcel->id, 52, $status = 'started', 0); // notify fiscal agent
                guide_set_progress($parcel->id, 51, $status = 'started', 0); // tier 3 approval
                guide_set_progress($parcel->id, 50, $status = 'started', 0); // tier 2 approval
                guide_set_progress($parcel->id, 49, $status = 'started', 0); // tier 1 approval
                guide_set_progress($parcel->id, 48, $status = 'started', 0); // step 5

                // uncheck step 4 items
                guide_set_progress($parcel->id, 47, $status = 'started', 0); // send invoice to HFA
                guide_set_progress($parcel->id, 46, $status = 'started', 0); // approve the invoice
                guide_set_progress($parcel->id, 44, $status = 'started', 0); // step 4

                // uncheck step 3: enter approved amounts, parcel approved in PO, PO ready for compliance, PO approved, PO sent

                if ($parcel->associatedPo) {
                    $parcel->associatedPo->po->first()->resetApprovals();

                    // if there is a compliance for this parcel, delete it and reset flags in table
                    if ($parcel->compliance || $parcel->compliance_manual) {
                        if (count($parcel->compliances)) {
                            foreach ($parcel->compliances as $compliance) {
                                $compliance->delete();
                            }
                            $parcel->update([
                                'compliance_score' => null
                            ]);
                        }
                    }
                }
                guide_set_progress($parcel->id, 55, $status = 'started', 0); // Approve parcel in PO
                guide_set_progress($parcel->id, 39, $status = 'started', 0); // send PO to LB
                guide_set_progress($parcel->id, 38, $status = 'started', 0); // final PO approval
                guide_set_progress($parcel->id, 37, $status = 'started', 0); // complete compliance review
                guide_set_progress($parcel->id, 36, $status = 'started', 0); // po ready for compliance
                guide_set_progress($parcel->id, 35, $status = 'started', 0); // enter approved amount
                guide_set_progress($parcel->id, 32, $status = 'started', 0); // step 3

                // uncheck step 2: request approved, request sent to HFA
                
                if ($parcel->associatedRequest) {
                    $parcel->associatedRequest->request->first()->resetApprovals();
                }
                guide_set_progress($parcel->id, 31, $status = 'started', 0); // send request to HFA
                guide_set_progress($parcel->id, 30, $status = 'started', 0); // approve request
                guide_set_progress($parcel->id, 28, $status = 'started', 0); // step 2

                // uncheck step 1: enter request amount
                guide_set_progress($parcel->id, 26, $status = 'started', 0); // enter request amount
                guide_set_progress($parcel->id, 23, $status = 'started', 0); // step 1
            }
        }

        return 1;
    }
    public function addBreakOut(Request $request)
    {
        

        // get info needed to record entry
        $parcel = DB::table('parcels')->select('*')->where('id', $request->parcel_id)->first();
        if (count($parcel)< 1) {
            $msg = ['message'=> "I was not able to find the associated parcel. That is weird right? Please reload the parcel to make sure it 1. exists, and 2. I have it's information to work with. If the problem persists, contact Greenwood 360 and let them know of the issue, and parcel number you're having trouble with.",'status'=>1];
            return json_encode($msg);
        }
            
        // are they HFA or LB?
        if (Auth::user()->entity_type == "landbank") {
            /// landbank eh? - make sure their enity is the entity of the parcel?
            if ($parcel->entity_id == Auth::user()->entity_id) {
                $pass = 1;
            } else {
                $msg = ['message'=> 'Sorry you don\'t have access to this parcel.','status'=>1];
                return json_encode($msg);
            }
        } else {
            $pass = 1;
        }
        $cost = \App\Models\CostItem::find($request->reference_id);
        if (count($cost)< 1) {
            $msg = ['message'=> "I wasn't able to lookup the cost associated with this. Did someone delete it while you were working on it?",'status'=>1];
            return json_encode($msg);
        }
        if ($pass == 1) {
            if ($request->type == "request") {
                \App\Models\RequestItem::create([
                    'breakout_type' => $cost->breakout_type,
                    'parcel_id'=> $parcel->id,
                    'account_id'=> $parcel->account_id,
                    'program_id'=> $cost->program_id,
                    'entity_id'=> $parcel->entity_id,
                    'expense_category_id'=> $cost->expense_category_id,
                    'amount'=> floatval($request->amount),
                    'vendor_id'=> $cost->vendor_id,
                    'description'=> $cost->description,
                    'notes'=> $cost->notes,
                    'ref_id'=> $cost->id,
                    'breakout_item_status_id'=> 1
                    ]);
                $msg = ['message'=> 'I Added the requested amount successfully.','status'=>1];
                return json_encode($msg);
            }
        }
    }
}

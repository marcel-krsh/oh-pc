<?php

namespace App\Http\Controllers;

use App\Models\DocumentRule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Auth;
use Gate;
use File;
use Storage;
use App\Models\Programs;
use Illuminate\Http\Request;
use DB;
use App\Models\Parcel;
use App\Models\User;
use App\Models\ReimbursementRequest;
use App\Models\ParcelsToReimbursementRequest;

class RequestController extends Controller
{
    public function getParcelsFromRequestId(ReimbursementRequest $r)
    {
        // get parcel ids associated with the request
        $parcelids_array = ParcelsToReimbursementRequest::where('reimbursement_request_id', $r->id)->pluck('parcel_id')->toArray();
        // get parcels from parcel ids array
        //$parcels = Parcel::whereIn('id', $parcelids_array)->get();

        // $parcels = Parcel::join('programs', 'programs.id', '=', 'parcels.program_id')
        //                       ->join('counties', 'parcels.county_id', '=', 'counties.id')
        //                       ->join('states', 'parcels.state_id', '=', 'states.id')
        //                       ->join('property_status_options as hfa', 'parcels.hfa_property_status_id', '=', 'hfa.id')
        //                       ->join('property_status_options as lb', 'parcels.landbank_property_status_id', '=', 'lb.id')
        //                       ->join('entities', 'programs.entity_id','=','entities.id')
        //                       ->leftJoin('parcels_to_reimbursement_requests','parcels.id','=','parcels_to_reimbursement_requests.parcel_id')
        //                       ->leftJoin('parcels_to_purchase_orders','parcels.id','=','parcels_to_purchase_orders.parcel_id')
        //                       ->leftJoin('parcels_to_reimbursement_invoices','parcels.id','=','parcels_to_reimbursement_invoices.parcel_id')
        //                       ->select('parcels.id as parcel_system_id','parcels.street_address','parcels.city','states.state_acronym','parcels.zip','parcels.parcel_id','programs.program_name','entities.entity_name','parcels.created_at','parcels.google_map_link','hfa.option_name as hfa_option_name','lb.option_name as lb_option_name','reimbursement_request_id','reimbursement_invoice_id','purchase_order_id',
        //                           DB::raw("(select sum(cost_items.amount) from cost_items where cost_items.parcel_id = parcel_system_id) as cost_total"),
        //                           DB::raw("(select sum(request_items.amount) from request_items where request_items.parcel_id = parcel_system_id) as requested_total"),
        //                           DB::raw("(select sum(po_items.amount) from po_items where po_items.parcel_id = parcel_system_id) as approved_total"),
        //                           DB::raw("(select sum(invoice_items.amount) from invoice_items where invoice_items.parcel_id = parcel_system_id) as invoiced_total"))
        //                       ->whereIn('parcels.id', $parcelids_array)
        //                       ->get();

        $r->load('status')
            ->load('parcels')
            ->load('entity');

        $total = 0;
        $r->legacy = 0;
        setlocale(LC_MONETARY, 'en_US');
        foreach ($r->parcels as $parcel) {
            $parcel->load('landbankPropertyStatus');
            $parcel->load('hfaPropertyStatus');
            $parcel->load('program');

            $parcel->load('associatedRequest');
            if ($parcel->associatedRequest) {
                $parcel->reimbursement_request_id = $parcel->associatedRequest->reimbursement_request_id;
            } else {
                $parcel->reimbursement_request_id = 0;
            }
            
            $parcel->load('associatedPo');
            if ($parcel->associatedPo) {
                $parcel->purchase_order_id = $parcel->associatedPo->purchase_order_id;
            } else {
                $parcel->purchase_order_id = 0;
            }

            $parcel->load('associatedInvoice');
            if ($parcel->associatedInvoice) {
                $parcel->reimbursement_invoice_id = $parcel->associatedInvoice->reimbursement_invoice_id;
            } else {
                $parcel->reimbursement_invoice_id = 0;
            }

            $parcel->costTotal = $parcel->costTotal();
            $parcel->requestedTotal = $parcel->requestedTotal();
            $parcel->requested_total_formatted = money_format('%n', $parcel->requestedTotal);
            $parcel->approved_total = $parcel->approvedTotal();
            $parcel->invoiced_total = $parcel->invoicedTotal();
            $total = $total + $parcel->requestedTotal;
            if ($parcel->legacy == 1 || $parcel->sf_parcel_id != null) {
                $r->legacy = 1;
            }

            $parcel->created_at_m = date('m', strtotime($parcel->created_at));
            $parcel->created_at_d = date('d', strtotime($parcel->created_at));
            $parcel->created_at_Y = date('Y', strtotime($parcel->created_at));

            $parcel->cost_total_formatted = money_format('%n', $parcel->costTotal);
            $parcel->requested_total_formatted = money_format('%n', $parcel->requestedTotal);
            $parcel->approved_total_formatted = money_format('%n', $parcel->approved_total);
            $parcel->invoiced_total_formatted = money_format('%n', $parcel->invoiced_total);
        }
        $total = money_format('%n', $total);

        return $r->parcels;
    }
}

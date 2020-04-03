<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Request;
use Gate;
use Auth;
use App\Models\User;
use File;
use Storage;
use Session;
use Carbon;
use DB;
use App\Models\Programs;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentRule;
use App\Models\DocumentRuleEntry;
use App\Models\Entity;
use App\Models\Parcel;
use App\LogConverter;
use App\Models\Disposition;
use App\Models\DispositionType;
use App\Models\ProgramRule;
use App\Models\InvoiceItem;
use App\Models\ReimbursementInvoice;
use App\Models\ParcelsToReimbursementInvoice;
use App\Models\Transaction;
use App\Models\ApprovalRequest;
use App\Models\ApprovalAction;
use App\Models\DispositionsToInvoice;
use App\Models\RecaptureInvoice;
use App\Models\RecaptureItem;
use App\Models\Mail\RecaptureApproverNotification;
// use App\Models\Mail\EmailNotificationDispositionReleaseRequested;
use App\Models\Mail\EmailNotificationRecapturePaymentRequested;
// use App\Models\Mail\EmailNotificationDispositionReview;
use DateTime;
use App\Models\GuideStep;
use App\Models\GuideProgress;
use App\Models\RecaptureInvoiceNote;
use App\Models\CostItem;

class RecaptureController extends Controller
{
    /*
    * getRecapturesFromParcelId
    * getRecapturesFromInvoiceId
    * getRecaptureInvoice
    * computeDisposition
    */

     public function __construct(){
        $this->allitapc();
    }

    // public function recaptureList(Request $request)
    // {
    //     if (Gate::allows('view-recapture')  || Auth::user()->entity_id == 1) {
    //         $lc = new LogConverter('recapturelist', 'view');
    //         $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . 'viewed recapturelist')->save();
    //         // determine if they are OHFA or not
    //         if (Auth::user()->entity_id != 1) {
    //             // create values for a where clause
    //             $where_entity_id = Auth::user()->entity_id;
    //             $where_entity_id_operator = '=';
    //         } else {
    //             // they are OHFA - see them all
    //             $where_entity_id = 0;
    //             $where_entity_id_operator = '>';
    //         }

    //         // Quick check for legacy records to make sure that all records have status id
    //         if (RecaptureItem::where('status_id', '=', null)->orwhere('status_id', '>', 7)->count()) {
    //             $recaptures_to_update = RecaptureItem::where('status_id', '=', null)->orwhere('status_id', '>', 7)->get();
    //             foreach ($recaptures_to_update as $recapture) {
    //                 $recapture->update([
    //                     'status_id' => 1,
    //                 ]);
    //             }
    //         }

    //         /// The sorting column
    //         $sortedBy = $request->query('recaptures_sort_by');
    //         /// Retain the original value submitted through the query
    //         if (strlen($sortedBy)>0) {
    //             // update the sort by
    //             session(['recaptures_sorted_by_query'=>$sortedBy]);
    //             $recaptures_sorted_by_query = $request->session()->get('recaptures_sorted_by_query');
    //         } elseif (!is_null($request->session()->get('recaptures_sorted_by_query'))) {
    //             // use the session value
    //             $recaptures_sorted_by_query = $request->session()->get('recaptures_sorted_by_query');
    //         } else {
    //             // set the default
    //             session(['recaptures_sorted_by_query'=>'1']);
    //             $recaptures_sorted_by_query = $request->session()->get('recaptures_sorted_by_query');
    //         }

    //         /// If a new sort has been provided
    //         // Rebuild the query

    //         if (!is_null($sortedBy)) {
    //             switch ($request->query('recaptures_asc_desc')) {
    //                 case '1':
    //                     # code...
    //                 session(['recaptures_asc_desc'=> 'desc']);
    //                 $recapturesAscDesc =  $request->session()->get('recaptures_asc_desc');
    //                 session(['recaptures_asc_desc_opposite' => ""]);
    //                 $recapturesAscDescOpposite =  $request->session()->get('recaptures_asc_desc_opposite');
    //                     break;
                    
    //                 default:
    //                 session(['recaptures_asc_desc'=> 'asc']);
    //                 $recapturesAscDesc =  $request->session()->get('recaptures_asc_desc');
    //                 session(['recaptures_asc_desc_opposite' => 1]);
    //                 $recapturesAscDescOpposite = $request->session()->get('recaptures_asc_desc_opposite');
    //                     break;
    //             }
    //             switch ($sortedBy) {
    //                 case '1':
    //                     # created_at
    //                     session(['recaptures_sort_by' => 'recapture_items.created_at']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 case '2':
    //                     # request_id
    //                     session(['recaptures_sort_by' => 'recapture_items.id']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 case '3':
    //                     # account_id
    //                     session(['recaptures_sort_by' => 'recapture_items.account_id']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 case '4':
    //                     # program_id
    //                     session(['recaptures_sort_by' =>'recapture_items.program_id']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 case '5':
    //                     # entity_id
    //                     session(['recaptures_sort_by' =>'recapture_items.entity_id']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 case '6':
    //                     # total_parcels
    //                     session(['recaptures_sort_by' => 'pid']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 // case '9':
    //                 //     #  total_amount
    //                 //     session(['recaptures_sort_by' => 'total_invoiced']);
    //                 //     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                 //     break;
    //                 // case '12':
    //                 //     #  total_paid
    //                 //     session(['recaptures_sort_by' => 'total_paid']);
    //                 //     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                 //     break;
    //                 case '10':
    //                     #  breakout_item_status_name
    //                     session(['recaptures_sort_by' => 'status_name']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //                 default:
    //                     # code...
    //                     session(['recaptures_sort_by' => 'recapture_items.created_at']);
    //                     $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //                     break;
    //             }
    //         } elseif (is_null($request->session()->get('recaptures_sort_by'))) {
    //             // no values in the session - then store in simpler variables.
    //             session(['recaptures_sort_by' => 'recapture_items.created_at']);
    //             $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //             session(['recaptures_asc_desc' => 'asc']);
    //             $recapturesAscDesc = $request->session()->get('recaptures_asc_desc');
    //             session(['recaptures_asc_desc_opposite' => '1']);
    //             $recapturesAscDescOpposite = $request->session()->get('recaptures_asc_desc_opposite');
    //         } else {
    //             // use values in the session
    //             $recapturesSortBy = $request->session()->get('recaptures_sort_by');
    //             $recapturesAscDesc = $request->session()->get('recaptures_asc_desc');
    //             $recapturesAscDescOpposite = $request->session()->get('recaptures_asc_desc_opposite');
    //         }

    //         // Check if there is a Program Filter Provided
    //         if (is_numeric($request->query('recaptures_program_filter'))) {
    //             //Update the session
    //             session(['recaptures_program_filter' => $request->query('recaptures_program_filter')]);
    //             $recapturesProgramFilter = $request->session()->get('recaptures_program_filter');
    //             session(['recaptures_program_filter_operator' => '=']);
    //             $recapturesProgramFilterOperator = $request->session()->get('recaptures_program_filter_operator');
    //         } elseif (is_null($request->session()->get('recaptures_program_filter')) || $request->query('recaptures_program_filter') == 'ALL') {
    //             // There is no Program Filter in the Session
    //             session(['recaptures_program_filter' => '%']);
    //             $recapturesProgramFilter = $request->session()->get('recaptures_program_filter');
    //             session(['recaptures_program_filter_operator' => 'LIKE']);
    //             $recapturesProgramFilterOperator = $request->session()->get('recaptures_program_filter_operator');
    //         } else {
    //             // use values in the session
    //             $recapturesProgramFilter = $request->session()->get('recaptures_program_filter');
    //             $recapturesProgramFilterOperator = $request->session()->get('recaptures_program_filter_operator');
    //         }

    //         if (is_numeric($request->query('recaptures_status_filter'))) {
    //             //Update the session
    //             session(['recaptures_status_filter' => $request->query('recaptures_status_filter')]);
    //             $recapturesStatusFilter = $request->session()->get('recaptures_status_filter');
    //             session(['recaptures_status_filter_operator' => '=']);
    //             $recapturesStatusFilterOperator = $request->session()->get('recaptures_program_filter_operator');
    //         } elseif (is_null($request->session()->get('recaptures_status_filter')) || $request->query('recaptures_status_filter') == 'ALL') {
    //             // There is no Status Filter in the Session
    //             session(['recaptures_status_filter' => '%']);
    //             $recapturesStatusFilter = $request->session()->get('recaptures_status_filter');
    //             session(['recaptures_status_filter_operator' => 'LIKE']);
    //             $recapturesStatusFilterOperator = $request->session()->get('recaptures_status_filter_operator');
    //         } else {
    //             // use values in the session
    //             $recapturesStatusFilter = $request->session()->get('recaptures_status_filter');
    //             $recapturesStatusFilterOperator = $request->session()->get('recaptures_status_filter_operator');
    //         }
            
    //         // Insert other Filters here
    //         $currentUser = Auth::user();

    //         //// set the defualt begining for just a status filter - should start with where if there
    //         //// is no program filter
    //         $and = ' WHERE ';
    //         if ($recapturesProgramFilter) {
    //             $recapturesWhereOrder = "WHERE recapture_items.program_id ".$recapturesProgramFilterOperator." '".$recapturesProgramFilter."' \n";
    //             $and = ' AND ';
    //         }

    //         if ($recapturesStatusFilter) {
    //             $recapturesWhereOrder .= $and."recapture_items.status_id ".$recapturesStatusFilterOperator." '".$recapturesStatusFilter."' \n";
    //             $and = ' AND ';
    //         }

    //         $recapturesWhereOrder .= $and." recapture_items.entity_id $where_entity_id_operator '$where_entity_id' "." \n";
    //         if ($recapturesSortBy) {
    //             $recapturesWhereOrder .="ORDER BY ".$recapturesSortBy." ".$recapturesAscDesc;
    //         }
           
    //         $recaptures = DB::select(
    //             DB::raw("
    //                         SELECT
    //                             recapture_items.id AS id ,
    //                             recapture_items.account_id,
    //                             recapture_items.created_at as 'date',
    //                             recapture_items.status_id,
    //                             recapture_items.program_id,
    //                             recapture_items.entity_id,
    //                             recapture_items.parcel_id,
    //                             parcels.parcel_id  as pid,
    //                             invstat.invoice_status_name as status_name,
    //                             pr.program_name ,
    //                             ent.entity_name
                                
    //                         FROM
    //                             dispositions

    //                         INNER JOIN parcels ON recapture_items.parcel_id = parcels.id
    //                         INNER JOIN programs pr ON recapture_items.program_id = pr.id
    //                         INNER JOIN entities ent ON recapture_items.entity_id = ent.id
    //                         LEFT JOIN invoice_statuses invstat ON recapture_items.status_id = invstat.id

    //                         ".$recapturesWhereOrder."
    //                     ")
    //         );

    //         $programs = RecaptureItem::join('programs', 'recapture_items.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->get()->all();
    //         $statuses = RecaptureItem::join('invoice_statuses', 'recapture_items.status_id', '=', 'invoice_statuses.id')
    //                         ->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')
    //                         ->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')
    //                         ->get()
    //                         ->all();

    //         return view('dashboard.recapture_list', compact('recaptures', 'programs', 'statuses', 'currentUser', 'recaptures_sorted_by_query', 'recapturesAscDesc', 'recapturesAscDescOpposite', 'programs', 'recapturesProgramFilter', 'recapturesStatusFilter'));
    //     } else {
    //         return 'Sorry you do not have access to the Recapture Listing page. Please try logging in again or contact your admin to request access.';
    //     }

    //     if (Auth::user()->entity_id != 1) {
    //         $recaptures = RecaptureItem::where('entity_id', '=', Auth::user()->entity_id)->get();
    //     } else {
    //         $recaptures = RecaptureItem::get();
    //     }
        
    //     return view('dashboard.recapture_list', compact('recaptures'));
    // }

    public function recaptureInvoiceList(Request $request)
    {
        if (Gate::allows('view_recapture') || Auth::user()->entity_id == 1) {
            $lc = new LogConverter('recapture_invoice', 'view');
            $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed recapture invoice list')->save();
            
            $query = new RecaptureInvoice;

            $recapture_invoices_query = RecaptureInvoice::with('entity')
                    ->with('RecaptureItem.item')
                    ->with('transactions');

            // determine if they are OHFA or not
            if (Auth::user()->entity_id != 1) {
                $recapture_invoices_query->where('entity_id', '=', Auth::user()->entity_id);
                $where_entity_id = Auth::user()->entity_id;
                $where_entity_id_operator = '=';
            } else {
                $recapture_invoices_query->where('entity_id', '>', 0);
                $where_entity_id = 0;
                $where_entity_id_operator = '>';
            }

            // The sorting column
            $sortedBy = $request->query('invoices_sort_by');
            //$sortedBy=1;
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['recapture_invoices_sorted_by_query'=>$sortedBy]);
                $invoices_sorted_by_query = $request->session()->get('recapture_invoices_sorted_by_query');
            } elseif (!is_null($request->session()->get('recapture_invoices_sorted_by_query'))) {
                // use the session value
                $invoices_sorted_by_query = $request->session()->get('recapture_invoices_sorted_by_query');
            } else {
                // set the default
                session(['recapture_invoices_sorted_by_query'=>'12']);
                $invoices_sorted_by_query = $request->session()->get('recapture_invoices_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query
            if (!is_null($sortedBy)) {
                switch ($request->query('invoices_asc_desc')) {
                    case '1':
                        session(['recapture_invoices_asc_desc'=> 'desc']);
                        $invoicesAscDesc =  $request->session()->get('recapture_invoices_asc_desc');
                        session(['recapture_invoices_asc_desc_opposite' => "0"]);
                        $invoicesAscDescOpposite =  $request->session()->get('recapture_invoices_asc_desc_opposite');
                        break;
                    
                    default:
                        session(['recapture_invoices_asc_desc'=> 'asc']);
                        $invoicesAscDesc =  $request->session()->get('recapture_invoices_asc_desc');
                        session(['recapture_invoices_asc_desc_opposite' => '1']);
                        $invoicesAscDescOpposite = $request->session()->get('recapture_invoices_asc_desc_opposite');
                        break;
                }

                switch ($sortedBy) {
                    case '12':
                        # created_at
                        session(['recapture_invoices_sort_by' => 'recapture_invoices.created_at']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '2':
                        # invoice_id
                        session(['recapture_invoices_sort_by' => 'recapture_invoices.id']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '3':
                        # account_id
                        session(['recapture_invoices_sort_by' => 'recapture_invoices.account_id']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '4':
                        # program_id
                        session(['recapture_invoices_sort_by' =>'recapture_invoices.program_id']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '5':
                        # entity_id
                        session(['recapture_invoices_sort_by' =>'recapture_invoices.entity_id']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '6':
                        # total_parcels
                        session(['recapture_invoices_sort_by' => 'total_parcels']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '7':
                        # total_requested
                        session(['recapture_invoices_sort_by' => 'total_requested']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '8':
                        #  total_approved
                        session(['recapture_invoices_sort_by' => 'total_approved']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '9':
                        #  total_amount (invoiced)
                        session(['recapture_invoices_sort_by' => 'total_amount']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '10':
                        #  total_paid
                        session(['recapture_invoices_sort_by' => 'total_paid']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    case '11':
                        #  invoice_status_name
                        session(['recapture_invoices_sort_by' => 'invoice_status_name']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                    default:
                        # code...
                        session(['recapture_invoices_sort_by' => 'recapture_invoices.created_at']);
                        $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('recapture_invoices_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['recapture_invoices_sort_by' => 'recapture_invoices.created_at']);
                $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                session(['recapture_invoices_asc_desc' => 'asc']);
                $invoicesAscDesc = $request->session()->get('recapture_invoices_asc_desc');
                session(['recapture_invoices_asc_desc_opposite' => '1']);
                $invoicesAscDescOpposite = $request->session()->get('recapture_invoices_asc_desc_opposite');
            } else {
                // use values in the session
                $invoicessSortBy = $request->session()->get('recapture_invoices_sort_by');
                $invoicesAscDesc = $request->session()->get('recapture_invoices_asc_desc');
                $invoicesAscDescOpposite = $request->session()->get('recapture_invoices_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('invoices_program_filter'))) {
                //Update the session
                session(['recapture_invoices_program_filter' => $request->query('invoices_program_filter')]);
                $invoicesProgramFilter = $request->session()->get('recapture_invoices_program_filter');
                session(['recapture_invoices_program_filter_operator' => '=']);
                $invoicesProgramFilterOperator = $request->session()->get('recapture_invoices_program_filter_operator');
            } elseif (is_null($request->session()->get('recapture_invoices_program_filter')) || $request->query('invoices_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['recapture_invoices_program_filter' => '%%']);
                $invoicesProgramFilter = $request->session()->get('recapture_invoices_program_filter');
                session(['recapture_invoices_program_filter_operator' => 'LIKE']);
                $invoicesProgramFilterOperator = $request->session()->get('recapture_invoices_program_filter_operator');
            } else {
                // use values in the session
                $invoicesProgramFilter = $request->session()->get('recapture_invoices_program_filter');
                $invoicesProgramFilterOperator = $request->session()->get('recapture_invoices_program_filter_operator');
            }

            if (is_numeric($request->query('invoices_status_filter'))) {
                //Update the session
                session(['recapture_invoices_status_filter' => $request->query('invoices_status_filter')]);
                $invoicesStatusFilter = $request->session()->get('recapture_invoices_status_filter');
                session(['recapture_invoices_status_filter_operator' => '=']);
                $invoicesStatusFilterOperator = $request->session()->get('recapture_invoices_program_filter_operator');
            } elseif (is_null($request->session()->get('recapture_invoices_status_filter')) || $request->query('invoices_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['recapture_invoices_status_filter' => '%%']);
                $invoicesStatusFilter = $request->session()->get('recapture_invoices_status_filter');
                session(['recapture_invoices_status_filter_operator' => 'LIKE']);
                $invoicesStatusFilterOperator = $request->session()->get('recapture_invoices_status_filter_operator');
            } else {
                // use values in the session
                $invoicesStatusFilter = $request->session()->get('recapture_invoices_status_filter');
                $invoicesStatusFilterOperator = $request->session()->get('recapture_invoices_status_filter_operator');
            }

            // Insert other Filters here
            $currentUser = Auth::user();

            if ($invoicesProgramFilter && $invoicesProgramFilter != "%%") {
                $recapture_invoices_query->where("recapture_invoices.program_id", $invoicesProgramFilterOperator, $invoicesProgramFilter);
            }

            if ($invoicesStatusFilter && $invoicesStatusFilter != "%%") {
                $recapture_invoices_query->where("recapture_invoices.status_id", $invoicesStatusFilterOperator, $invoicesStatusFilter);
            }

            $recapture_invoices_query->where("recapture_invoices.entity_id", $where_entity_id_operator, $where_entity_id);

            if ($invoicessSortBy) {
                if ($invoicessSortBy == "invoice_status_name") {
                    $recapture_invoices_query->join('invoice_statuses', function ($join) use ($invoicessSortBy, $invoicesAscDesc) {
                        $join->on('invoice_statuses.id', '=', 'recapture_invoices.status_id');
                    });
                    $recapture_invoices_query->orderBy('invoice_statuses.'.$invoicessSortBy, $invoicesAscDesc);
                } else {
                    $recapture_invoices_query->orderBy($invoicessSortBy, $invoicesAscDesc);
                }
            }

            $recapture_invoices = $recapture_invoices_query->select('recapture_invoices.*')->get();

            foreach ($recapture_invoices as $invoice) {
                //fix status id if set to "Pending LB Approval"
                if ($invoice->status_id == 2) {
                    $invoice->update([
                        'status_id' => 3
                    ]);
                }
                $total = 0;
                $total_paid = 0;
                $invoice->total = $total;
                $total_paid = $total_paid + $invoice->transactions->sum('amount');
                $invoice->total_paid = $total_paid;
            }

            $programs = RecaptureInvoice::join('programs', 'recapture_invoices.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->get();
            $statuses = RecaptureInvoice::join('invoice_statuses', 'recapture_invoices.status_id', '=', 'invoice_statuses.id')->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')->get();

            return view('dashboard.recapture_invoice_list', compact('recapture_invoices', 'programs', 'statuses', 'currentUser', 'invoices_sorted_by_query', 'invoicesAscDesc', 'invoicesAscDescOpposite', 'invoicesProgramFilter', 'invoicesStatusFilter'));
        } else {
            return 'Sorry you do not have access to Recapture Invoice Listing page. Please try logging in again or contact your admin to request access.';
        }
    }

    public function getRecapturesFromInvoiceId(RecaptureInvoice $invoice)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to this resource.';
        }
        $recaptures = $invoice->RecaptureItem;

        $need_to_reload = 0;

        setlocale(LC_MONETARY, 'en_US');
        foreach ($recaptures as $recapture) {
            $recapture->load('program')->load('parcel');

            $recapture->invoiced_total = $recapture->amount;

            $recapture->created_at_m = date('m', strtotime($recapture->created_at));
            $recapture->created_at_d = date('d', strtotime($recapture->created_at));
            $recapture->created_at_Y = date('Y', strtotime($recapture->created_at));

            $recapture->total_formatted = money_format('%n', $recapture->amount);

            $recapture->expense_category_name = $recapture->expenseCategory->expense_category_name;

            $recapture->total = $recapture->amount;
        }

        return ['recaptures'=>$recaptures,'invoice_id'=>$invoice->id];
    }

    /**
     * Show recaptures belonging to a parcel
     *
     * @param  int $parcel_id
     * @return Response
     */

    public function breakoutViewRecapture(Parcel $parcel, $cost_item_id)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to the recapture.';
        }

        // is invoice paid?
        if ($parcel->associatedInvoice) {
            $invoice = ReimbursementInvoice::where('id', '=', $parcel->associatedInvoice->reimbursement_invoice_id)->first();
            if ($invoice->status_id != 6) {
                return 'Sorry the invoice associated with this parcel has not been paid.';
            }
        }

        // get cost item
        $cost_item = CostItem::where('id', '=', $cost_item_id)->first();

        return view('modals.new-recapture-from-breakouts', compact('parcel', 'cost_item'));
    }
    
    /**
     * Edit recaptures belonging to a parcel
     *
     * @param  int $parcel_id
     * @return Response
     */

    public function editRecapture(RecaptureItem $recapture)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to the recapture.';
        }

        return view('modals.edit-recapture', compact('recapture'));
    }

    /**
     * Show recaptures belonging to a parcel
     *
     * @param  int $parcel_id
     * @return Response
     */

    public function getRecapturesFromParcelId($parcel_id, $recapture = 'all', $format = null)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to the recapture.';
        }

        $parcel = Parcel::where('id', '=', $parcel_id)->first();

        if (!$parcel) {
            return 'Sorry this parcel cannot be found.';
        }
        
        $lc = new LogConverter('recapture', 'view');
        if (!is_null($recapture) && $recapture != 'all') {
            $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed recapture '.$recapture.' for parcel '.$parcel->id)->save();
        } else {
            $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed recapture for parcel '.$parcel->id)->save();
        }
        setlocale(LC_MONETARY, 'en_US');
        $parcel->with('state')->with('county')->with('recaptures');

        return view('parcels.recapture', compact('parcel'));
    }

    public function getUploadedDocuments(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            $output['message'] = "Sorry you do not have access to the disposition.";
            return $output;
        }
        
        if ($parcel) {
            // get all documents
            $documents = Document::where('parcel_id', '=', $parcel->id)->get();
            if ($documents) {
                $i = 0;
                $output = [];
                foreach ($documents as $document) {
                    if ($document->categories) {
                        $categories_decoded = json_decode($document->categories, true); // cats used by the doc
                    } else {
                        $categories_decoded = [];
                    }
                    if (in_array('38', $categories_decoded)) {
                        $output[$i]['filename'] = $document->filename;
                        $output[$i]['id'] = $document->id;
                        $output[$i]['comment'] = $document->comment;
                        $output[$i]['filelink'] = route('documents.downloadDocument', [$parcel->id, $document->id]);
                        $output[$i]['filedate'] = date('m/d/Y', strtotime($document->created_at));
                        $i++;
                    }
                }
                if (count($output) == 0) {
                    $output['message'] = "Displaying the documents.";
                }
            } else {
                $output['message'] = "No documents at this time.";
            }
            return $output;
        } else {
            $output['message'] = "Something went wrong.";
            return $output;
        }
    }

    public function uploadSupportingDocuments(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you cannot upload documents to recapture_items.';
        }

        if (app('env') == 'local') {
            app('debugbar')->disable();
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';
            $categories_json = json_encode(['38'], true); // 38 is "Disposition Supporting Documents"

            $user = Auth::user();

            foreach ($files as $file) {
                // Create filepath
                $folderpath = 'documents/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';
                
                // sanitize filename
                $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                // Create a record in documents table
                $document = new Document([
                    'user_id' => $user->id,
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
                $lc=new LogConverter('document', 'create');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                // store original file
                Storage::put($filepath, File::get($file));

                $uploadcount++;
            }

            // get current disposition and set progress
            $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->orderby('id', 'DESC')->first();
            guide_set_progress($recapture->id, 3, $status = 'completed', 1); // step 1 - uploaded documents

            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function uploadSupportingDocumentsComments(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry something went wrong.';
        }

        if (!$request->get('postvars')) {
            return 'Something went wrong';
        }

        // get document ids
        $documentids = explode(",", $request->get('postvars'));

        // get comment
        $comment = $request->get('comment');

        if (is_array($documentids) && count($documentids)) {
            foreach ($documentids as $documentid) {
                $document = Document::find($documentid);
                $document->update([
                    'comment' => $comment,
                ]);
                $lc = new LogConverter('document', 'comment');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' added comment to document ')->save();
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function approveUploadSignature(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you cannot upload documents to recapture_items.';
        }

        if (app('env') == 'local') {
            app('debugbar')->disable();
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';
            $categories_json = json_encode(['37'], true); // 37 is "Disposition signature"

            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            foreach ($files as $file) {
                // Create filepath
                $folderpath = 'documents/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';
                
                // sanitize filename
                $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                // Create a record in documents table
                $document = new Document([
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'categories' => $categories_json,
                    'filename' => $original_filename
                ]);

                $document->save();

                // automatically approve
                $document->approve_categories([37]);

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
                $lc=new LogConverter('document', 'create');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                // store original file
                Storage::put($filepath, File::get($file));

                $uploadcount++;
            }


            $approval_process = $this->approveDisposition($parcel, $approvers, $document_ids);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function approveUploadSignatureComments(Parcel $parcel, Request $request)
    {
        if (!$request->get('postvars')) {
            return 'Something went wrong';
        }

        // get document ids
        $documentids = explode(",", $request->get('postvars'));

        // get comment
        $comment = $request->get('comment');

        if (is_array($documentids) && count($documentids)) {
            foreach ($documentids as $documentid) {
                $document = Document::find($documentid);
                $document->update([
                    'comment' => $comment,
                ]);
                $lc = new LogConverter('document', 'comment');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' added comment to document ')->save();
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function approveHFAUploadSignature(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-recapture') && !Gate::allows('hfa-release-disposition') && Auth::user()->entity_id != 1) {
            return 'Sorry something went wrong.';
        }

        if (app('env') == 'local') {
            app('debugbar')->disable();
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';
            $categories_json = json_encode(['37'], true); // 37 is "Disposition signature"

            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            foreach ($files as $file) {
                // Create filepath
                $folderpath = 'documents/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';
                
                // sanitize filename
                $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                // Create a record in documents table
                $document = new Document([
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'categories' => $categories_json,
                    'filename' => $original_filename
                ]);

                $document->save();

                // automatically approve
                $document->approve_categories([37]);

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
                $lc=new LogConverter('document', 'create');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                // store original file
                Storage::put($filepath, File::get($file));

                $uploadcount++;
            }

            $approval_process = $this->approveDisposition($parcel, $approvers, $document_ids, 1);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    /**
     * Calculate total recapture owed to HFA
     *
     * @param  int parcel, float income, float cost
     * @return payback amount
     */
    public function computeRecaptureOwed(Dispositions $recapture, Request $request)
    {
        $debug = [];

        $income = $request->get('income');
        $cost = $request->get('cost');

        $parcel = $recapture->parcel;

        // get total maintenance
        $maintenance_array = $this->getUnusedMaintenance($parcel);
        if ($maintenance_array['unused'] == -1) {
            return "The invoice has not been paid yet.";
        }

        // get demolition cost
        $demolition = $this->getDemolitionTotal($parcel);

        // transaction cost
        $rules = ProgramRule::first(['imputed_cost_per_parcel','maintenance_max','maintenance_recap_pro_rate']);
        $imputed_cost_per_parcel = $rules->imputed_cost_per_parcel; //200
        $maintenance_max = $rules->maintenance_max; //1200
        $maintenance_recap_pro_rate = $rules->maintenance_recap_pro_rate; //36
        
        if ($recapture->hfa_calc_months_prepaid != null) {
            $maintenance_recap_pro_rate = $recapture->hfa_calc_months_prepaid;
        }

        $rule_min_cost = $imputed_cost_per_parcel;
        if ($cost > $rule_min_cost) {
            $transaction_cost = $cost;
        } else {
            $transaction_cost = $rule_min_cost;
        }

        // compute eligible property income (income - cost, cost >= 200)
        $eligible_income = $income - $transaction_cost;
        if ($eligible_income < 0) {
            $eligible_income = 0;
        }

        // compute owed amount
        if ($eligible_income > $demolition) {
            $payback = $demolition + $maintenance_array['unused'];
        } else {
            $payback = $eligible_income + $maintenance_array['unused'];
        }

        return $payback;
    }

    public function getMaintenanceTotal(Parcel $parcel)
    {
        $maintenance_total = InvoiceItem::where('parcel_id', '=', $parcel->id)
                            ->where('expense_category_id', '=', 6)
                            ->sum('amount');

        $rules = ProgramRule::first(['maintenance_max']); //1200
        $maintenance_max = $rules->maintenance_max; //1200
        if ($maintenance_total > $maintenance_max && $maintenance_max != 0) {
            $maintenance_total = $maintenance_max;
        }
        
        return $maintenance_total;
    }

    public function getDemolitionTotal(Parcel $parcel)
    {
        $demolition = InvoiceItem::where('parcel_id', '=', $parcel->id)
                            ->where('expense_category_id', '!=', 6)
                            ->sum('amount');

        $rules = ProgramRule::first(['demolition_max']); //1200
        $demolition_max = $rules->demolition_max; //1200
        if ($demolition > $demolition_max && $demolition_max != 0) {
            $demolition = $demolition_max;
        }
        $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();
        if (!is_null($recapture->hfa_calc_demo_cost)) {
            return $recapture->hfa_calc_demo_cost;
        } else {
            return $demolition;
        }
    }

    public function getUnusedMaintenance(Parcel $parcel)
    {
        // calculate number of unused maintenance month based on 36 months
        $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

        $rules = ProgramRule::first(['imputed_cost_per_parcel', 'maintenance_recap_pro_rate', 'maintenance_max']); //200
        $imputed_cost_per_parcel = $rules->imputed_cost_per_parcel;

        $maintenance_recap_pro_rate = $rules->maintenance_recap_pro_rate; //36
        if ($recapture->hfa_calc_months_prepaid != null) {
            $maintenance_recap_pro_rate = $recapture->hfa_calc_months_prepaid;
        }

        $maintenance_max = $rules->maintenance_max;

        // get total maintenance
        $maintenance_total = $this->getMaintenanceTotal($parcel);
        if (!is_null($recapture->hfa_calc_maintenance_total)) {
            $maintenance_total_hfa = $recapture->hfa_calc_maintenance_total;
        } else {
            $maintenance_total_hfa = $maintenance_total;
        }

        if ($maintenance_total_hfa > $maintenance_max && $maintenance_max != 0) {
            $maintenance_total_hfa = $maintenance_max;
        }

        $invoiceid = ParcelsToReimbursementInvoice::where('parcel_id', '=', $parcel->id)->first();
        $invoice = ReimbursementInvoice::where('id', '=', $invoiceid->reimbursement_invoice_id)->first();
  
        if ($invoice->status_id != 6) {
            return -1; // has not been paid
        } else {
            // when was the transaction paid?
            $transaction = Transaction::where('type_id', '=', 1)
                            ->where('link_to_type_id', '=', $invoiceid->reimbursement_invoice_id)
                            ->where('status_id', '=', 2)
                            ->orderBy('id', 'desc')
                            ->first();

            if ($transaction) {
                $invoice_payment_date = $transaction->date_cleared;
                if ($invoice_payment_date == "0000-00-00") {
                    return -2;
                }
            } else {
                return -1; // has not been paid
            }
        }


        $recapture_date = $recapture->created_at;
        if ($recapture->created_at) {
            if ($recapture->created_at->toDateTimeString() == "0000-00-00 00:00:00" || $recapture->created_at->toDateTimeString() == "-0001-11-30 00:00:00") {
                $recapture->update([
                    'created_at' => Carbon\Carbon::today()->toDateTimeString()
                ]);
            }
        } elseif ($recapture->updated_at) {
            $recapture->update([
                'created_at' => $recapture->updated_at
            ]);
        }
         

        $ts1 = strtotime($invoice_payment_date);
        $ts2 = strtotime($recapture_date);
        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);
        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);
        $months = (($year2 - $year1) * 12) + ($month2 - $month1) +1;
        if (!is_null($recapture->hfa_calc_months)) {
            $months_hfa = $recapture->hfa_calc_months;
        } else {
            $months_hfa = $months;
        }

        if ($months > $maintenance_recap_pro_rate && $maintenance_recap_pro_rate != 0) {
            $months = $maintenance_recap_pro_rate;
        }
        if ($months_hfa > $maintenance_recap_pro_rate && $maintenance_recap_pro_rate != 0) {
            $months_hfa = $maintenance_recap_pro_rate;
        }

        // calculate unused maintenance
        $maintenance['unused'] = $maintenance_total - ($months * $maintenance_total / $maintenance_recap_pro_rate);
        $maintenance['months'] = $months;
        $maintenance['monthly_rate'] = number_format($maintenance_total / $maintenance_recap_pro_rate, 2, '.', '');
        $maintenance['disposition_date'] = $recapture_date;
        $maintenance['invoice_payment_date'] = $invoice_payment_date;

        $maintenance['hfa_unused'] = $maintenance_total_hfa - ($months_hfa * $maintenance_total_hfa / $maintenance_recap_pro_rate);
        $maintenance['hfa_months'] = $months_hfa;
        $maintenance['hfa_monthly_rate'] = number_format($maintenance_total_hfa / $maintenance_recap_pro_rate, 2, '.', '');
        $maintenance['hfa_disposition_date'] = $recapture_date;
        $maintenance['hfa_invoice_payment_date'] = $invoice_payment_date;
        $maintenance['months_prepaid'] = $maintenance_recap_pro_rate;

        return $maintenance;
    }

    

    /**
     * Create approvers
     *
     * @param  $parcel, $request
     * @return Response
     */
    public function addApprover(RecaptureInvoice $invoice, Request $request)
    {
        if (!Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $approver_id = $request->get('user_id');
            if (!ApprovalRequest::where('approval_type_id', '=', 5)
                        ->where('link_type_id', '=', $invoice->id)
                        ->where('user_id', '=', $approver_id)
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 5,
                    "link_type_id" => $invoice->id,
                    "user_id" => $approver_id
                ]);
                $newApprovalRequest->save();
                $lc = new LogConverter('recapture_invoices', 'add.approver');
                $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'added an approver.')->save();

                // send emails
                try {
                    $current_recipient = User::where('id', '=', $approver_id)->get()->first();
                    $emailNotification = new RecaptureApproverNotification($current_recipient, $invoice->id);
                    \Mail::to($current_recipient->email)->send($emailNotification);
                    //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                }

                $data['message'] = 'The approver was added.';
                return $data;
            } else {
                $data['message'] = 'Something went wrong.';
                return $data;
            }
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    // public function addHFAApprover(Parcel $parcel, Request $request)
    // {
    //     if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
    //         $output['message'] = 'Something went wrong.';
    //         return $output;
    //     }

    //     // get disposition for parcel
    //     $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

    //     if ($recapture) {
    //         $approver_id = $request->get('user_id');
    //         if (!ApprovalRequest::where('approval_type_id', '=', 11)
    //                     ->where('link_type_id', '=', $recapture->id)
    //                     ->where('user_id', '=', $approver_id)
    //                     ->count()) {
    //             $newApprovalRequest = new  ApprovalRequest([
    //                 "approval_type_id" => 11,
    //                 "link_type_id" => $recapture->id,
    //                 "user_id" => $approver_id
    //             ]);
    //             $newApprovalRequest->save();
    //             $lc = new LogConverter('disposition', 'add.hfa.approver');
    //             $lc->setFrom(Auth::user())->setTo($recapture)->setDesc(Auth::user()->email . 'added a HFA approver.')->save();

    //             $data['message'] = 'The approver was added.';
    //             return $data;
    //         } else {
    //             $data['message'] = 'Something went wrong.';
    //             return $data;
    //         }
    //     } else {
    //         $data['message'] = 'Something went wrong.';
    //         return $data;
    //     }
    // }

    /**
     * Remove approvers
     *
     * @param  $parcel, $request
     * @return Response
     */
    public function removeApprover(RecaptureInvoice $invoice, Request $request)
    {
        if ($invoice) {
            $approver_id = $request->get('id');
            $approver = ApprovalRequest::where('approval_type_id', '=', 5)
                            ->where('link_type_id', '=', $invoice->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $approver->delete();
 
            $data['message'] = '';
            $data['id'] = $request->get('id');
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    // public function removeHFAApprover(Parcel $parcel, Request $request)
    // {
    //     if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
    //         $output['message'] = 'Something went wrong.';
    //         return $output;
    //     }

    //     // get disposition for parcel
    //     $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

    //     if ($recapture) {
    //         $approver_id = $request->get('id');
    //         $approver = ApprovalRequest::where('approval_type_id', '=', 11)
    //                         ->where('link_type_id', '=', $recapture->id)
    //                         ->where('user_id', '=', $approver_id)
    //                         ->first();
    //         $approver->delete();
 
    //         $data['message'] = '';
    //         $data['id'] = $request->get('id');
    //         return $data;
    //     } else {
    //         $data['message'] = 'Something went wrong.';
    //         $data['id'] = null;
    //         return $data;
    //     }
    // }

    public function approve(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

        if ($recapture) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 1)
                            ->where('link_type_id', '=', $recapture->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 1
                    ]);
            $action->save();
 
            $data['message'] = 'Your approval was recorded.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function approveHFA(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

        if ($recapture) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 11)
                            ->where('link_type_id', '=', $recapture->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 1
                    ]);
            $action->save();
 
            $data['message'] = 'Your approval was recorded.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function decline(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

        if ($recapture) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 1)
                            ->where('link_type_id', '=', $recapture->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();
 
            $data['message'] = 'This document has been declined.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function declineHFA(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $recapture = RecaptureItem::where('parcel_id', '=', $parcel->id)->first();

        if ($recapture) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 11)
                            ->where('link_type_id', '=', $recapture->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();
 
            $data['message'] = 'This document has been declined.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function viewInvoice(RecaptureInvoice $invoice)
    {
        if (!Gate::allows('view-disposition')) {
            return 'Sorry you do not have access to the invoice.';
        }

        setlocale(LC_MONETARY, 'en_US');

        $invoice->load('status')
                ->load('RecaptureItem.parcel')
                ->load('entity')
                ->load('account')
                ->load('notes')
                ->load('account.transactions')
                ->load('program')
                ->load('transactions');

        $stat = [];
        $stat = $stat + $invoice->account->statsParcels->toArray()[0]
                        + $invoice->account->statsTransactions->toArray()[0]
                        + $invoice->account->statsCostItems->toArray()[0]
                        + $invoice->account->statsRequestItems->toArray()[0]
                        + $invoice->account->statsPoItems->toArray()[0]
                        + $invoice->account->statsInvoiceItems->toArray()[0];


        // get dispositions
        $total = 0;
        $legacy = 1;
        $display_request_button = 0; // when at least one disposition has no release nor release request
        $display_release_button = 0; // when at least one disposition has no release

        foreach ($invoice->RecaptureItem as $recapture) {
            $recapture->total = $recapture->amount;
            $recapture->total_formatted = money_format('%n', $recapture->amount);
            $total = $total + $recapture->total;
            if ($recapture->sf_parcel_id === null) {
                $legacy = 0;
            }

            // perform_all_disposition_checks($recapture);
        }
        $total_unformatted = $total;
        $total = money_format('%n', $total);

        // transactions
        $sum_transactions = 0;
        if ($invoice->transactions) {
            foreach ($invoice->transactions as $transaction) {
                if ($transaction->credit_debit == 'c') {
                    $sum_transactions = $sum_transactions - $transaction->amount;
                } else {
                    $sum_transactions = $sum_transactions + $transaction->amount;
                }
            }
        }
        $total_unformatted = round($total_unformatted, 2);
        $sum_transactions = round($sum_transactions, 2);
        $balance = round($total_unformatted + $sum_transactions, 2);

        // get notes
        $owners_array = [];
        foreach ($invoice->notes as $note) {
            // create initials
            $words = explode(" ", $note->owner->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $note->initials = $initials;

            if (!array_key_exists($note->owner->id, $owners_array)) {
                $owners_array[$note->owner->id]['initials'] = $initials;
                $owners_array[$note->owner->id]['name'] = $note->owner->name;
                $owners_array[$note->owner->id]['color'] = $note->owner->badge_color;
                $owners_array[$note->owner->id]['id'] = $note->owner->id;
            }
        }

        $lc = new LogConverter('recapture_invoices', 'view');
        $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'Viewed disposition invoice')->save();
        
        $nip = Entity::where('id', 1)->with('state')->with('user')->first();

        //RecaptureInvoiceNote
        $recaptures = $invoice->RecaptureItem;

        $isApprover = 0;

        // get approvers (type id 5 is for recapture invoices)
        $recaptureInvoiceApprovers = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 27)
                                        ->where('users.active', 1)
                                        ->select('users.id', 'users.name')
                                        ->get();

        $added_approvers = ApprovalRequest::where('approval_type_id', '=', 5)
                                ->where('link_type_id', '=', $invoice->id)
                                ->pluck('user_id as id');

        $pending_approvers = [];

        if (count($added_approvers) == 0 && count($recaptureInvoiceApprovers) > 0) {
            foreach ($recaptureInvoiceApprovers as $recaptureInvoiceApprover) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 5,
                    "link_type_id" => $invoice->id,
                    "user_id" => $recaptureInvoiceApprover->id
                ]);
                $newApprovalRequest->save();
            }
        } elseif (count($recaptureInvoiceApprovers) > 0) {
            // list all approvers who are not already in the approval_request table

            $pending_approvers = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 27)
                                    ->where('users.active', 1)
                                    ->whereNotIn('id', $added_approvers)
                                    ->select('users.id', 'users.name')
                                    ->get();
        }

        // get approvals
        $approval_status = guide_approval_status(5, $invoice->id);
        $isApprover     = $approval_status['is_approver'];
        $hasApprovals   = $approval_status['has_approvals'];
        $isApproved     = $approval_status['is_approved'];
        $approvals      = $approval_status['approvals'];
        $isDeclined     = $approval_status['is_declined'];

        $isReadyForPayment = 0;

        if ($isApproved) {
            $isReadyForPayment = 1;
        } elseif ($invoice->status_id != 6 && $invoice->status_id != 3 && !$legacy) {
            if ($invoice->status_id == 7) {
                //was previously approved
                $invoice->update(['status_id'=>3]); // Pending HFA Approval
                $invoice->status_id = 3;
            } else {
                $invoice->update(['status_id'=>1]); // Draft
                $invoice->status_id = 1;
            }
            
            $invoice->load('status');
        }

        // if invoice is fully paid, make sure all recapture in it is marked as such
        if ($invoice->status_id == 6) {
            // foreach ($invoice->RecaptureItem as $recapture) {
            //     // make a copy of the recapture because we extended the collection with additional fields not in the model
            //     $updated_recapture = RecaptureItem::where('id', '=', $recapture->id);
            //     $updated_recapture->update(['status_id'=>6]); // Paid
            //     $recapture->status_id = 6;
            // }
            // make sure the invoice itself is marked as paid
            \App\Models\RecaptureInvoice::where('id', $invoice->id)->update(['paid' => 1]);
        }

        if (($isReadyForPayment === 1 || $legacy) && $balance > 0) {
            // after invoice was sent to fiscal agent, its status is 8 (submitted to fiscal agent)
            if ($invoice->status_id != 8) {
                $invoice->update(['status_id'=>7]); // Approved
                $invoice->status_id = 7;
                $invoice->load('status');
                // update recaptures' status
                // foreach ($invoice->RecaptureItem as $recapture) {
                //     // make a copy of the recapture because we extended the collection with additional fields not in the model
                //     $updated_recapture = RecaptureItem::where('id', '=', $recapture->id);
                //     $updated_recapture->update(['status_id'=>4]); // Pending Payment
                //     $recapture->status_id = 4;
                // }

                $invoice->load('RecaptureItem');
            }
        } elseif ($legacy && count($invoice->transactions) && $balance > 0) {
            $invoice->update(['status_id'=>4,'paid'=>null]); // Pending payment
            $invoice->status_id = 4;
            $invoice->paid = null;
            $invoice->load('status');
        } elseif (($isReadyForPayment === 1 || $legacy) && $balance < .01 && count($invoice->RecaptureItem) > 0) {
            // for each disposition, change the status to Paid as well
            // foreach ($invoice->RecaptureItem as $recapture) {
            //     // make a copy of the disposition because we extended the collection with additional fields not in the model
            //     $updated_recapture = RecaptureItem::where('id', '=', $recapture->id);
            //     $updated_recapture->update(['status_id'=>6]); // Paid
            //     $recapture->status_id = 6;
            // }

            $invoice->update(['status_id'=>6, 'paid' => 1]); // Paid
            $invoice->status_id = 6;
            $invoice->paid = 1;
            $invoice->load('status');
            $invoice->load('RecaptureItem'); // reload to make sure they show latest status
        }
        
        // refresh invoice data to use current status
        //$invoice = RecaptureInvoice::find($invoice->id); //if we reload, we will loose some of the work done above (formatted total, etc)

        return view('pages.recapture_invoice', compact('invoice', 'notes', 'balance', 'nip', 'hasApprovals', 'isApprover', 'isDeclined', 'approvals', 'pending_approvers', 'isApproved', 'isReadyForPayment', 'total', 'balance', 'stat', 'legacy', 'display_request_button'));
    }

    public function submitForApproval(RecaptureInvoice $invoice, Request $request)
    {
        if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            // check that the invoice is status_id == 1 first, then change the status to 3 (Pending HFA approval)
            if ($invoice->status_id == 1) {
                $invoice->update([
                    'status_id' => 3
                ]);

                $lc = new LogConverter('recapture_invoices', 'submitted for approval');
                $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' submitted the disposition invoice '.$invoice->id.' for approval')->save();

                $output['message'] = "The disposition invoice has been submitted for approval.";
            } else {
                $output['message'] = "Nothing to do here.";
            }
        } else {
            $output['message'] = "Something is wrong, I couldn't find a valid invoice.";
        }
        return $output;
    }

    public function newNoteEntry(RecaptureInvoice $invoice, Request $request)
    {
        if ($invoice && $request->get('invoice-note')) {
            $user = Auth::user();

            $note = new RecaptureInvoiceNote([
                'owner_id' => $user->id,
                'recapture_invoice_id' => $invoice->id,
                'note' => $request->get('invoice-note')
            ]);
            $note->save();
            $lc = new LogConverter('recapture_invoices', 'addnote');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' added note to disposition invoice')->save();

            $words = explode(" ", $user->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $note->initials = $initials;
            $note->name = $user->name;
            $note->badge_color = $user->badge_color;
            $note->created_at_formatted = date('m/d/Y', strtotime($note->created_at));

            return $note;
        } else {
            return "Something went wrong. We couldn't save your note.";
        }
    }

    public function sendForPayment(RecaptureInvoice $invoice)
    {
        if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $invoice->update([
                'status_id' => 4 // pending payment
            ]);

            $lc = new LogConverter('recapture_invoices', 'payment pending');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc('Invoice is pending payment.')->save();

            // Send email notification to LB
            $fiscalAgents = User::where('entity_id', '=', 1)
                                ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                ->where('users_roles.role_id', '=', 21)
                                ->where('active', '=', 1)
                                ->select('id')
                                ->get();
            $message_recipients_array = $fiscalAgents->toArray();
            try {
                foreach ($message_recipients_array as $userToNotify) {
                    $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                    $emailNotification = new EmailNotificationRecapturePaymentRequested($userToNotify, $invoice->id);
                    \Mail::to($current_recipient->email)->send($emailNotification);
                    //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                dd($ex->getMessage());
            }

            $data['message'] = 'The invoice was sent to a fiscal agent!';
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    // public function addHFAApproverToInvoice(RecaptureInvoice $invoice, Request $request)
    // {
    //     if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
    //         $output['message'] = 'Something went wrong.';
    //         return $output;
    //     }

    //     if ($invoice) {
    //         $approver_id = $request->get('user_id');
    //         if (!ApprovalRequest::where('approval_type_id', '=', 12)
    //                     ->where('link_type_id', '=', $invoice->id)
    //                     ->where('user_id', '=', $approver_id)
    //                     ->count()) {
    //             $newApprovalRequest = new  ApprovalRequest([
    //                 "approval_type_id" => 12,
    //                 "link_type_id" => $invoice->id,
    //                 "user_id" => $approver_id
    //             ]);
    //             $newApprovalRequest->save();
    //             $lc = new LogConverter('recapture_invoices', 'add.approver');
    //             $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'added an approver.')->save();

    //             $data['message'] = 'The approver was added.';
    //             return $data;
    //         } else {
    //             $data['message'] = 'Something went wrong.';
    //             return $data;
    //         }
    //     } else {
    //         $data['message'] = 'Something went wrong.';
    //         return $data;
    //     }
    // }

    public function approveInvoice(RecaptureInvoice $invoice, $approvers = null, $document_ids = null, $approval_type = 5)
    {
        if ((!Auth::user()->isHFADispositionApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            // it is possible that a HFA admin uploads a signature file for multiple LB users
            // if current user is HFA admin, make sure that person is added as the approver
            // in the records
            if (Auth::user()->isHFAAdmin()) {
                // create an approval request for HFA user
                if (!ApprovalRequest::where('approval_type_id', '=', $approval_type)
                            ->where('link_type_id', '=', $invoice->id)
                            ->where('user_id', '=', Auth::user()->id)
                            ->count()) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => $approval_type,
                        "link_type_id" => $invoice->id,
                        "user_id" => Auth::user()->id
                    ]);
                    $newApprovalRequest->save();
                }
            }

            // check if multiple people need to record approvals
            if (count($approvers) > 0) {
                if ($document_ids !== null) {
                    $documents = explode(",", $document_ids);
                } else {
                    $documents = [];
                }
                $documents_json = json_encode($documents, true);

                foreach ($approvers as $approver_id) {
                    $approver = ApprovalRequest::where('approval_type_id', '=', $approval_type)
                                ->where('link_type_id', '=', $invoice->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                    if (count($approver)) {
                        $action = new ApprovalAction([
                                'approval_request_id' => $approver->id,
                                'approval_action_type_id' => 5, //by proxy
                                'documents' => $documents_json
                            ]);
                        $action->save();
             
                        $lc = new LogConverter('recapture_invoices', 'approval by proxy');
                        $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'approved the invoice for '.$approver->name)->save();
                    }
                }
                $data['message'] = 'This invoice was approved.';
                $data['id'] = $approver_id;
                return $data;
            } else {
                $approver_id = Auth::user()->id;
                $approver = ApprovalRequest::where('approval_type_id', '=', $approval_type)
                                ->where('link_type_id', '=', $invoice->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                if (count($approver)) {
                    $action = new ApprovalAction([
                            'approval_request_id' => $approver->id,
                            'approval_action_type_id' => 1
                        ]);
                    $action->save();
         
                    $lc = new LogConverter('recapture_invoices', 'approval');
                    $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'approved the recapture invoice.')->save();

                    $data['message'] = 'Your invoice was approved.';
                    $data['id'] = $approver_id;
                    return $data;
                } else {
                    $data['message'] = 'Something went wrong.';
                    $data['id'] = null;
                }
            }
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    // this is to record decline action during approval process
    public function declineInvoice(RecaptureInvoice $invoice, Request $request)
    {
        // check user belongs to invoice entity
        if ((!Auth::user()->isHFADispositionApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $approver_id = Auth::user()->id;
            if ($request->get('approval_type') !== null) {
                $approval_type = $request->get('approval_type');
            } else {
                $approval_type = 5;
            }
            
            $approver = ApprovalRequest::where('approval_type_id', '=', $approval_type)
                            ->where('link_type_id', '=', $invoice->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();


            $lc = new LogConverter('recapture_invoices', 'decline');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' declined the invoice.')->save();


            $data['message'] = 'This invoice has been declined.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function approveInvoiceUploadSignature(RecaptureInvoice $invoice, Request $request)
    {
        if (app('env') == 'local') {
            app('debugbar')->disable();
        }
        
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';

            $categories_json = json_encode(['49'], true);
                       
            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            // get parcels from req $req->parcels
            foreach ($invoice->RecaptureItem as $recapture) {
                foreach ($files as $file) {
                    // Create filepath
                    $folderpath = 'documents/entity_'. $recapture->entity_id . '/program_' . $recapture->program_id . '/recapture_' . $recapture->id . '/';
                    
                    // sanitize filename
                    $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                    $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                    // Create a record in documents table
                    $document = new Document([
                        'user_id' => $user->id,
                        'parcel_id' => $recapture->parcel_id,
                        'categories' => $categories_json,
                        'filename' => $original_filename
                    ]);

                    $document->save();

                    // automatically approve
                    $document->approve_categories([49]);

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
                    $lc=new LogConverter('document', 'create');
                    $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                    // store original file
                    Storage::put($filepath, File::get($file));

                    $uploadcount++;
                }
            }

            if ($request->get('approvaltype') !== null) {
                $approval_type = $request->get('approvaltype');
            } else {
                $approval_type = 5;
            }
            $approval_process = $this->approveInvoice($invoice, $approvers, $document_ids, $approval_type);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function approveInvoiceUploadSignatureComments(RecaptureInvoice $invoice, Request $request)
    {
        if (!$request->get('postvars')) {
            return 'Something went wrong';
        }

        // get document ids
        $documentids = explode(",", $request->get('postvars'));

        // get comment
        $comment = $request->get('comment');

        if (is_array($documentids) && count($documentids)) {
            foreach ($documentids as $documentid) {
                $document = Document::find($documentid);
                $document->update([
                    'comment' => $comment,
                ]);
                $lc = new LogConverter('document', 'comment');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' added comment to document ')->save();
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function saveRecapture(CostItem $costitem, Request $request)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to this resource.';
        }

        // prepare fields
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        if (!isset($forminputs['amount'])) {
            $forminputs['amount'] = null;
        }
        if (!isset($forminputs['description'])) {
            $forminputs['description'] = null;
        }

        // look for an existing recapture invoice (draft) for this program
        $current_recapture_invoice = RecaptureInvoice::where('entity_id', '=', $costitem->entity_id)
                                ->where('account_id', '=', $costitem->account_id)
                                ->where('program_id', '=', $costitem->program_id)
                                ->where('active', '=', 1)
                                ->where('status_id', '=', 1)
                                ->first();

        // if no recapture invoice exists, create one
        if (!$current_recapture_invoice) {
            $current_recapture_invoice = new RecaptureInvoice([
                            'entity_id' => $costitem->entity_id,
                            'program_id' => $costitem->program_id,
                            'account_id' => $costitem->account_id,
                            'status_id' => 1,
                            'active' => 1
            ]);
            $current_recapture_invoice->save();

            $lc = new LogConverter('recapture_invoice', 'create');
            $lc->setFrom(Auth::user())->setTo($current_recapture_invoice)->setDesc(Auth::user()->email . 'Created a new recapture invoice draft')->save();
        }

        // create the new recapture item, add to invoice
        $recapture_item = new RecaptureItem([
                            'breakout_type' => $costitem->breakout_type,
                            'recapture_invoice_id' => $current_recapture_invoice->id,
                            'parcel_id' => $costitem->parcel_id,
                            'program_id' => $costitem->program_id,
                            'entity_id' => $costitem->entity_id,
                            'account_id' => $costitem->account_id,
                            'expense_category_id' => $costitem->expense_category_id,
                            'amount' => $forminputs['amount'],
                            'description' => $forminputs['description']
                        ]);
        $recapture_item->save();

        return 1;
    }

    public function updateRecapture(RecaptureItem $recapture, Request $request)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to this resource.';
        }

        // prepare fields
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        if (!isset($forminputs['amount'])) {
            $forminputs['amount'] = null;
        }
        if (!isset($forminputs['description'])) {
            $forminputs['description'] = null;
        }

        if ($recapture) {
            $recapture->update([
                'amount' => $forminputs['amount'],
                'description' => $forminputs['description']
            ]);
            return 1;
        } else {
            return "I couldn't find this recapture.";
        }
    }

    public function deleteRecapture(RecaptureItem $recapture)
    {
        if (!Gate::allows('view-recapture') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to this resource.';
        }

        if ($recapture) {
            $lc = new LogConverter('recapture_item', 'delete');
            $lc->setFrom(Auth::user())->setTo($recapture)->setDesc(Auth::user()->email . 'deleted a recapture.')->save();

            $recapture->delete();
        }

        $output['message'] = "This recapture has been deleted!";

        return $output;
    }
}

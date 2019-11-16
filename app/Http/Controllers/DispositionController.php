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
// use App\LogConverter;
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
use App\Models\DispositionInvoice;
use App\Models\Mail\ApproverNotification;
use App\Models\Mail\EmailNotificationDispositionReleaseRequested;
use App\Models\Mail\EmailNotificationDispositionPaymentRequested;
use App\Models\Mail\EmailNotificationDispositionReview;
use App\Models\Mail\DispositionApprovedNotification;
use DateTime;
use App\Models\DispositionInvoiceNote;
use App\Models\DispositionItems;
use App\Models\GuideStep;
use App\Models\GuideProgress;

class DispositionController extends Controller
{
    /*
    * getDispositionsFromParcelId
    * getDispositionsFromInvoiceId
    * getDispositionInvoice
    * computeDisposition
    */

    public function __construct(Request $request)
    {
        // $this->middleware('auth');
        // for testing only! //////////////////////////////////////////////////////////
        //Auth::onceUsingId(3);
    }

    public function dispositionList(Request $request)
    {
        if (Gate::allows('view-disposition')  || Auth::user()->entity_id == 1) {
            // $lc = new LogConverter('dispositionlist', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . 'viewed dispositionlist')->save();
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

            // Quick check for legacy records to make sure that all records have status id
            if (Disposition::where('status_id', '=', null)->orwhere('status_id', '>', 7)->count()) {
                $dispositions_to_update = Disposition::where('status_id', '=', null)->orwhere('status_id', '>', 7)->get();
                foreach ($dispositions_to_update as $disposition) {
                    $disposition->update([
                        'status_id' => 1,
                    ]);
                }
            }

            /// The sorting column
            $sortedBy = $request->query('dispositions_sort_by');
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['dispositions_sorted_by_query'=>$sortedBy]);
                $dispositions_sorted_by_query = $request->session()->get('dispositions_sorted_by_query');
            } elseif (!is_null($request->session()->get('dispositions_sorted_by_query'))) {
                // use the session value
                $dispositions_sorted_by_query = $request->session()->get('dispositions_sorted_by_query');
            } else {
                // set the default
                session(['dispositions_sorted_by_query'=>'1']);
                $dispositions_sorted_by_query = $request->session()->get('dispositions_sorted_by_query');
            }

            /// If a new sort has been provided
            // Rebuild the query

            if (!is_null($sortedBy)) {
                switch ($request->query('dispositions_asc_desc')) {
                    case '1':
                        # code...
                        session(['dispositions_asc_desc'=> 'desc']);
                        $dispositionsAscDesc =  $request->session()->get('dispositions_asc_desc');
                        session(['dispositions_asc_desc_opposite' => ""]);
                        $dispositionsAscDescOpposite =  $request->session()->get('dispositions_asc_desc_opposite');
                        break;

                    default:
                        session(['dispositions_asc_desc'=> 'asc']);
                        $dispositionsAscDesc =  $request->session()->get('dispositions_asc_desc');
                        session(['dispositions_asc_desc_opposite' => 1]);
                        $dispositionsAscDescOpposite = $request->session()->get('dispositions_asc_desc_opposite');
                        break;
                }
                switch ($sortedBy) {
                    case '1':
                        # created_at
                        session(['dispositions_sort_by' => 'dispositions.created_at']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    case '2':
                        # request_id
                        session(['dispositions_sort_by' => 'dispositions.id']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    case '3':
                        # account_id
                        session(['dispositions_sort_by' => 'dispositions.account_id']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    case '4':
                        # program_id
                        session(['dispositions_sort_by' =>'dispositions.program_id']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    case '5':
                        # entity_id
                        session(['dispositions_sort_by' =>'dispositions.entity_id']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    case '6':
                        # total_parcels
                        session(['dispositions_sort_by' => 'pid']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    // case '9':
                    //     #  total_amount
                    //     session(['dispositions_sort_by' => 'total_invoiced']);
                    //     $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                    //     break;
                    // case '12':
                    //     #  total_paid
                    //     session(['dispositions_sort_by' => 'total_paid']);
                    //     $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                    //     break;
                    case '10':
                        #  breakout_item_status_name
                        session(['dispositions_sort_by' => 'status_name']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                    default:
                        # code...
                        session(['dispositions_sort_by' => 'dispositions.created_at']);
                        $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('dispositions_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['dispositions_sort_by' => 'dispositions.created_at']);
                $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                session(['dispositions_asc_desc' => 'asc']);
                $dispositionsAscDesc = $request->session()->get('dispositions_asc_desc');
                session(['dispositions_asc_desc_opposite' => '1']);
                $dispositionsAscDescOpposite = $request->session()->get('dispositions_asc_desc_opposite');
            } else {
                // use values in the session
                $dispositionssSortBy = $request->session()->get('dispositions_sort_by');
                $dispositionsAscDesc = $request->session()->get('dispositions_asc_desc');
                $dispositionsAscDescOpposite = $request->session()->get('dispositions_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('dispositions_program_filter'))) {
                //Update the session
                session(['dispositions_program_filter' => $request->query('dispositions_program_filter')]);
                $dispositionsProgramFilter = $request->session()->get('dispositions_program_filter');
                session(['dispositions_program_filter_operator' => '=']);
                $dispositionsProgramFilterOperator = $request->session()->get('dispositions_program_filter_operator');
            } elseif (is_null($request->session()->get('dispositions_program_filter')) || $request->query('dispositions_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['dispositions_program_filter' => '%']);
                $dispositionsProgramFilter = $request->session()->get('dispositions_program_filter');
                session(['dispositions_program_filter_operator' => 'LIKE']);
                $dispositionsProgramFilterOperator = $request->session()->get('dispositions_program_filter_operator');
            } else {
                // use values in the session
                $dispositionsProgramFilter = $request->session()->get('dispositions_program_filter');
                $dispositionsProgramFilterOperator = $request->session()->get('dispositions_program_filter_operator');
            }

            if (is_numeric($request->query('dispositions_status_filter'))) {
                //Update the session
                session(['dispositions_status_filter' => $request->query('dispositions_status_filter')]);
                $dispositionsStatusFilter = $request->session()->get('dispositions_status_filter');
                session(['dispositions_status_filter_operator' => '=']);
                $dispositionsStatusFilterOperator = $request->session()->get('dispositions_program_filter_operator');
            } elseif (is_null($request->session()->get('dispositions_status_filter')) || $request->query('dispositions_status_filter') == 'ALL') {
                // There is no Status Filter in the Session
                session(['dispositions_status_filter' => '%']);
                $dispositionsStatusFilter = $request->session()->get('dispositions_status_filter');
                session(['dispositions_status_filter_operator' => 'LIKE']);
                $dispositionsStatusFilterOperator = $request->session()->get('dispositions_status_filter_operator');
            } else {
                // use values in the session
                $dispositionsStatusFilter = $request->session()->get('dispositions_status_filter');
                $dispositionsStatusFilterOperator = $request->session()->get('dispositions_status_filter_operator');
            }

            // Insert other Filters here
            $currentUser = Auth::user();

            //// set the defualt begining for just a status filter - should start with where if there
            //// is no program filter
            $and = ' WHERE ';
            if ($dispositionsProgramFilter) {
                $dispositionsWhereOrder = "WHERE dispositions.program_id ".$dispositionsProgramFilterOperator." '".$dispositionsProgramFilter."' \n";
                $and = ' AND ';
            }

            if ($dispositionsStatusFilter) {
                $dispositionsWhereOrder .= $and."dispositions.status_id ".$dispositionsStatusFilterOperator." '".$dispositionsStatusFilter."' \n";
                $and = ' AND ';
            }

            $dispositionsWhereOrder .= $and." dispositions.entity_id $where_entity_id_operator '$where_entity_id' "." \n";
            if ($dispositionssSortBy) {
                $dispositionsWhereOrder .="ORDER BY ".$dispositionssSortBy." ".$dispositionsAscDesc;
            }

            $dispositions = DB::select(
                DB::raw("
                            SELECT
                                dispositions.id AS id ,
                                dispositions.account_id,
                                dispositions.created_at as 'date',
                                dispositions.status_id,
                                dispositions.program_id,
                                dispositions.entity_id,
                                dispositions.parcel_id,
                                parcels.parcel_id  as pid,
                                invstat.invoice_status_name as status_name,
                                pr.program_name ,
                                ent.entity_name

                            FROM
                                dispositions

                            INNER JOIN parcels ON dispositions.parcel_id = parcels.id
                            INNER JOIN programs pr ON dispositions.program_id = pr.id
                            INNER JOIN entities ent ON dispositions.entity_id = ent.id
                            LEFT JOIN invoice_statuses invstat ON dispositions.status_id = invstat.id

                            ".$dispositionsWhereOrder."
                        ")
            );

            $programs = Disposition::join('programs', 'dispositions.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->get()->all();
            $statuses = Disposition::join('invoice_statuses', 'dispositions.status_id', '=', 'invoice_statuses.id')
                            ->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')
                            ->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')
                            ->get()
                            ->all();

            return view('dashboard.disposition_list', compact('dispositions', 'programs', 'statuses', 'currentUser', 'dispositions_sorted_by_query', 'dispositionsAscDesc', 'dispositionsAscDescOpposite', 'programs', 'dispositionsProgramFilter', 'dispositionsStatusFilter'));
        } else {
            return 'Sorry you do not have access to the Disposition Listing page. Please try logging in again or contact your admin to request access.';
        }

        if (Auth::user()->entity_id != 1) {
            $dispositions = Disposition::where('entity_id', '=', Auth::user()->entity_id)->get();
        } else {
            $dispositions = Disposition::get();
        }

        return view('dashboard.disposition_list', compact('dispositions'));
    }

    public function dispositionInvoiceList(Request $request)
    {
        if (Gate::allows('view_disposition') || Auth::user()->entity_id == 1) {
            // $lc = new LogConverter('disposition_invoice', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed disposition invoice list')->save();

            $query = new DispositionInvoice;

            $disposition_invoices_query = DispositionInvoice::with('entity')
                    ->with('dispositions')
                    ->with('dispositions.items')
                    ->with('transactions');

            // determine if they are OHFA or not
            if (Auth::user()->entity_id != 1) {
                $disposition_invoices_query->where('entity_id', '=', Auth::user()->entity_id);
                $where_entity_id = Auth::user()->entity_id;
                $where_entity_id_operator = '=';
            } else {
                $disposition_invoices_query->where('entity_id', '>', 0);
                $where_entity_id = 0;
                $where_entity_id_operator = '>';
            }

            // The sorting column
            $sortedBy = $request->query('invoices_sort_by');
            //$sortedBy=1;
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['disposition_invoices_sorted_by_query'=>$sortedBy]);
                $invoices_sorted_by_query = $request->session()->get('disposition_invoices_sorted_by_query');
            } elseif (!is_null($request->session()->get('disposition_invoices_sorted_by_query'))) {
                // use the session value
                $invoices_sorted_by_query = $request->session()->get('disposition_invoices_sorted_by_query');
            } else {
                // set the default
                session(['disposition_invoices_sorted_by_query'=>'12']);
                $invoices_sorted_by_query = $request->session()->get('disposition_invoices_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query
            if (!is_null($sortedBy)) {
                switch ($request->query('invoices_asc_desc')) {
                    case '1':
                        session(['disposition_invoices_asc_desc'=> 'desc']);
                        $invoicesAscDesc =  $request->session()->get('disposition_invoices_asc_desc');
                        session(['disposition_invoices_asc_desc_opposite' => "0"]);
                        $invoicesAscDescOpposite =  $request->session()->get('disposition_invoices_asc_desc_opposite');
                        break;

                    default:
                        session(['disposition_invoices_asc_desc'=> 'asc']);
                        $invoicesAscDesc =  $request->session()->get('disposition_invoices_asc_desc');
                        session(['disposition_invoices_asc_desc_opposite' => '1']);
                        $invoicesAscDescOpposite = $request->session()->get('disposition_invoices_asc_desc_opposite');
                        break;
                }

                switch ($sortedBy) {
                    case '12':
                        # created_at
                        session(['disposition_invoices_sort_by' => 'disposition_invoices.created_at']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '2':
                        # invoice_id
                        session(['disposition_invoices_sort_by' => 'disposition_invoices.id']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '3':
                        # account_id
                        session(['disposition_invoices_sort_by' => 'disposition_invoices.account_id']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '4':
                        # program_id
                        session(['disposition_invoices_sort_by' =>'disposition_invoices.program_id']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '5':
                        # entity_id
                        session(['disposition_invoices_sort_by' =>'disposition_invoices.entity_id']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '6':
                        # total_parcels
                        session(['disposition_invoices_sort_by' => 'total_parcels']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '7':
                        # total_requested
                        session(['disposition_invoices_sort_by' => 'total_requested']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '8':
                        #  total_approved
                        session(['disposition_invoices_sort_by' => 'total_approved']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '9':
                        #  total_amount (invoiced)
                        session(['disposition_invoices_sort_by' => 'total_amount']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '10':
                        #  total_paid
                        session(['disposition_invoices_sort_by' => 'total_paid']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    case '11':
                        #  invoice_status_name
                        session(['disposition_invoices_sort_by' => 'invoice_status_name']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                    default:
                        # code...
                        session(['disposition_invoices_sort_by' => 'disposition_invoices.created_at']);
                        $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('disposition_invoices_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['disposition_invoices_sort_by' => 'disposition_invoices.created_at']);
                $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                session(['disposition_invoices_asc_desc' => 'asc']);
                $invoicesAscDesc = $request->session()->get('disposition_invoices_asc_desc');
                session(['disposition_invoices_asc_desc_opposite' => '1']);
                $invoicesAscDescOpposite = $request->session()->get('disposition_invoices_asc_desc_opposite');
            } else {
                // use values in the session
                $invoicessSortBy = $request->session()->get('disposition_invoices_sort_by');
                $invoicesAscDesc = $request->session()->get('disposition_invoices_asc_desc');
                $invoicesAscDescOpposite = $request->session()->get('disposition_invoices_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('invoices_program_filter'))) {
                //Update the session
                session(['disposition_invoices_program_filter' => $request->query('invoices_program_filter')]);
                $invoicesProgramFilter = $request->session()->get('disposition_invoices_program_filter');
                session(['disposition_invoices_program_filter_operator' => '=']);
                $invoicesProgramFilterOperator = $request->session()->get('disposition_invoices_program_filter_operator');
            } elseif (is_null($request->session()->get('disposition_invoices_program_filter')) || $request->query('invoices_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['disposition_invoices_program_filter' => '%%']);
                $invoicesProgramFilter = $request->session()->get('disposition_invoices_program_filter');
                session(['disposition_invoices_program_filter_operator' => 'LIKE']);
                $invoicesProgramFilterOperator = $request->session()->get('disposition_invoices_program_filter_operator');
            } else {
                // use values in the session
                $invoicesProgramFilter = $request->session()->get('disposition_invoices_program_filter');
                $invoicesProgramFilterOperator = $request->session()->get('disposition_invoices_program_filter_operator');
            }

            if (is_numeric($request->query('invoices_status_filter'))) {
                //Update the session
                session(['disposition_invoices_status_filter' => $request->query('invoices_status_filter')]);
                $invoicesStatusFilter = $request->session()->get('disposition_invoices_status_filter');
                session(['disposition_invoices_status_filter_operator' => '=']);
                $invoicesStatusFilterOperator = $request->session()->get('disposition_invoices_program_filter_operator');
            } elseif (is_null($request->session()->get('disposition_invoices_status_filter')) || $request->query('invoices_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['disposition_invoices_status_filter' => '%%']);
                $invoicesStatusFilter = $request->session()->get('disposition_invoices_status_filter');
                session(['disposition_invoices_status_filter_operator' => 'LIKE']);
                $invoicesStatusFilterOperator = $request->session()->get('disposition_invoices_status_filter_operator');
            } else {
                // use values in the session
                $invoicesStatusFilter = $request->session()->get('disposition_invoices_status_filter');
                $invoicesStatusFilterOperator = $request->session()->get('disposition_invoices_status_filter_operator');
            }

            // Insert other Filters here
            $currentUser = Auth::user();

            if ($invoicesProgramFilter && $invoicesProgramFilter != "%%") {
                $disposition_invoices_query->where("disposition_invoices.program_id", $invoicesProgramFilterOperator, $invoicesProgramFilter);
            }

            if ($invoicesStatusFilter && $invoicesStatusFilter != "%%") {
                $disposition_invoices_query->where("disposition_invoices.status_id", $invoicesStatusFilterOperator, $invoicesStatusFilter);
            }

            $disposition_invoices_query->where("disposition_invoices.entity_id", $where_entity_id_operator, $where_entity_id);

            if ($invoicessSortBy) {
                if ($invoicessSortBy == "invoice_status_name") {
                    $disposition_invoices_query->join('invoice_statuses', function ($join) use ($invoicessSortBy, $invoicesAscDesc) {
                        $join->on('invoice_statuses.id', '=', 'disposition_invoices.status_id');
                    });
                    $disposition_invoices_query->orderBy('invoice_statuses.'.$invoicessSortBy, $invoicesAscDesc);
                } else {
                    $disposition_invoices_query->orderBy($invoicessSortBy, $invoicesAscDesc);
                }
            }

            $disposition_invoices = $disposition_invoices_query->select('disposition_invoices.*')->get();

            foreach ($disposition_invoices as $invoice) {
                $total = 0;
                $total_paid = 0;
                foreach ($invoice->dispositions as $disposition) {
                    $total = $total + $disposition->total();
                }
                $invoice->total = $total;
                $total_paid = $total_paid + $invoice->transactions->sum('amount');
                $invoice->total_paid = $total_paid;
            }

            $programs = DispositionInvoice::join('programs', 'disposition_invoices.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->get();
            $statuses = DispositionInvoice::join('invoice_statuses', 'disposition_invoices.status_id', '=', 'invoice_statuses.id')->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')->get();

            return view('dashboard.disposition_invoice_list', compact('disposition_invoices', 'programs', 'statuses', 'currentUser', 'invoices_sorted_by_query', 'invoicesAscDesc', 'invoicesAscDescOpposite', 'invoicesProgramFilter', 'invoicesStatusFilter'));
        } else {
            return 'Sorry you do not have access to Disposition Invoice Listing page. Please try logging in again or contact your admin to request access.';
        }
    }

    public function getDispositionsFromInvoiceId(DispositionInvoice $invoice)
    {
        if (!Gate::allows('view-disposition') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to this resource.';
        }
        $dispositions = $invoice->dispositions;

        $need_to_reload = 0;

        setlocale(LC_MONETARY, 'en_US');
        foreach ($dispositions as $disposition) {
            $disposition->load('program')->load('status')->load('parcel');

            $disposition->invoiced_total = $disposition->total();

            $disposition->created_at_m = date('m', strtotime($disposition->created_at));
            $disposition->created_at_d = date('d', strtotime($disposition->created_at));
            $disposition->created_at_Y = date('Y', strtotime($disposition->created_at));

            $disposition->total_formatted = money_format('%n', $disposition->total());

            $disposition->total = $disposition->total();
            // check if total is $0 and adjust status accordingly (if $0 mark as paid)
            if ($disposition->status_id != 6 && $disposition->total == 0) {
                // load the disposition to update (we added some columns so we have to load another instance)
                $disposition_to_update = Disposition::where('id', '=', $disposition->id)->first();

                $disposition_to_update->update([
                    "status_id" => 6
                ]);

                // also update current instance
                $disposition->status_id = 6;
                $need_to_reload = 1;
                $disposition->load('program')->load('status')->load('parcel');
            }

            // if invoice is fully paid, make sure all dispositions in it is marked as such
            if ($invoice->status_id == 6) {
                // make a copy of the disposition because we extended the collection with additional fields not in the model
                $updated_disposition = Disposition::where('id', '=', $disposition->id);
                $updated_disposition->update(['status_id'=>6]); // Paid
                $disposition->status_id = 6;
                $need_to_reload = 1;
            }
        }

        if ($need_to_reload) {
            $dispositions = $invoice->dispositions;
        }

        return ['dispositions'=>$dispositions,'invoice_id'=>$invoice->id];
    }

    /**
     * Show dispositions belonging to a parcel
     *
     * @param  int $parcel_id
     * @return Response
     */

    public function getDispositionFromParcelId($parcel_id, $disposition = 'all', $format = null)
    {
        if (!Gate::allows('view-disposition') && Auth::user()->entity_id != 1) {
            return 'Sorry you do not have access to the disposition.';
        }

        $parcel = Parcel::where('id', '=', $parcel_id)->first();

        if (!$parcel) {
            return 'Sorry this parcel cannot be found.';
        }

        // $lc = new LogConverter('dispositions', 'view');
        // if (!is_null($disposition) && $disposition != 'all') {
        //     $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed disposition '.$disposition.' for parcel '.$parcel->id)->save();
        // } else {
        //     $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed disposition for parcel '.$parcel->id)->save();
        // }
        setlocale(LC_MONETARY, 'en_US');
        $parcel->with('state')->with('county');

        //
        // check if parcel's invoice has not been paid (status id 6)
        $parcelid = $parcel->id;
        $query = DB::table('reimbursement_invoices')
            ->where('status_id', '=', 6)
            ->join('parcels_to_reimbursement_invoices', function ($join) use ($parcelid) {
                $join->on('parcels_to_reimbursement_invoices.reimbursement_invoice_id', '=', 'reimbursement_invoices.id')
                    ->where('parcels_to_reimbursement_invoices.parcel_id', '=', $parcelid);
            });
        $checked_invoice = $query->first();



        if (!$checked_invoice || (!Gate::allows('create-disposition') && !Gate::allows('hfa-review-disposition') && !Auth::user()->isHFAAdmin())) {
            $types = [];
            $document_categories = [];
            $disposition = null;
            $nip = null;
            $entity = null;
            $proceed = 0;
            $isApprover = 0;
            $isApprover_hfa = 0;
            $isApproved = 0;
            $isApproved_hfa = 0;
            $approvals = [];
            $approvals_hfa = [];
            $calculation = [];
            $actual = [];
            $step = [];
            $current_user = Auth::user();
            $landbankRequestApprovers = [];
            $pending_approvers_hfa = [];
            $pending_approvers = [];
            $supporting_documents = [];
            //return view('parcels.disposition-tab', compact('isApprover', 'parcel','types','proceed','disposition','document_categories','nip','entity','approvals','calculation','potential_approvers_lb','potential_approvers_hfa'));
            return view('parcels.disposition-tab', compact('isApprover', 'isApprover_hfa', 'parcel', 'types', 'proceed', 'disposition', 'document_categories', 'nip', 'entity', 'approvals', 'approvals_hfa', 'calculation', 'actual', 'supporting_documents', 'current_user', 'isApproved', 'isApproved_hfa', 'pending_approvers', 'pending_approvers_hfa', 'landbankRequestApprovers', 'step'));
        } else {
            $proceed = 1;
        }
        // get disposition types for step 1 dropdown
        $types = DispositionType::where('active', 1)->orderBy('disposition_type_name', 'asc')->get();

        // get current disposition if it exists
        if ($disposition == 'all') {
            $dispositions = Disposition::where('parcel_id', '=', $parcel->id)->with('parcel')->orderby('id', 'DESC')->get();
            $disposition = null;
        } elseif (!is_null($disposition)) {
            $disposition = Disposition::where('parcel_id', '=', $parcel->id)->where('id', '=', $disposition)->with('parcel')->orderby('id', 'DESC')->first();
            if ($disposition) {
                if ($disposition->sf_parcel_id !== null) {
                    $legacy = 1;
                } else {
                    $legacy = 0;
                }
            }
        }

        // get all declined dispositions
        $declined_dispositions = Disposition::where('parcel_id', '=', $parcel->id)->with('parcel')->where('status_id', '=', 5)->get();

        // get document categories
        $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

        // get entities
        $nip = Entity::where('id', 1)->with('state')->with('user')->first();
        $entity = Entity::where('id', $parcel->entity_id)->with('state')->with('user')->first();

        $isApprover = 0;
        if ($disposition) {
            // Guide
            perform_all_disposition_checks($disposition);

            // get approvers (type id 2 is for reimbursement requests)
            $landbankDispositionApprovers = User::where('entity_id', '=', $disposition->entity_id)
                                            ->where('active', '=', 1)
                                            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                            ->where('users_roles.role_id', '=', 11)
                                            ->where('users.active', 1)
                                            ->select('users.id', 'users.name')
                                            ->get();
            $HFADispositionApprovers = User::where('entity_id', '=', 1)
                                            ->where('active', '=', 1)
                                            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                            ->where('users_roles.role_id', '=', 7)
                                            ->where('users.active', 1)
                                            ->select('users.id', 'users.name')
                                            ->get();

            $added_approvers = ApprovalRequest::where('approval_type_id', '=', 1)
                                        ->where('link_type_id', '=', $disposition->id)
                                        ->pluck('user_id as id');

            // remove inactive approvers
            $resetAddedApprovers = 0;

            foreach ($added_approvers as $approver) {
                $inactiveUserCheck = User::where('id', $approver)->where('active', 0)->count();

                if ($inactiveUserCheck > 0) {
                    $debug = "Inactive user found";
                    $ARCheck = ApprovalRequest::select('*')->where('approval_type_id', '=', 1)
                                        ->where('link_type_id', '=', $disposition->id)
                                        ->where('user_id', $approver)->first();

                    //dd($debug,$ARCheck);
                    // check to see if an action has been taken against it
                    if (ApprovalAction::where('approval_request_id', $ARCheck->id)->count() < 1) {
                        // no action taken - delete it.
                        $resetAddedApprovers = 1;
                        ApprovalRequest::where('id', $ARCheck->id)->delete();
                    }
                }
            }
            if ($resetAddedApprovers == 1) {
                $added_approvers = ApprovalRequest::where('approval_type_id', '=', 1)
                                        ->where('link_type_id', '=', $disposition->id)
                                        ->pluck('user_id as id');
            }
            ///////////////////////////////////////////////////////////////////////////

            $added_approvers_hfa = ApprovalRequest::where('approval_type_id', '=', 11)
                                    ->where('link_type_id', '=', $disposition->id)
                                    ->pluck('user_id as id');

            // remove inactive hfa approvers
            $resetAddedApprovers = 0;
            foreach ($added_approvers_hfa as $approver) {
                $inactiveUserCheck = User::where('id', $approver)->where('active', 0)->count();

                if ($inactiveUserCheck > 0) {
                    $debug = "Inactive user found";
                    $ARCheck = ApprovalRequest::select('*')->where('approval_type_id', '=', 1)
                                        ->where('link_type_id', '=', $disposition->id)
                                        ->where('user_id', $approver)->first();

                    //dd($debug,$ARCheck);
                    // check to see if an action has been taken against it
                    if ($ARCheck) {
                        if (ApprovalAction::where('approval_request_id', $ARCheck->id)->count() < 1) {
                            // no action taken - delete it.
                            $resetAddedApprovers = 1;
                            ApprovalRequest::where('id', $ARCheck->id)->delete();
                        }
                    }
                }
            }
            if ($resetAddedApprovers == 1) {
                $added_approvers_hfa = ApprovalRequest::where('approval_type_id', '=', 11)
                                    ->where('link_type_id', '=', $disposition->id)
                                    ->pluck('user_id as id');
            }

            $pending_approvers = [];
            $pending_approvers_hfa = [];

            if (count($added_approvers) == 0 && count($landbankDispositionApprovers) > 0) {
                foreach ($landbankDispositionApprovers as $landbankDispositionApprover) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 1,
                        "link_type_id" => $disposition->id,
                        "user_id" => $landbankDispositionApprover->id
                    ]);
                    $newApprovalRequest->save();
                }
            } elseif (count($landbankDispositionApprovers) > 0) {
                // list all approvers who are not already in the approval_request table

                $pending_approvers = User::where('entity_id', '=', $disposition->entity_id)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 11)
                                        ->where('active', 1)
                                        ->whereNotIn('id', $added_approvers)
                                        ->select('users.id', 'users.name')
                                        ->get();
            }

            if (count($added_approvers_hfa) == 0 && count($HFADispositionApprovers) > 0) {
                foreach ($HFADispositionApprovers as $HFADispositionApprover) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 11,
                        "link_type_id" => $disposition->id,
                        "user_id" => $HFADispositionApprover->id
                    ]);
                    $newApprovalRequest->save();
                }
            } elseif (count($HFADispositionApprovers) > 0) {
                // list all approvers who are not already in the approval_request table

                $pending_approvers_hfa = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 7)
                                        ->where('active', 1)
                                        ->whereNotIn('id', $added_approvers_hfa)
                                        ->select('users.id', 'users.name')
                                        ->get();
            }

            // get approvals

            // Landbank
            $approval_status = guide_approval_status(1, $disposition->id);
            $isApprover     = $approval_status['is_approver'];
            $hasApprovals   = $approval_status['has_approvals'];
            $isApproved     = $approval_status['is_approved'];
            $approvals      = $approval_status['approvals'];
            $isDeclined     = $approval_status['is_declined'];

            // hfa
            $approval_status_hfa = guide_approval_status(11, $disposition->id);
            $isApprover_hfa     = $approval_status_hfa['is_approver'];
            $hasApprovals_hfa   = $approval_status_hfa['has_approvals'];
            $isApproved_hfa     = $approval_status_hfa['is_approved'];
            $approvals_hfa      = $approval_status_hfa['approvals'];
            $isDeclined_hfa     = $approval_status_hfa['is_declined'];

            $isReadyForInvoice = 0;

            if ($isApproved && $isApproved_hfa) {
                $isReadyForInvoice = 1;

                if ($disposition->status_id == 5) {
                    $disposition->update([
                        'status_id' => 3 // back to pending HFA
                    ]);

                    if ($disposition->parcel) {
                        $disposition->parcel->update([
                                "landbank_property_status_id" => 15,
                                "hfa_property_status_id" => 29
                        ]);
                        perform_all_parcel_checks($disposition->parcel);
                        guide_next_pending_step(2, $disposition->parcel->id);
                    }
                    // $lc = new LogConverter('dispositions', 'approved by all HFA');
                    // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc('All HFA approved the disposition.')->save();
                }
            }
        } else {
            $approvals = [];

            $potential_approvers_lb = [];
            $potential_approvers_hfa = [];
        }

        // calculation
        $imputed_cost_per_parcel = ProgramRule::first(['imputed_cost_per_parcel']);
        $rule_min_cost = $imputed_cost_per_parcel->imputed_cost_per_parcel;
        $rule_min_cost_formatted = money_format('%n', $rule_min_cost);


        if ($disposition) {
            $maintenance_array = $this->getUnusedMaintenance($parcel);
            $income = $disposition->program_income;
            $transaction_cost = $disposition->transaction_cost;
            $maintenance_total = $this->getMaintenanceTotal($parcel);
            $eligible_income = $income - $transaction_cost;
            if ($eligible_income < 0) {
                $eligible_income = 0;
            }

            $request_for_recapture = new Request;
            $request_for_recapture->request->add(['income'=>$income, 'cost'=>$transaction_cost]);


            $payback = $this->computeRecaptureOwed($disposition, $request_for_recapture);
            $demolition = $this->getDemolitionTotal($parcel);

            if ($eligible_income > $demolition + $maintenance_array['unused']) {
                $gain = $eligible_income - $demolition - $maintenance_array['unused'];
            } else {
                $gain = 0;
            }
        } else {
            $maintenance_array = [];
            $maintenance_array['monthly_rate'] = 0;
            $maintenance_array['months'] = 0;
            $maintenance_array['unused'] = 0;
            $income = 0;
            $maintenance_array['months_prepaid'] = 36;
            $transaction_cost = 0;
            $maintenance_total = 0;
            $eligible_income = 0;
            $payback = 0;
            $demolition = 0;
            $gain = 0;
        }

        $calculation = [
            'maintenance_total' => number_format($maintenance_total, 2, '.', ''),
            'maintenance_total_formatted' => money_format('%n', $maintenance_total),
            'months_prepaid' => $maintenance_array['months_prepaid'],
            'monthly_maintenance' => number_format($maintenance_array['monthly_rate'], 2, '.', ''),
            'monthly_maintenance_rate' => money_format('%n', $maintenance_array['monthly_rate']),
            'rule_min_cost' => $rule_min_cost,
            'rule_min_cost_formatted' => money_format('%n', $rule_min_cost),
            'month_unused' => $maintenance_array['months'],
            'maintenance_unused' => number_format($maintenance_array['unused'], 2, '.', ''),
            'maintenance_unused_formatted' => money_format('%n', $maintenance_array['unused']),
            'transaction_cost' => number_format($transaction_cost, 2, '.', ''),
            'transaction_cost_formatted' => money_format('%n', $transaction_cost),
            'income' => number_format($income, 2, '.', ''),
            'income_formatted' => money_format('%n', $income),
            'demolition_cost' => number_format($demolition, 2, '.', ''),
            'demolition_cost_formatted' => money_format('%n', $demolition),
            'eligible_income' => number_format($eligible_income, 2, '.', ''),
            'eligible_income_formatted' => money_format('%n', $eligible_income),
            'payback' => number_format($payback, 2, '.', ''),
            'payback_formatted' => money_format('%n', $payback),
            'gain' => number_format($gain, 2, '.', ''),
            'gain_formatted' => money_format('%n', $gain)
        ];

        // compute actual results based on calculation and HFA adjustments
        if ($disposition) {
            // check against the rules
            $rules = ProgramRule::first(['imputed_cost_per_parcel','maintenance_max','maintenance_recap_pro_rate','demolition_max']);
            $imputed_cost_per_parcel = $rules->imputed_cost_per_parcel; //200
            $maintenance_max = $rules->maintenance_max; //1200
            $maintenance_recap_pro_rate = $rules->maintenance_recap_pro_rate; //36
            if ($disposition->hfa_calc_months_prepaid != null) {
                $maintenance_recap_pro_rate = $disposition->hfa_calc_months_prepaid;
            }
            $demolition_max = $rules->demolition_max;

            if (!is_null($disposition->hfa_calc_maintenance_total)) {
                $maintenance_total = $disposition->hfa_calc_maintenance_total;
            }
            if (!is_null($disposition->hfa_calc_income)) {
                $income = $disposition->hfa_calc_income;
            }
            if (!is_null($disposition->hfa_calc_trans_cost)) {
                $transaction_cost = $disposition->hfa_calc_trans_cost;
            }
            if (!is_null($disposition->hfa_calc_demo_cost)) {
                $demolition = $disposition->hfa_calc_demo_cost;
            }
            if (!is_null($disposition->hfa_calc_epi)) {
                $eligible_income = $disposition->hfa_calc_epi;
            }
            if (!is_null($disposition->hfa_calc_payback)) {
                $payback = $disposition->hfa_calc_payback;
            }
            if (!is_null($disposition->hfa_calc_gain)) {
                $gain = $disposition->hfa_calc_gain;
            }
            if (!is_null($disposition->hfa_calc_monthly_rate)) {
                $monthly_rate = $disposition->hfa_calc_monthly_rate;
            } elseif (!is_null($disposition->hfa_calc_maintenance_total) && $disposition->hfa_calc_maintenance_total != 0) {
                $monthly_rate = $disposition->hfa_calc_maintenance_total / $maintenance_recap_pro_rate;
            } else {
                $monthly_rate = $maintenance_array['monthly_rate'];
            }

            if (!is_null($disposition->hfa_calc_maintenance_due)) {
                $maintenance_to_repay = $disposition->hfa_calc_maintenance_due;
            } else {
                $maintenance_to_repay = $maintenance_total - $maintenance_array['hfa_months']*$monthly_rate;
            }

            if ($maintenance_to_repay < 0) {
                $maintenance_to_repay = 0;
            }

            if (!is_null($disposition->hfa_calc_payback)) {
                $payback = $disposition->hfa_calc_payback;
            } else {
                if ($eligible_income > $demolition) {
                    $payback = $demolition + $maintenance_to_repay;
                } else {
                    $payback = $eligible_income + $maintenance_to_repay;
                }
            }


            if (!is_null($disposition->hfa_calc_gain)) {
                $gain = $disposition->hfa_calc_gain;
            } else {
                if ($eligible_income > $demolition + $maintenance_to_repay) {
                    $gain = $eligible_income - $demolition - $maintenance_to_repay;
                } else {
                    $gain = 0;
                }
            }

            $actual = [
                'maintenance_total' => $maintenance_total,
                'maintenance_total_formatted' => money_format('%n', $maintenance_total),
                'months_prepaid' => $maintenance_array['months_prepaid'],
                'monthly_maintenance_rate' => money_format('%n', $monthly_rate),
                'rule_min_cost' => $rule_min_cost,
                'rule_min_cost_formatted' => money_format('%n', $rule_min_cost),
                'month_unused' => $maintenance_array['hfa_months'],
                'maintenance_unused' => $maintenance_to_repay,
                'maintenance_unused_formatted' => money_format('%n', $maintenance_to_repay),
                'transaction_cost' => $transaction_cost,
                'transaction_cost_formatted' => money_format('%n', $transaction_cost),
                'income' => $income,
                'income_formatted' => money_format('%n', $income),
                'demolition_cost' => $demolition,
                'demolition_cost_formatted' => money_format('%n', $demolition),
                'eligible_income' => $eligible_income,
                'eligible_income_formatted' => money_format('%n', $eligible_income),
                'payback' => $payback,
                'payback_formatted' => money_format('%n', $payback),
                'gain' => $gain,
                'gain_formatted' => money_format('%n', $gain)
            ];
        } else {
            $actual = [];
        }

        // get documents from the category "Disposition Supporting Documents" id 38
        $supporting_documents = [];
        $documents = Document::where('parcel_id', '=', $parcel->id)->get();
        if (count($documents)) {
            foreach ($documents as $document) {
                if ($document->categories) {
                    $categories_decoded = json_decode($document->categories, true); // cats used by the doc
                } else {
                    $categories_decoded = [];
                }
                if ($document->approved) {
                    $categories_approved = json_decode($document->approved, true);
                } else {
                    $categories_approved = [];
                }
                if ($document->notapproved) {
                    $categories_notapproved = json_decode($document->notapproved, true);
                } else {
                    $categories_notapproved = [];
                }

                if (in_array('38', $categories_decoded)) {
                    $supporting_documents[$document->id]['filename'] = $document->filename;
                    $supporting_documents[$document->id]['comment'] = $document->comment;
                    $supporting_documents[$document->id]['id'] = $document->id;
                    $supporting_documents[$document->id]['date'] = $document->created_at;
                    if (in_array('38', $categories_approved)) {
                        $supporting_documents[$document->id]['approved'] = 1;
                    } else {
                        $supporting_documents[$document->id]['approved'] = 0;
                    }
                    if (in_array('38', $categories_notapproved)) {
                        $supporting_documents[$document->id]['notapproved'] = 1;
                    } else {
                        $supporting_documents[$document->id]['notapproved'] = 0;
                    }
                    $supporting_documents[$document->id]['category'] = document_category_name(38);
                }
                if (in_array('46', $categories_decoded)) { // disposition sale final
                    $supporting_documents[$document->id]['filename'] = $document->filename;
                    $supporting_documents[$document->id]['comment'] = $document->comment;
                    $supporting_documents[$document->id]['id'] = $document->id;
                    $supporting_documents[$document->id]['date'] = $document->created_at;
                    if (in_array('46', $categories_approved)) {
                        $supporting_documents[$document->id]['approved'] = 1;
                        guide_set_progress($disposition->id, 16, $status = 'completed', 0); // finalize sale
                    } else {
                        $supporting_documents[$document->id]['approved'] = 0;
                        guide_set_progress($disposition->id, 16, $status = 'started', 0);
                    }
                    if (in_array('46', $categories_notapproved)) {
                        $supporting_documents[$document->id]['notapproved'] = 1;
                        guide_set_progress($disposition->id, 16, $status = 'started', 0);
                    } else {
                        $supporting_documents[$document->id]['notapproved'] = 0;
                    }
                    $supporting_documents[$document->id]['category'] = document_category_name(46);
                } else {
                    if ($disposition) {
                        guide_set_progress($disposition->id, 16, $status = 'started', 0);
                    }
                }
                if ($disposition) {
                    if ($disposition->disposition_type_id == 5) {
                        if (in_array('22', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('22', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('22', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(22);
                        }
                    } elseif ($disposition->disposition_type_id == 4) {
                        if (in_array('22', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('22', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('22', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(22);
                        }
                        if (in_array('23', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('23', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('23', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(23);
                        }
                    } elseif ($disposition->disposition_type_id == 2) {
                        if (in_array('22', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('22', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('22', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(22);
                        }
                        if (in_array('24', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('24', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('24', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(24);
                        }
                        if (in_array('25', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('25', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('25', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(25);
                        }
                        if (in_array('26', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('26', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('26', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(26);
                        }
                    } elseif ($disposition->disposition_type_id == 1) {
                        if (in_array('22', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('22', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('22', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(22);
                        }
                        if (in_array('25', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('25', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('25', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(25);
                        }
                        if (in_array('26', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('26', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('26', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(26);
                        }
                        if (in_array('27', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('27', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('27', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(27);
                        }
                        if (in_array('28', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('28', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('28', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(28);
                        }
                        if (in_array('29', $categories_decoded)) {
                            $supporting_documents[$document->id]['filename'] = $document->filename;
                            $supporting_documents[$document->id]['comment'] = $document->comment;
                            $supporting_documents[$document->id]['id'] = $document->id;
                            $supporting_documents[$document->id]['date'] = $document->created_at;
                            if (in_array('29', $categories_approved)) {
                                $supporting_documents[$document->id]['approved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['approved'] = 0;
                            }
                            if (in_array('29', $categories_notapproved)) {
                                $supporting_documents[$document->id]['notapproved'] = 1;
                            } else {
                                $supporting_documents[$document->id]['notapproved'] = 0;
                            }
                            $supporting_documents[$document->id]['category'] = document_category_name(29);
                        }
                    }
                }
            }
        }

        $current_user = User::where('id', '=', Auth::user()->id)->first();

        if ($disposition) {
            switch ($disposition->status_id) {
                case 1:
                    $step = "Draft";
                    break;
                case 2:
                    $step = "Pending Landbank Approval";
                    break;
                case 3:
                    $step = "Pending HFA Approval";
                    break;
                case 4:
                    $step = "Pending Payment";
                    break;
                case 5:
                    $step = "Declined";
                    break;
                case 6:
                    $step = "Paid";
                    break;
                case 7:
                    $step = "Approved";
                    break;
                case 8:
                    $step = "Submitted to Fiscal Agent";
                    break;
                default:
                    $step = "Draft";
            }
        }

        // get dates
        $invoiceid = ParcelsToReimbursementInvoice::where('parcel_id', '=', $parcel->id)->first();
        $invoice = ReimbursementInvoice::where('id', '=', $invoiceid->reimbursement_invoice_id)->first();

        if ($invoice->status_id != 6) {
            return 'Sorry it looks like the invoice has not been paid.';
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
                    return "Please make sure that all transactions have been cleared.";
                }
            } else {
                return "I cannot find a cleared transaction for this invoice. Please make sure that all transactions have been cleared."; // has not been paid
            }
        }

        if ($disposition) {
            $disposition_date = $disposition->created_at;
            if ($disposition->created_at) {
                if ($disposition->created_at->toDateTimeString() == "0000-00-00 00:00:00" || $disposition->created_at->toDateTimeString() == "-0001-11-30 00:00:00") {
                    $disposition->update([
                        'created_at' => Carbon\Carbon::today()->toDateTimeString()
                    ]);
                }
            } elseif ($disposition->updated_at) {
                $disposition->update([
                    'created_at' => $disposition->updated_at
                ]);
            }
        }

        $guide_steps = GuideStep::where('guide_step_type_id', '=', 1)->get();
        $guide_help = [];
        $guide_name = [];
        foreach ($guide_steps as $guide_step) {
            $guide_help[$guide_step->id] = $guide_step->step_help;
            $guide_name[$guide_step->id]['name'] = $guide_step->name;
            $guide_name[$guide_step->id]['name_completed'] = $guide_step->name_completed;
        }

        // list HFA disposition invoice approvers for the guide tooltips
        $invoice_hfa_approvers_list = '';
        if ($disposition) {
            if ($disposition->invoice) {
                $invoice_id = $disposition->invoice->disposition_invoice_id;
                $invoice_hfa_approvers = ApprovalRequest::where('approval_type_id', '=', 12)
                                    ->where('link_type_id', '=', $invoice_id)
                                    ->with('approver')
                                    ->get();
                foreach ($invoice_hfa_approvers as $invoice_hfa_approver) {
                    if ($invoice_hfa_approvers_list == '') {
                        $invoice_hfa_approvers_list = $invoice_hfa_approver->approver->name;
                    } else {
                        $invoice_hfa_approvers_list = $invoice_hfa_approvers_list.", ".$invoice_hfa_approver->approver->name;
                    }
                }
                if ($invoice_hfa_approvers_list != '') {
                    $invoice_hfa_approvers_list = "[".$invoice_hfa_approvers_list."]";
                }
            }
        }


        if ($format == "invoice" && $disposition) {
            //dd("open Invoice page here.");
        } elseif ($format == "print-approve" && $disposition) {
            // you can only print when the form is available... the disposition must have been created
            return view('pages.print_disposition', compact('isApprover', 'isApprover_hfa', 'parcel', 'types', 'proceed', 'disposition', 'document_categories', 'nip', 'entity', 'approvals', 'approvals_hfa', 'calculation', 'actual', 'current_user', 'isApproved', 'isApproved_hfa', 'pending_approvers', 'pending_approvers_hfa', 'landbankRequestApprovers', 'step', 'isDeclined', 'declinedDispositions'));
        } elseif ($format == "tab") {
            return view('parcels.disposition-tab', compact('isApprover', 'isApprover_hfa', 'parcel', 'types', 'proceed', 'disposition', 'document_categories', 'nip', 'entity', 'approvals', 'approvals_hfa', 'calculation', 'actual', 'supporting_documents', 'current_user', 'isApproved', 'isApproved_hfa', 'pending_approvers', 'pending_approvers_hfa', 'landbankRequestApprovers', 'step', 'disposition_date', 'invoice_payment_date', 'declinedDispositions'));
        } else {
            return view('pages.dispositions', compact('isApprover', 'isApprover_hfa', 'parcel', 'types', 'proceed', 'disposition', 'document_categories', 'nip', 'entity', 'approvals', 'approvals_hfa', 'calculation', 'actual', 'supporting_documents', 'current_user', 'isApproved', 'isApproved_hfa', 'pending_approvers', 'pending_approvers_hfa', 'landbankRequestApprovers', 'step', 'disposition_date', 'invoice_payment_date', 'isDeclined', 'declinedDispositions', 'guide_progress', 'guide_help', 'guide_name', 'invoice_hfa_approvers_list', 'legacy'));
        }
    }

    public function getUploadedDocuments(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('view-disposition') && Auth::user()->entity_id != 1) {
            $output['message'] = "Ruh Roh Shaggy! (Scooby Doo fan?)... You do not have access to the disposition. Make sure your manager has given you viewing priveleges.";
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
                    $output['message'] = "Displaying all the beautiful documents. Well, beautiful to me anyways.";
                }
            } else {
                $output['message'] = "Hmmm, no documents at this time. You can always add some!";
            }
            return $output;
        } else {
            $output['message'] = "Whoa. I cannot find the parcel this is tied to... that should not happen - try refreshing the page and try again.";
            return $output;
        }
    }

    public function uploadSupportingDocuments(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-disposition') && Auth::user()->entity_id != 1) {
            return 'Sorry you cannot upload documents to dispositions.';
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
                $characters = [' ','�','`',"'",'~','"','\'','\\','/'];
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
                // $lc=new LogConverter('document', 'create');
                // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                // store original file
                Storage::put($filepath, File::get($file));

                $uploadcount++;
            }

            // get current disposition and set progress
            $disposition = Disposition::where('parcel_id', '=', $parcel->id)->orderby('id', 'DESC')->first();
            guide_set_progress($disposition->id, 3, $status = 'completed', 1); // step 1 - uploaded documents

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
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-disposition') && Auth::user()->entity_id != 1) {
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
                // $lc = new LogConverter('document', 'comment');
                // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' added comment to document ')->save();
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function approveUploadSignature(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-disposition') && Auth::user()->entity_id != 1) {
            return 'Sorry you cannot upload documents to dispositions.';
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
                $characters = [' ','�','`',"'",'~','"','\'','\\','/'];
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
                // $lc=new LogConverter('document', 'create');
                // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
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

    public function approveDisposition(Parcel $parcel, $approvers = null, $document_ids = null, $hfa_approval = null)
    {
        if ((!Auth::user()->isLandbankDispositionApprover() || Auth::user()->entity_id != $parcel->entity_id) && !Auth::user()->isHFAAdmin() && !Auth::user()->isHFADispositionReviewer() && !Gate::allows('hfa-release-disposition') && !Auth::user()->isHFADispositionApprover()) {
            $output['message'] = 'No Disposition Approval For You! (Seinfeld fan?). Sorry, but it does not look like you have the right permissions to approve this disposition. Sorry!';
            return $output;
        }

        if ($parcel) {
            $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

            // check if multiple people need to record approvals
            if (count($approvers) > 0) {
                if ($document_ids !== null) {
                    $documents = explode(",", $document_ids);
                } else {
                    $documents = [];
                }
                $documents_json = json_encode($documents, true);

                if ($hfa_approval) {
                    $approval_type = 11;
                } else {
                    $approval_type = 1;
                }

                foreach ($approvers as $approver_id) {
                    $approver = ApprovalRequest::where('approval_type_id', '=', $approval_type)
                                ->where('link_type_id', '=', $disposition->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                    if (count($approver)) {
                        $action = new ApprovalAction([
                                'approval_request_id' => $approver->id,
                                'approval_action_type_id' => 5, //by proxy
                                'documents' => $documents_json
                            ]);
                        $action->save();

                        // $lc = new LogConverter('Dispositions', 'approval by proxy');
                        // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . 'approved the disposition for '.$approver->name)->save();

                        if ($hfa_approval) {
                            /*if($approver_id == 2){ // holly approval
                                guide_set_progress($disposition->id, 20, $status = 'completed'); // step 4 - Holly approval
                            }elseif($approver_id == 142){ // Jim approval
                                guide_set_progress($disposition->id, 21, $status = 'completed'); // step 4 - Jim approval
                            }*/
                        } else {
                            guide_set_progress($disposition->id, 4, $status = 'completed', 1); // step 1 - submitted for internal approval
                        }
                    }
                }
                perform_all_parcel_checks($parcel);
                guide_next_pending_step(2, $parcel->id);
                $output['message'] = 'This disposition was approved. And I approve of you smiling because of how awesome you are for getting this done!';
                $output['id'] = $approver_id;
                return $output;
            } else {
                $approver_id = Auth::user()->id;
                if ($hfa_approval) {
                    $approval_type = 11;
                } else {
                    $approval_type = 1;
                }
                $approver = ApprovalRequest::where('approval_type_id', '=', $approval_type)
                                ->where('link_type_id', '=', $disposition->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                if (count($approver)) {
                    $action = new ApprovalAction([
                            'approval_request_id' => $approver->id,
                            'approval_action_type_id' => 1
                        ]);
                    $action->save();

                    // $lc = new LogConverter('Dispositions', 'approval');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . 'approved the disposition.')->save();

                    if ($hfa_approval) {
                        // if($approver_id == 2){ // holly approval
                        //     guide_set_progress($disposition->id, 20, $status = 'completed'); // step 4 - Holly approval
                        // }elseif($approver_id == 142){ // Jim approval
                        //     guide_set_progress($disposition->id, 21, $status = 'completed'); // step 4 - Jim approval
                        // }
                    } else {
                        guide_set_progress($disposition->id, 4, $status = 'completed', 1); // step 1 - submitted for internal approval
                    }


                    $output['message'] = 'Your disposition was approved. Feels good doesn\'t it?';
                    $output['id'] = $approver_id;
                    return $output;
                } else {
                    $output['message'] = 'Something went wrong.';
                    $output['id'] = null;
                }
            }
        } else {
            $output['message'] = 'Something went wrong.';
            $output['id'] = null;
            return $output;
        }

        // output message
        $output['message'] = 'This disposition has been approved!';
        return $output;
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
                // $lc = new LogConverter('document', 'comment');
                // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' added comment to document ')->save();
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function approveHFAUploadSignature(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-disposition') && !Gate::allows('hfa-release-disposition') && Auth::user()->entity_id != 1) {
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
                $characters = [' ','�','`',"'",'~','"','\'','\\','/'];
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
                // $lc=new LogConverter('document', 'create');
                // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
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
    public function computeRecaptureOwed($disposition, Request $request)
    {
        $debug = [];

        $income = $request->get('income');
        $cost = $request->get('cost');

        $parcel = $disposition->parcel;

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

        if ($disposition->hfa_calc_months_prepaid != null) {
            $maintenance_recap_pro_rate = $disposition->hfa_calc_months_prepaid;
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
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();
        if (!is_null($disposition->hfa_calc_demo_cost)) {
            return $disposition->hfa_calc_demo_cost;
        } else {
            return $demolition;
        }
    }

    public function getUnusedMaintenance(Parcel $parcel)
    {
        // calculate number of unused maintenance month based on 36 months
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        $rules = ProgramRule::first(['imputed_cost_per_parcel', 'maintenance_recap_pro_rate', 'maintenance_max']); //200
        $imputed_cost_per_parcel = $rules->imputed_cost_per_parcel;

        $maintenance_recap_pro_rate = $rules->maintenance_recap_pro_rate; //36
        if ($disposition->hfa_calc_months_prepaid != null) {
            $maintenance_recap_pro_rate = $disposition->hfa_calc_months_prepaid;
        }

        $maintenance_max = $rules->maintenance_max;

        // get total maintenance
        $maintenance_total = $this->getMaintenanceTotal($parcel);
        if (!is_null($disposition->hfa_calc_maintenance_total)) {
            $maintenance_total_hfa = $disposition->hfa_calc_maintenance_total;
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


        $disposition_date = $disposition->created_at;
        if ($disposition->created_at) {
            if ($disposition->created_at->toDateTimeString() == "0000-00-00 00:00:00" || $disposition->created_at->toDateTimeString() == "-0001-11-30 00:00:00") {
                $disposition->update([
                    'created_at' => Carbon\Carbon::today()->toDateTimeString()
                ]);
            }
        } elseif ($disposition->updated_at) {
            $disposition->update([
                'created_at' => $disposition->updated_at
            ]);
        }


        $ts1 = strtotime($invoice_payment_date);
        $ts2 = strtotime($disposition_date);
        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);
        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);
        $months = (($year2 - $year1) * 12) + ($month2 - $month1) +1;
        if (!is_null($disposition->hfa_calc_months)) {
            $months_hfa = $disposition->hfa_calc_months;
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
        $maintenance['disposition_date'] = $disposition_date;
        $maintenance['invoice_payment_date'] = $invoice_payment_date;

        $maintenance['hfa_unused'] = $maintenance_total_hfa - ($months_hfa * $maintenance_total_hfa / $maintenance_recap_pro_rate);
        $maintenance['hfa_months'] = $months_hfa;
        $maintenance['hfa_monthly_rate'] = number_format($maintenance_total_hfa / $maintenance_recap_pro_rate, 2, '.', '');
        $maintenance['hfa_disposition_date'] = $disposition_date;
        $maintenance['hfa_invoice_payment_date'] = $invoice_payment_date;
        $maintenance['months_prepaid'] = $maintenance_recap_pro_rate;

        return $maintenance;
    }

    /**
     * Process steps throughout the disposition
     *
     * @param  int
     * @return Response
     */
    public function processStep(Parcel $parcel, Request $request)
    {
        if (!Gate::allows('create-disposition') && !Gate::allows('authorize-disposition-request') && !Gate::allows('submit-disposition') && !Gate::allows('hfa-sign-disposition') && !Gate::allows('hfa-review-disposition') && !Gate::allows('hfa-release-disposition') && Auth::user()->entity_id != 1) {
            return 'Sorry something went wrong.';
        }

        $input = $request->get('inputs');
        parse_str($input, $input);
        $input['step'] = $request->get('step');

        $output = [];

        $user = Auth::user();

        switch ($input['step']) {
            case "disposition-start":
                // create new disposition record if none exist yet or if last one was declined
                $disposition_count = Disposition::where('parcel_id', '=', $parcel->id)->count();
                $last_disposition = Disposition::where('parcel_id', '=', $parcel->id)->orderby('id', 'DESC')->first();

                $disposition_check = 0;

                if ($disposition_count == 0) {
                    $disposition_check = 1;
                } elseif ($last_disposition) {
                    if ($last_disposition->status_id == 5) {
                        // declined
                        $disposition_check = 1;
                    }
                }

                if ($disposition_check) {
                    $disposition = new Disposition([
                        'entity_id' => $parcel->entity_id,
                        'program_id' => $parcel->program_id,
                        'account_id' => $parcel->account_id,
                        'parcel_id' => $parcel->id,
                        'status_id' => 1,
                        'disposition_type_id' => $input['disposition-type'],
                        'active' => 1
                    ]);
                    $disposition->save();

                    // $lc = new LogConverter('Dispositions', 'new');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' created a new disposition.')->save();
                    // set status of parcel to draft for the landbank
                    // Parcel::where('id',$parcel->id)->update(['landbank_property_status_id'=>51]);
                    updateStatus("parcel", $parcel, 'landbank_property_status_id', 51, 0, "");

                    guide_set_progress($disposition->id, 2, $status = 'started', 1); // step 1 - complete form started

                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);

                    session(['next_step'=>'disposition-upload']);
                    $output['next'] = "disposition-open";
                    $output['disposition'] = $disposition->id;
                    $output['message'] = "A new disposition has been created. You can now upload supporting documents and start filling out the request form.";
                } else {
                    $output['next'] = 0;
                    $output['message'] = "This parcel already has a disposition";
                }

                break;





            case "disposition-upload":
                // nothing to do here. all doc uploading using ajax
                break;




            // LB saves the form
            case "disposition-form":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

                if ($disposition) {
                    $imputed_cost_per_parcel = ProgramRule::first(['imputed_cost_per_parcel']);
                    $rule_min_cost = $imputed_cost_per_parcel->imputed_cost_per_parcel;
                    // if($input['transaction_cost'] > $rule_min_cost){
                    //     $transaction_cost = $input['transaction_cost'];
                    // }else{
                    //     $transaction_cost = $rule_min_cost;
                    // }
                    $special = (isset($input['special']) ? $input['special'] : null);
                    //$missing_date = (isset($input['missing_date']) ? $input['missing_date'] : null);
                    $legal_description_in_documents = (isset($input['legal_description_in_documents']) ? $input['legal_description_in_documents'] : null);
                    $description_use_in_documents = (isset($input['description_use_in_documents']) ? $input['description_use_in_documents'] : null);

                    $disposition->update([
                        "program_income" => $input['income'],
                        //"transaction_cost" => $transaction_cost,
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "special_circumstance" => $input['special_circumstance'],
                        "special_circumstance_id" => $special,
                        'disposition_type_id' => $special,
                        "full_description" => $input['full_description'],
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        // "hfa_calc_income" => $input['hfa_calc_income'],
                        // "hfa_calc_trans_cost" => $input['hfa_calc_trans_cost'],
                        // "hfa_calc_maintenance_total" => $input['hfa_calc_maintenance_total'],
                        // "hfa_calc_monthly_rate" => $input['hfa_calc_monthly_rate'],
                        // "missing_date" => $missing_date,
                        // "hfa_calc_months" => $input['hfa_calc_months'],
                        // "hfa_calc_maintenance_due" => $input['hfa_calc_maintenance_due'],
                        // "hfa_calc_demo_cost" => $input['hfa_calc_demo_cost'],
                        // "hfa_calc_epi" => $input['hfa_calc_epi'],
                        // "hfa_calc_payback" => $input['hfa_calc_payback'],
                        // "hfa_calc_gain" => $input['hfa_calc_gain'],
                        "legal_description_in_documents" => $legal_description_in_documents,
                        "description_use_in_documents" => $description_use_in_documents
                    ]);

                    // $lc = new LogConverter('Dispositions', 'saved');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' saved disposition '.$disposition->id)->save();

                    // assuming that this step is completed on the first form save
                    guide_set_progress($disposition->id, 2, $status = 'completed', 1); // step 1 - complete form completed

                    session(['next_step'=>'disposition-form']);
                    $output['next'] = "disposition-form";
                    $output['message'] = "I've updated the form!";
                } else {
                    $output['next'] = 0;
                    $output['message'] = "Something went wrong";
                }

                break;




            // LB send form for LB approval
            case "disposition-submitted":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

                if ($disposition) {
                    $imputed_cost_per_parcel = ProgramRule::first(['imputed_cost_per_parcel']);
                    $rule_min_cost = $imputed_cost_per_parcel->imputed_cost_per_parcel;
                    // if($input['transaction_cost'] > $rule_min_cost){
                    //     $transaction_cost = $input['transaction_cost'];
                    // }else{
                    //     $transaction_cost = $rule_min_cost;
                    // }
                    $special = (isset($input['special']) ? $input['special'] : null);
                    $disposition->update([
                        "program_income" => $input['income'],
                        //"transaction_cost" => $transaction_cost,
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "special_circumstance" => $input['special_circumstance'],
                        "special_circumstance_id" => $special,
                        'disposition_type_id' => $special,
                        "full_description" => $input['full_description'],
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "status_id" => 2
                    ]);

                    // foreach parcel
                    // updateStatus("parcel", $parcel, 'landbank_property_status_id', 49, 0, "");
                    $disposition->parcel()->update([
                        "landbank_property_status_id" => 49 // Disposition Submitted for Internal Approval
                    ]);

                    // session(['next_step'=>'disposition-submitted']);
                    // $output['next'] = "disposition-submitted";
                    // $output['message'] = "I've updated the form!";

                    // check if there are approvers, if not give error
                    $approvals = ApprovalRequest::where('approval_type_id', '=', 1)
                                        ->where('link_type_id', '=', $disposition->id)
                                        ->with('actions')
                                        ->with('actions.action_type')
                                        ->with('approver')
                                        ->get();
                    $has_approvers = 0;
                    foreach ($approvals as $approval) {
                        if ($approval->approver->entity_id != 1) {
                            $has_approvers = 1;
                        }
                    }
                    if (!$has_approvers) {
                        $landbankDispositionApprovers = User::where('entity_id', '=', $disposition->entity_id)
                                        ->where('active', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 11)
                                        ->where('users.active', 1) // make sure the users are active
                                        ->select('id')
                                        ->get();
                        $message_recipients_array = $landbankDispositionApprovers->toArray();
                        try {
                            foreach ($message_recipients_array as $userToNotify) {
                                $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                                $emailNotification = new ApproverNotification($userToNotify, $disposition->id);
                                \Mail::to($current_recipient->email)->send($emailNotification);
                                //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                            }
                        } catch (\Illuminate\Database\QueryException $ex) {
                            dd($ex->getMessage());
                        }

                        session(['next_step'=>'disposition-form']);
                        $output['next'] = "disposition-form";
                        $output['message'] = "I've updated the form, but couldn't submit it for internal approval. Did you select approvers?";
                    } else {
                        // $lc = new LogConverter('Dispositions', 'submitted to LB');
                        // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' submitted disposition '.$disposition->id.' to LB.')->save();

                        // send emails
                        try {
                            foreach ($approvals as $approval) {
                                $userToNotify = $approval->approver->id;
                                $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                                $emailNotification = new ApproverNotification($userToNotify, $disposition->id);
                                \Mail::to($current_recipient->email)->send($emailNotification);
                                //    \Mail::to('jotassin@gmail.com')->send($emailNotification);
                            }
                        } catch (\Exception $ex) {
                            dd($ex->getMessage());
                        }

                        guide_set_progress($disposition->id, 4, $status = 'completed', 1); // step 1 - submitted for approval

                        session(['next_step'=>'disposition-submitted']);
                        $output['next'] = "disposition-submitted";
                        $output['message'] = "I've updated the form and it has been submitted for internal approval.";
                    }
                } else {
                    $output['next'] = 0;
                    $output['message'] = "Something went wrong";
                }
                break;


            // HFA saves the form
            case "disposition-under-review":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();
                if ($disposition) {
                    $imputed_cost_per_parcel = ProgramRule::first(['imputed_cost_per_parcel']);
                    $rule_min_cost = $imputed_cost_per_parcel->imputed_cost_per_parcel;
                    if ($input['transaction_cost'] > $rule_min_cost) {
                        $transaction_cost = $input['transaction_cost'];
                    } else {
                        $transaction_cost = $rule_min_cost;
                    }
                    $special = (isset($input['special']) ? $input['special'] : null);

                    $public_use_political = (isset($input['public_use_political']) ? $input['public_use_political'] : null);
                    $public_use_community = (isset($input['public_use_community']) ? $input['public_use_community'] : null);
                    $public_use_oneyear = (isset($input['public_use_oneyear']) ? $input['public_use_oneyear'] : null);
                    $public_use_facility = (isset($input['public_use_facility']) ? $input['public_use_facility'] : null);
                    $nonprofit_taxexempt = (isset($input['nonprofit_taxexempt']) ? $input['nonprofit_taxexempt'] : null);
                    $nonprofit_community = (isset($input['nonprofit_community']) ? $input['nonprofit_community'] : null);
                    $nonprofit_oneyear = (isset($input['nonprofit_oneyear']) ? $input['nonprofit_oneyear'] : null);
                    $nonprofit_newuse = (isset($input['nonprofit_newuse']) ? $input['nonprofit_newuse'] : null);
                    $dev_oneyear = (isset($input['dev_oneyear']) ? $input['dev_oneyear'] : null);
                    $dev_newuse = (isset($input['dev_newuse']) ? $input['dev_newuse'] : null);
                    $dev_purchaseag = (isset($input['dev_purchaseag']) ? $input['dev_purchaseag'] : null);
                    $dev_taxescurrent = (isset($input['dev_taxescurrent']) ? $input['dev_taxescurrent'] : null);
                    $dev_nofc = (isset($input['dev_nofc']) ? $input['dev_nofc'] : null);
                    $dev_fmv = (isset($input['dev_fmv']) ? $input['dev_fmv'] : null);

                    $calculation = [];
                    $calculation['hfa_calc_income'] = (isset($input['hfa_calc_income']) && $input['hfa_calc_income']!='' ? $input['hfa_calc_income'] : null);
                    $calculation['hfa_calc_trans_cost'] = (isset($input['hfa_calc_trans_cost']) && $input['hfa_calc_trans_cost']!='' ? $input['hfa_calc_trans_cost'] : null);
                    $calculation['hfa_calc_maintenance_total'] = (isset($input['hfa_calc_maintenance_total']) && $input['hfa_calc_maintenance_total']!='' ? $input['hfa_calc_maintenance_total'] : null);
                    $calculation['hfa_calc_monthly_rate'] = (isset($input['hfa_calc_monthly_rate']) && $input['hfa_calc_monthly_rate']!='' ? $input['hfa_calc_monthly_rate'] : null);
                    $calculation['hfa_calc_months'] = (isset($input['hfa_calc_months']) && $input['hfa_calc_months']!='' ? $input['hfa_calc_months'] : null);
                    $calculation['hfa_calc_maintenance_due'] = (isset($input['hfa_calc_maintenance_due']) && $input['hfa_calc_maintenance_due']!='' ? $input['hfa_calc_maintenance_due'] : null);
                    $calculation['hfa_calc_demo_cost'] = (isset($input['hfa_calc_demo_cost']) && $input['hfa_calc_demo_cost']!='' ? $input['hfa_calc_demo_cost'] : null);
                    $calculation['hfa_calc_epi'] = (isset($input['hfa_calc_epi']) && $input['hfa_calc_epi']!='' ? $input['hfa_calc_epi'] : null);
                    $calculation['hfa_calc_payback'] = (isset($input['hfa_calc_payback']) && $input['hfa_calc_payback']!='' ? $input['hfa_calc_payback'] : null);
                    $calculation['hfa_calc_gain'] = (isset($input['hfa_calc_gain']) && $input['hfa_calc_gain']!='' ? $input['hfa_calc_gain'] : null);
                    $calculation['hfa_calc_months_prepaid'] = (isset($input['hfa_calc_months_prepaid']) && $input['hfa_calc_months_prepaid']!='' ? $input['hfa_calc_months_prepaid'] : null);


                    $disposition->update([
                        "program_income" => $input['income'],
                        "transaction_cost" => $transaction_cost,
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "special_circumstance" => $input['special_circumstance'],
                        "special_circumstance_id" => $special,
                        'disposition_type_id' => $special,
                        "full_description" => $input['full_description'],
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "public_use_political" => $public_use_political,
                        "public_use_community" => $public_use_community,
                        "public_use_oneyear" => $public_use_oneyear,
                        "public_use_facility" => $public_use_facility,
                        "nonprofit_taxexempt" => $nonprofit_taxexempt,
                        "nonprofit_community" => $nonprofit_community,
                        "nonprofit_oneyear" => $nonprofit_oneyear,
                        "nonprofit_newuse" => $nonprofit_newuse,
                        "dev_oneyear" => $dev_oneyear,
                        "dev_newuse" => $dev_newuse,
                        "dev_purchaseag" => $dev_purchaseag,
                        "dev_taxescurrent" => $dev_taxescurrent,
                        "dev_nofc" => $dev_nofc,
                        "dev_fmv" => $dev_fmv,
                        "hfa_calc_income" => $calculation['hfa_calc_income'],
                        "hfa_calc_trans_cost" => $calculation['hfa_calc_trans_cost'],
                        "hfa_calc_maintenance_total" => $calculation['hfa_calc_maintenance_total'],
                        "hfa_calc_monthly_rate" => $calculation['hfa_calc_monthly_rate'],
                        "hfa_calc_months" => $calculation['hfa_calc_months'],
                        "hfa_calc_maintenance_due" => $calculation['hfa_calc_maintenance_due'],
                        "hfa_calc_demo_cost" => $calculation['hfa_calc_demo_cost'],
                        "hfa_calc_epi" => $calculation['hfa_calc_epi'],
                        "hfa_calc_payback" => $calculation['hfa_calc_payback'],
                        "hfa_calc_gain" => $calculation['hfa_calc_gain'],
                        "hfa_calc_months_prepaid" => $calculation['hfa_calc_months_prepaid']
                    ]);

                    // $lc = new LogConverter('Dispositions', 'saved');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' saved disposition '.$disposition->id)->save();

                    // make sure step 1 is all done
                    guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - all done
                    guide_set_progress($disposition->id, 7, $status = 'started', 0); // step 2 - confirm calculations
                    guide_set_progress($disposition->id, 8, $status = 'started', 1); // step 2 - review supporting documents

                    session(['next_step'=>'disposition-under-review']);
                    $output['next'] = "disposition-under-review";
                    $output['message'] = "The form has been saved.";
                } else {
                    $output['next'] = 0;
                    $output['message'] = "Something went wrong";
                }


                break;

            // LB saves the form and sends to HFA for approval
            case "disposition-submit-to-hfa":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

                if ($disposition) {
                    $imputed_cost_per_parcel = ProgramRule::first(['imputed_cost_per_parcel']);
                    $rule_min_cost = $imputed_cost_per_parcel->imputed_cost_per_parcel;

                    $transaction_cost = $rule_min_cost;

                    $special = (isset($input['special']) ? $input['special'] : null);

                    $public_use_political = (isset($input['public_use_political']) ? $input['public_use_political'] : null);
                    $public_use_community = (isset($input['public_use_community']) ? $input['public_use_community'] : null);
                    $public_use_oneyear = (isset($input['public_use_oneyear']) ? $input['public_use_oneyear'] : null);
                    $public_use_facility = (isset($input['public_use_facility']) ? $input['public_use_facility'] : null);
                    $nonprofit_taxexempt = (isset($input['nonprofit_taxexempt']) ? $input['nonprofit_taxexempt'] : null);
                    $nonprofit_community = (isset($input['nonprofit_community']) ? $input['nonprofit_community'] : null);
                    $nonprofit_oneyear = (isset($input['nonprofit_oneyear']) ? $input['nonprofit_oneyear'] : null);
                    $nonprofit_newuse = (isset($input['nonprofit_newuse']) ? $input['nonprofit_newuse'] : null);
                    $dev_oneyear = (isset($input['dev_oneyear']) ? $input['dev_oneyear'] : null);
                    $dev_newuse = (isset($input['dev_newuse']) ? $input['dev_newuse'] : null);
                    $dev_purchaseag = (isset($input['dev_purchaseag']) ? $input['dev_purchaseag'] : null);
                    $dev_taxescurrent = (isset($input['dev_taxescurrent']) ? $input['dev_taxescurrent'] : null);
                    $dev_nofc = (isset($input['dev_nofc']) ? $input['dev_nofc'] : null);
                    $dev_fmv = (isset($input['dev_fmv']) ? $input['dev_fmv'] : null);

                    $calculation = [];
                    $calculation['hfa_calc_income'] = (isset($input['hfa_calc_income']) && $input['hfa_calc_income']!='' ? $input['hfa_calc_income'] : null);
                    $calculation['hfa_calc_trans_cost'] = (isset($input['hfa_calc_trans_cost']) && $input['hfa_calc_trans_cost']!='' ? $input['hfa_calc_trans_cost'] : null);
                    $calculation['hfa_calc_maintenance_total'] = (isset($input['hfa_calc_maintenance_total']) && $input['hfa_calc_maintenance_total']!='' ? $input['hfa_calc_maintenance_total'] : null);
                    $calculation['hfa_calc_monthly_rate'] = (isset($input['hfa_calc_monthly_rate']) && $input['hfa_calc_monthly_rate']!='' ? $input['hfa_calc_monthly_rate'] : null);
                    $calculation['hfa_calc_months'] = (isset($input['hfa_calc_months']) && $input['hfa_calc_months']!='' ? $input['hfa_calc_months'] : null);
                    $calculation['hfa_calc_maintenance_due'] = (isset($input['hfa_calc_maintenance_due']) && $input['hfa_calc_maintenance_due']!='' ? $input['hfa_calc_maintenance_due'] : null);
                    $calculation['hfa_calc_demo_cost'] = (isset($input['hfa_calc_demo_cost']) && $input['hfa_calc_demo_cost']!='' ? $input['hfa_calc_demo_cost'] : null);
                    $calculation['hfa_calc_epi'] = (isset($input['hfa_calc_epi']) && $input['hfa_calc_epi']!='' ? $input['hfa_calc_epi'] : null);
                    $calculation['hfa_calc_payback'] = (isset($input['hfa_calc_payback']) && $input['hfa_calc_payback']!='' ? $input['hfa_calc_payback'] : null);
                    $calculation['hfa_calc_gain'] = (isset($input['hfa_calc_gain']) && $input['hfa_calc_gain']!='' ? $input['hfa_calc_gain'] : null);
                    $calculation['hfa_calc_months_prepaid'] = (isset($input['hfa_calc_months_prepaid']) && $input['hfa_calc_months_prepaid']!='' ? $input['hfa_calc_months_prepaid'] : null);

                    $disposition->update([
                        "program_income" => $input['income'],
                        "transaction_cost" => $transaction_cost,
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "special_circumstance" => $input['special_circumstance'],
                        "special_circumstance_id" => $special,
                        'disposition_type_id' => $special,
                        "full_description" => $input['full_description'],
                        "permanent_parcel_id" => $input['permanentparcelid'],
                        "public_use_political" => $public_use_political,
                        "public_use_community" => $public_use_community,
                        "public_use_oneyear" => $public_use_oneyear,
                        "public_use_facility" => $public_use_facility,
                        "nonprofit_taxexempt" => $nonprofit_taxexempt,
                        "nonprofit_community" => $nonprofit_community,
                        "nonprofit_oneyear" => $nonprofit_oneyear,
                        "nonprofit_newuse" => $nonprofit_newuse,
                        "dev_oneyear" => $dev_oneyear,
                        "dev_newuse" => $dev_newuse,
                        "dev_purchaseag" => $dev_purchaseag,
                        "dev_taxescurrent" => $dev_taxescurrent,
                        "dev_nofc" => $dev_nofc,
                        "dev_fmv" => $dev_fmv,
                        "hfa_calc_income" => $calculation['hfa_calc_income'],
                        "hfa_calc_trans_cost" => $calculation['hfa_calc_trans_cost'],
                        "hfa_calc_maintenance_total" => $calculation['hfa_calc_maintenance_total'],
                        "hfa_calc_monthly_rate" => $calculation['hfa_calc_monthly_rate'],
                        "hfa_calc_months" => $calculation['hfa_calc_months'],
                        "hfa_calc_maintenance_due" => $calculation['hfa_calc_maintenance_due'],
                        "hfa_calc_demo_cost" => $calculation['hfa_calc_demo_cost'],
                        "hfa_calc_epi" => $calculation['hfa_calc_epi'],
                        "hfa_calc_payback" => $calculation['hfa_calc_payback'],
                        "hfa_calc_gain" => $calculation['hfa_calc_gain'],
                        "hfa_calc_months_prepaid" => $calculation['hfa_calc_months_prepaid']
                    ]);
                    session(['next_step'=>'disposition-under-review']);
                    $output['next'] = "disposition-under-review";
                    $output['message'] = "I've updated the form!";

                    $disposition->update([
                        "status_id" => 3
                    ]);
                    $parcel->update([
                        "landbank_property_status_id" => 15,
                        "hfa_property_status_id" => 29
                    ]);

                    // $lc = new LogConverter('Dispositions', 'sent to HFA');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' sent the disposition '.$disposition->id.' to HFA for approval.')->save();

                    // Send email notification to LB

                    $recipients = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 9) // Reviews dispositions for HFA
                                        ->where('users.active', 1) // make sure the users are active
                                        ->select('id')
                                        ->get();
                    $message_recipients_array = $recipients->toArray();
                    try {
                        foreach ($message_recipients_array as $userToNotify) {
                            $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                            $emailNotification = new EmailNotificationDispositionReview($userToNotify, null, $disposition->id);
                            \Mail::to($current_recipient->email)->send($emailNotification);
                            //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        dd($ex->getMessage());
                    }

                    guide_set_progress($disposition->id, 5, $status = 'completed', 0); // step 1 - submitted to HFA
                    // all step 1 is done
                    guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1

                    session(['next_step'=>'disposition-under-review']);
                    $output['next'] = "disposition-under-review";
                    $output['message'] = "The form has been saved.";
                } else {
                    $output['next'] = 0;
                    $output['message'] = "Something went wrong";
                }


                break;

            // approve and add to invoice
            case "disposition-approve":
                if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
                    $output['message'] = 'Sorry, either you are no longer an approver, or no  longer an HFA admin. Both roles are required to approve a disposition.';
                    return $output;
                }
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();
                if ($disposition) {
                    // check if disposition already in an invoice
                    if (DispositionsToInvoice::where('disposition_id', '=', $disposition->id)->count() > 0) {
                        return ['message'=>"Oops, looks like that disposition is already in an invoice...", "error"=>1];
                    }

                    // get current invoice
                    $current_invoice = DispositionInvoice::where('entity_id', '=', $disposition->entity_id)
                                ->where('account_id', '=', $disposition->account_id)
                                ->where('program_id', '=', $disposition->program_id)
                                ->where('active', '=', 1)
                                ->where('status_id', '=', 1)
                                ->first();

                    // if no invoice, create one
                    if (!$current_invoice) {
                        $current_invoice = new DispositionInvoice([
                                        'entity_id' => $disposition->entity_id,
                                        'program_id' => $disposition->program_id,
                                        'account_id' => $disposition->account_id,
                                        'status_id' => 1,
                                        'active' => 1
                        ]);
                        $current_invoice->save();

                        // $lc = new LogConverter('disposition_invoices', 'create');
                        // $lc->setFrom(Auth::user())->setTo($current_invoice)->setDesc(Auth::user()->email . 'Created a new disposition invoice draft')->save();
                    }

                    // calculate total maintenance & total of net proceeds owed
                    // save as two disposition_items
                    $maintenance_array = $this->getUnusedMaintenance($parcel);
                    $income = $disposition->program_income;
                    $transaction_cost = $disposition->transaction_cost;
                    $maintenance_total = $this->getMaintenanceTotal($parcel);

                    // payback
                    $request_for_recapture = new Request;
                    $request_for_recapture->request->add(['income'=>$income, 'cost'=>$transaction_cost]);
                    $payback = $this->computeRecaptureOwed($disposition, $request_for_recapture);


                    // check against the rules
                    $rules = ProgramRule::first(['imputed_cost_per_parcel','maintenance_max','maintenance_recap_pro_rate','demolition_max']);
                    $imputed_cost_per_parcel = $rules->imputed_cost_per_parcel; //200
                    $maintenance_max = $rules->maintenance_max; //1200
                    $maintenance_recap_pro_rate = $rules->maintenance_recap_pro_rate; //36
                    if ($disposition->hfa_calc_months_prepaid != null) {
                        $maintenance_recap_pro_rate = $disposition->hfa_calc_months_prepaid;
                    }
                    $demolition_max = $rules->demolition_max;

                    if (!is_null($disposition->hfa_calc_maintenance_total)) {
                        $maintenance_total = $disposition->hfa_calc_maintenance_total;
                    }
                    if (!is_null($disposition->hfa_calc_income)) {
                        $income = $disposition->hfa_calc_income;
                    }
                    if (!is_null($disposition->hfa_calc_trans_cost)) {
                        $transaction_cost = $disposition->hfa_calc_trans_cost;
                    }
                    if (!is_null($disposition->hfa_calc_demo_cost)) {
                        $demolition = $disposition->hfa_calc_demo_cost;
                    }
                    if (!is_null($disposition->hfa_calc_epi)) {
                        $eligible_income = $disposition->hfa_calc_epi;
                    }
                    if (!is_null($disposition->hfa_calc_payback)) {
                        $payback = $disposition->hfa_calc_payback;
                    }
                    if (!is_null($disposition->hfa_calc_gain)) {
                        $gain = $disposition->hfa_calc_gain;
                    }
                    if (!is_null($disposition->hfa_calc_monthly_rate)) {
                        $monthly_rate = $disposition->hfa_calc_monthly_rate;
                    } elseif (!is_null($disposition->hfa_calc_maintenance_total) && $disposition->hfa_calc_maintenance_total != 0) {
                        $monthly_rate = $disposition->hfa_calc_maintenance_total / $maintenance_recap_pro_rate;
                    } else {
                        $monthly_rate = $maintenance_array['monthly_rate'];
                    }

                    if (!is_null($disposition->hfa_calc_maintenance_due)) {
                        $maintenance_to_repay = $disposition->hfa_calc_maintenance_due;
                    } else {
                        $maintenance_to_repay = $maintenance_total - $maintenance_array['hfa_months']*$monthly_rate;
                    }

                    if ($maintenance_to_repay < 0) {
                        $maintenance_to_repay = 0;
                    }

                    if (!is_null($disposition->hfa_calc_payback)) {
                        $payback = $disposition->hfa_calc_payback;
                    } else {
                        if ($eligible_income > $demolition) {
                            $payback = $demolition + $maintenance_to_repay;
                        } else {
                            $payback = $eligible_income + $maintenance_to_repay;
                        }
                    }

                    $netProceeds = $payback - $maintenance_to_repay;
                    $maintenanceRecapOwed = $maintenance_to_repay;

                    if ($maintenanceRecapOwed > 0) {
                        $new_disposition_item = new DispositionItems([
                            'breakout_type' => 2,
                            'disposition_id' => $disposition->id,
                            'disposition_invoice_id'=>$current_invoice->id,
                            'parcel_id' => $disposition->parcel_id,
                            'account_id' => $disposition->account_id,
                            'program_id' => $disposition->program_id,
                            'entity_id' => $disposition->entity_id,
                            'expense_category_id' => 6,  //mainteancne
                            'amount' => $maintenanceRecapOwed,
                            'vendor_id' => 1,
                            'description' => '',
                            'notes' => ''
                        ]);
                        $new_disposition_item->save();
                    }
                    if ($netProceeds > 0.00) {
                        $new_disposition_item = new DispositionItems([
                            'breakout_type' => 2,
                            'disposition_id' => $disposition->id,
                            'disposition_invoice_id'=>$current_invoice->id,
                            'parcel_id' => $disposition->parcel_id,
                            'account_id' => $disposition->account_id,
                            'program_id' => $disposition->program_id,
                            'entity_id' => $disposition->entity_id,
                            'expense_category_id' => 8,
                            'amount' => $netProceeds,
                            'vendor_id' => 1,
                            'description' => '',
                            'notes' => ''
                        ]);
                        $new_disposition_item->save();
                    }

                    // add disposition to invoice
                    $disposition_to_invoice = new DispositionsToInvoice([
                                        'disposition_id' => $disposition->id,
                                        'disposition_invoice_id' => $current_invoice->id
                    ]);
                    $disposition_to_invoice->save();

                    // change parcel's status ids and disposition status id
                    $disposition->update([
                        "status_id" => 4
                    ]);
                    $parcel->update([
                        "landbank_property_status_id" => 16,
                        "hfa_property_status_id" => 31 // invoiced
                    ]);
                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);

                    // $lc = new LogConverter('Dispositions', 'added to invoice');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' added disposition '.$disposition->id.' to invoice '.$current_invoice->id)->save();

                    // request approved
                    // make sure step 1 is all done
                    guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - all done

                    guide_set_progress($disposition->id, 7, $status = 'completed', 0); // step 2 - calculations
                    guide_set_progress($disposition->id, 8, $status = 'completed', 0); // step 2 - docs
                    guide_set_progress($disposition->id, 9, $status = 'completed', 0); // step 2 - approve request
                    guide_set_progress($disposition->id, 12, $status = 'completed', 0); // step 2 - added to invoice

                    // notify LB that the disposition has been approved
                    $landbankDispositionManagers = User::where('entity_id', '=', $disposition->entity_id)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 12)
                                    ->where('users.active', 1) // make sure the users are active
                                    ->select('id')
                                    ->get();
                    $message_recipients_array = $landbankDispositionManagers->toArray();
                    try {
                        foreach ($message_recipients_array as $userToNotify) {
                            $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                            $emailNotification = new DispositionApprovedNotification($userToNotify, $disposition->id);
                            \Mail::to($current_recipient->email)->send($emailNotification);
                            //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        dd($ex->getMessage());
                    }

                    guide_set_progress($disposition->id, 10, $status = 'completed', 1); // step 2 - landbank notified

                    session(['next_step'=>'disposition-under-review']);
                    $output['next'] = "disposition-under-review";
                    $output['message'] = "The form has been saved.";
                } else {
                    $output['next'] = 0;
                    $output['message'] = "Something went wrong";
                }

                break;

            case "disposition-release-requested":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

                if ($disposition) {
                    $disposition->update([
                        'date_release_requested' => Carbon\Carbon::today()->toDateTimeString()
                    ]);
                    $parcel->update([
                        "landbank_property_status_id" => 51,
                        "hfa_property_status_id" => 52 // release requested
                    ]);
                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);

                    // $lc = new LogConverter('Dispositions', 'release requested');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' requested the release for disposition '.$disposition->id)->save();

                    // Send email notification to HFA
                    $recipients = User::where('entity_id', '=', 1)
                                        ->where('active', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 28) // notified of lien release request
                                        ->select('id')
                                        ->get();
                    $message_recipients_array = $recipients->toArray();
                    try {
                        foreach ($message_recipients_array as $userToNotify) {
                            $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                            $emailNotification = new EmailNotificationDispositionReleaseRequested($userToNotify, null, $disposition->id);
                            \Mail::to($current_recipient->email)->send($emailNotification);
                            //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        dd($ex->getMessage());
                    }

                    // make sure step 1 is all done
                    guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - all done
                    guide_set_progress($disposition->id, 11, $status = 'completed', 1); // step 2 - request lien release

                    session(['next_step'=>'disposition-release-requested']);
                    $output['next'] = "disposition-release-requested";
                    $output['message'] = "The release was requested successfully.";
                }

                break;

            case "disposition-released":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

                if ($disposition) {
                    if ($request->get('release_date')) {
                        $release_date = $request->get('release_date');
                        $date = new DateTime($release_date);
                        $date = $date->format('Y-m-d H:i:s');

                        $disposition->update([
                            'release_date' => $date
                        ]);
                    } else {
                        $disposition->update([
                            'release_date' => Carbon\Carbon::today()->toDateTimeString()
                        ]);
                    }

                    $parcel->update([
                        "landbank_property_status_id" => 17,
                        "hfa_property_status_id" => 33 // released
                    ]);
                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);

                    // $lc = new LogConverter('Dispositions', 'released');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' released disposition '.$disposition->id)->save();

                    // make sure step 1 & 2 all done
                    guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - all done
                    guide_set_progress($disposition->id, 6, $status = 'completed', 0); // step 2 - all done
                    guide_set_progress($disposition->id, 14, $status = 'completed', 1); // step 3 - lien released

                    session(['next_step'=>'disposition-released']);
                    $output['next'] = "disposition-released";
                    $output['message'] = "The disposition was released successfully.";
                }

                break;

            case "disposition-decline":
                $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

                if ($disposition) {
                    $disposition->update([
                        'status_id' => 5 // declined
                    ]);

                    updateStatus("parcel", $parcel, 'landbank_property_status_id', 14, 0, "");      // Paid by HFA
                    updateStatus("parcel", $parcel, 'hfa_property_status_id', 28, 0, "");           // Paid

                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);

                    // $lc = new LogConverter('Dispositions', 'declined');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' declined disposition '.$disposition->id)->save();
                    session(['next_step'=>'disposition-decline']);
                    $output['next'] = "disposition-decline";
                    $output['message'] = "The disposition was declined.";
                }

                break;
            default:
                $output['next'] = "0";
                $output['message'] = "Something went wrong.";
        }

        return $output;
    }


    public function removeDispositionFromInvoice(disposition_invoices $invoice, Request $request)
    {
        if (!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        $disposition_id = $request->get('disposition');

        $disposition = Disposition::where("id", "=", $disposition_id)->with('invoice')->first();

        if ($disposition) {
            $invoice_id = $disposition->invoice->disposition_invoice_id;

            // remove disposition_items
            if (DispositionItems::where('disposition_id', '=', $disposition->id)->count() > 0) {
                $items = DispositionItems::where('disposition_id', '=', $disposition->id)->delete();
            }

            // remove disposition to invoice
            if (DispositionsToInvoice::where('disposition_id', '=', $disposition->id)->count() > 0) {
                $d2i = DispositionsToInvoice::where('disposition_id', '=', $disposition->id)->delete();
            }
            // change the disposition status
            $disposition->update([
                "status_id" => 3
            ]);

            // change the parcel statuses
            $disposition->parcel->update([
                "landbank_property_status_id" => 15,
                "hfa_property_status_id" => 30
            ]);
            perform_all_parcel_checks($disposition->parcel);
            guide_next_pending_step(2, $disposition->parcel->id);

            // log converter
            // $lc=new LogConverter('dispositions', 'Removed from invoice');
            // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' removed disposition ' . $disposition->id.' from invoice '.$invoice_id)->save();

            // step 2 isn't completed anymore
            guide_set_progress($disposition->id, 6, $status = 'started', 0); // step 2 not all done
            guide_set_progress($disposition->id, 12, $status = 'started', 1); // step 2 not added to invoice

            $output['message'] = "The disposition has been removed from the invoice.";
            return $output;
        }
    }

    /**
     * Create approvers
     *
     * @param  $parcel, $request
     * @return Response
     */
    public function addApprover(Parcel $parcel, Request $request)
    {
        if ((!Auth::user()->isLandbankDispositionApprover() && !Auth::user()->isHFAAdmin()) || !Auth::user()->isActive()) {
            $output['message'] = 'Sorry, either that user is not a disposition approver or is no longer active. Both are required to be added as an approver.';
            return $output;
        }

        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = $request->get('user_id');
            if (!ApprovalRequest::where('approval_type_id', '=', 1)
                        ->where('link_type_id', '=', $disposition->id)
                        ->where('user_id', '=', $approver_id)
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 1,
                    "link_type_id" => $disposition->id,
                    "user_id" => $approver_id
                ]);
                $newApprovalRequest->save();
                // $lc = new LogConverter('dispositions', 'add.lb.approver');
                // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . 'added an approver.')->save();

                // send emails
                try {
                    $current_recipient = User::where('id', '=', $approver_id)->get()->first();
                    $emailNotification = new ApproverNotification($current_recipient, $disposition->id);
                    \Mail::to($current_recipient->email)->send($emailNotification);
                    //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                }

                $data['message'] = $current_recipient->name.' the approver was added. All hail the approver!';
                return $data;
            } else {
                $data['message'] = 'Huh, looks like they were added already... did you by chance double click the add button?';
                return $data;
            }
        } else {
            $data['message'] = 'Well, this is weird. The disposition you are adding this person to... I cannot find it. Try reloading the page, and if that does not work, let my friends at Greenwood 360 know.';
            return $data;
        }
    }

    public function addHFAApprover(Parcel $parcel, Request $request)
    {
        if ((!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) || !Auth::user()->isActive()) {
            $output['message'] = 'Sorry either that user is not a HFA Disposition Approver or no longer an HFA Admin. Both are required roles to be added as approver to this disposition.';
            return $output;
        }

        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = $request->get('user_id');
            if (!ApprovalRequest::where('approval_type_id', '=', 11)
                        ->where('link_type_id', '=', $disposition->id)
                        ->where('user_id', '=', $approver_id)
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 11,
                    "link_type_id" => $disposition->id,
                    "user_id" => $approver_id
                ]);
                $newApprovalRequest->save();
                // $lc = new LogConverter('disposition', 'add.hfa.approver');
                // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . 'added a HFA approver.')->save();

                $data['message'] = 'The approver was added.';
                return $data;
            } else {
                $data['message'] = 'Something went wrong.';
                return $data;
            }
        } else {
            $data['message'] = 'I am so sorry, I cannot find the disposition this approver is supposed to be added to... please try refreshing the page and trying again.';
            return $data;
        }
    }

    /**
     * Remove approvers
     *
     * @param  $parcel, $request
     * @return Response
     */
    public function removeApprover(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = $request->get('id');
            $approver = ApprovalRequest::where('approval_type_id', '=', 1)
                            ->where('link_type_id', '=', $disposition->id)
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

    /**
    * Remove approvers
    *
    * @param  $parcel, $request
    * @return Response
    */
    public function removeHFAApprover(Parcel $parcel, Request $request)
    {
        if ((!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) || !Auth::user()->isActive()) {
            $output['message'] = 'Sorry, you do not appear to have permission to remove the approvers.';
            return $output;
        }

        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = $request->get('id');
            $approver = ApprovalRequest::where('approval_type_id', '=', 11)
                            ->where('link_type_id', '=', $disposition->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $approver->delete();

            $data['message'] = '';
            $data['id'] = $request->get('id');
            return $data;
        } else {
            $data['message'] = 'I cannot find the disposition this approver was attached to in order to remove them. Please try refreshing the page and trying again.';
            $data['id'] = null;
            return $data;
        }
    }

    public function approve(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = Auth::user()->id;
            if (Auth::user()->isActive()) {
                $approver = ApprovalRequest::where('approval_type_id', '=', 1)
                                ->where('link_type_id', '=', $disposition->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                $action = new ApprovalAction([
                            'approval_request_id' => $approver->id,
                            'approval_action_type_id' => 1
                        ]);
                $action->save();

                $data['message'] = 'Your approval was recorded, stamped, and put in the history books. I know, it sounds pretty epic when I put it that way.';
                $data['id'] = $approver_id;
                return $data;
            } else {
                $data['message'] = 'Sorry, but does not look like your user is active any longer.';
                $data['id'] = null;
                return $data;
            }
        } else {
            $data['message'] = 'Oh no, I cannot find the disposition this is tied to... please try refreshing the page to approve it.';
            $data['id'] = null;
            return $data;
        }
    }

    public function approveHFA(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = Auth::user()->id;
            if (Auth::user()->isActive()) {
                $approver = ApprovalRequest::where('approval_type_id', '=', 11)
                                ->where('link_type_id', '=', $disposition->id)
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
                $data['message'] = 'Sorry, but does not look like your user is active any longer. Check with your manager, I am not even supposed to talk to you now. Harsh, I know.';
                $data['id'] = null;
                return $data;
            }
        } else {
            $data['message'] = 'OK, something went wrong because I cannot find the disposition you are trying to approve. Maybe try refreshing the page and give it another go.';
            $data['id'] = null;
            return $data;
        }
    }

    public function decline(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 1)
                            ->where('link_type_id', '=', $disposition->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();

            $data['message'] = 'This disposition has been declined. It pains me too, but we gotta stick to our guns, right?';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Well that didn\'t work. I cannot find the disposition you are trying to decline. Maybe try refreshing the page and give it another go.';
            $data['id'] = null;
            return $data;
        }
    }

    public function declineHFA(Parcel $parcel, Request $request)
    {
        // get disposition for parcel
        $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();

        if ($disposition) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 11)
                            ->where('link_type_id', '=', $disposition->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();

            $data['message'] = 'This disposition has been declined. It is never fun to decline anything... but we must be diligent!';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Well crud... I cannot find the disposition you are trying to decline. Maybe try refreshing the page and see if it works this time.';
            $data['id'] = null;
            return $data;
        }
    }

    public function addMissingDate(Parcel $parcel, Request $request)
    {
        $missing_date = $request->get('date');
        $date = new DateTime($missing_date);
        $date = $date->format('Y-m-d H:i:s');
        if ($date) {
            $invoiceid = ParcelsToReimbursementInvoice::where('parcel_id', '=', $parcel->id)->first();
            $transaction = Transaction::where('type_id', '=', 1)
                                ->where('link_to_type_id', '=', $invoiceid->reimbursement_invoice_id)
                                ->where('status_id', '=', 2)
                                ->first();
            if ($transaction->date_entered == "0000-00-00") {
                $transaction->update([
                    "date_entered" => $date,
                    "date_cleared" => $date
                ]);
            }

            $disposition = Disposition::where('parcel_id', '=', $parcel->id)->first();
            if ($disposition->status_id == 15 || $disposition->status_id == 16) {
                session(['next_step'=>'disposition-under-review']);
                $output['next'] = "disposition-under-review";
            } else {
                session(['next_step'=>'disposition-form']);
                $output['next'] = "disposition-form";
            }

            $output['message'] = "I've added the payment date for this parcel! Getting things done feels good!";
        } else {
            $output['message'] = '';
        }
        return $output;
    }

    public function goToDisposition($dispositionid) //TBD
    {
        // is user in the recipient list for this specific message?
        // $user = Auth::user();
        // $disposition = Disposition::where('id',$dispositionid)->get()->first();
        // if(count($disposition)){
        //     if(CommunicationRecipient::where('communication_id', '=', $message->id)->where('user_id', '=', $user->id)->exists() || $message->owner_id == $user->id){
        //         //prevents the UIkit notify to show up after reading the message
        //         $user_needs_to_read_more = CommunicationRecipient::where('communication_id', $message->id)->where('user_id',$user->id)->where('seen',0)->update(['seen' => 1]);
        //         session(['open_parcel'=>$message->parcel_id, 'parcel_subtab'=>'communications','dynamicModalLoad'=>$message->id]);

        //         return redirect('/');
        //     }
        // }
        // session(['open_parcel'=>'', 'parcel_subtab'=>'','dynamicModalLoad'=>'']);
        // $message = "You are not authorized to view this message.";
        // $error = "Looks like you are trying to access a message not sent to you.";
        // $type = "danger";
        // return view('pages.error', compact('error','message','type'));
    }

    public function viewInvoice(DispositionInvoice $invoice)
    {
        if (!Gate::allows('view-disposition')) {
            return 'Sorry you do not have access to the invoice.';
        }

        setlocale(LC_MONETARY, 'en_US');

        $invoice->load('status')
                ->load('dispositions')
                ->load('dispositions.parcel')
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

        foreach ($invoice->dispositions as $disposition) {
            $disposition->total = $disposition->total();

            // check if total is $0 and adjust status accordingly (if $0 and there are dispositions mark as paid)
            if ($disposition->status_id != 6 && $disposition->total == 0) {
                // load the disposition to update (we added some columns so we have to load another instance)
                $disposition_to_update = Disposition::where('id', '=', $disposition->id)->first();

                $disposition_to_update->update([
                    "status_id" => 6,

                ]);

                // also update current instance
                $disposition->status_id = 6;
            }

            if ($disposition->parcel) {
                // $disposition->parcel->update([
                //         "landbank_property_status_id" => 15,
                //         "hfa_property_status_id" => 29
                // ]);
            //    perform_all_parcel_checks($disposition->parcel);
            //    guide_next_pending_step(2, $disposition->parcel->id);
            }


            $disposition->total_formatted = money_format('%n', $disposition->total());
            $total = $total + $disposition->total;
            if ($disposition->sf_parcel_id === null) {
                $legacy = 0;
            }

            // used to display release buttons on invoice
            if ($disposition->date_release_requested == null && $disposition->release_date == null) {
                $display_request_button = 1;
            }
            if ($disposition->date_release_requested != null && $disposition->release_date == null) {
                $display_release_button = 1;
            }

            // perform_all_disposition_checks($disposition);
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

        // $lc = new LogConverter('disposition_invoices', 'view');
        // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'Viewed disposition invoice')->save();

        $nip = Entity::where('id', 1)->with('state')->with('user')->first();

        //DispositionInvoiceNote
        $dispositions = $invoice->dispositions;

        // get disposition types for step 1 dropdown
        $types = DispositionType::where('active', 1)->orderBy('disposition_type_name', 'asc')->get();

        $isApprover = 0;

        // get approvers (type id 12 is for disposition invoices)
        $dispositionInvoiceApprovers = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 27)
                                        ->where('users.active', 1)
                                        ->select('users.id', 'users.name')
                                        ->get();

        $added_approvers = ApprovalRequest::where('approval_type_id', '=', 12)
                                ->where('link_type_id', '=', $invoice->id)
                                ->pluck('user_id as id');

        $pending_approvers = [];

        if (count($added_approvers) == 0 && count($dispositionInvoiceApprovers) > 0) {
            foreach ($dispositionInvoiceApprovers as $dispositionInvoiceApprover) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 12,
                    "link_type_id" => $invoice->id,
                    "user_id" => $dispositionInvoiceApprover->id
                ]);
                $newApprovalRequest->save();
            }
        } elseif (count($dispositionInvoiceApprovers) > 0) {
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
        $approval_status = guide_approval_status(12, $invoice->id);
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

        // if invoice is fully paid, make sure all dispositions in it is marked as such
        if ($invoice->status_id == 6) {
            foreach ($invoice->dispositions as $disposition) {
                // make a copy of the disposition because we extended the collection with additional fields not in the model
                $updated_disposition = Disposition::where('id', '=', $disposition->id);
                $updated_disposition->update(['status_id'=>6]); // Paid
                $disposition->status_id = 6;
            }
            // make sure the invoice itself is marked as paid
            \App\Models\DispositionInvoice::where('id', $invoice->id)->update(['paid' => 1]);
        }

        if (($isReadyForPayment === 1 || $legacy) && $balance > 0) {
            // after invoice was sent to fiscal agent, its status is 8 (submitted to fiscal agent)
            if ($invoice->status_id != 8) {
                $invoice->update(['status_id'=>7]); // Approved
                $invoice->status_id = 7;
                $invoice->load('status');
                // update dispositions' status
                foreach ($invoice->dispositions as $disposition) {
                    // make a copy of the disposition because we extended the collection with additional fields not in the model
                    $updated_disposition = Disposition::where('id', '=', $disposition->id);
                    $updated_disposition->update(['status_id'=>4]); // Pending Payment
                    $disposition->status_id = 4;
                }

                $invoice->load('dispositions');
            }
        } elseif ($legacy && count($invoice->transactions) && $balance > 0) {
            $invoice->update(['status_id'=>4,'paid'=>null]); // Pending payment
            $invoice->status_id = 4;
            $invoice->paid = null;
            $invoice->load('status');
        } elseif (($isReadyForPayment === 1 || $legacy) && $balance < .01 && count($invoice->dispositions) > 0) {
            // for each disposition, change the status to Paid as well
            foreach ($invoice->dispositions as $disposition) {
                // make a copy of the disposition because we extended the collection with additional fields not in the model
                $updated_disposition = Disposition::where('id', '=', $disposition->id);
                $updated_disposition->update(['status_id'=>6]); // Paid
                $disposition->status_id = 6;
            }

            $invoice->update(['status_id'=>6, 'paid' => 1]); // Paid
            $invoice->status_id = 6;
            $invoice->paid = 1;
            $invoice->load('status');
            $invoice->load('dispositions'); // reload to make sure they show latest status
        }

        // refresh invoice data to use current status
        //$invoice = DispositionInvoice::find($invoice->id); //if we reload, we will loose some of the work done above (formatted total, etc)

        return view('pages.disposition_invoice', compact('invoice', 'dispositions', 'notes', 'balance', 'nip', 'hasApprovals', 'isApprover', 'isDeclined', 'approvals', 'pending_approvers', 'isApproved', 'isReadyForPayment', 'total', 'balance', 'stat', 'legacy', 'display_request_button', 'display_release_button'));
        // return view('pages.disposition_invoice', compact('isApprover', 'isApprover_hfa', 'parcel','types','proceed','disposition','document_categories','nip','entity','approvals','approvals_hfa','calculation','actual', 'supporting_documents','current_user', 'isApproved', 'isApproved_hfa', 'pending_approvers', 'pending_approvers_hfa', 'landbankRequestApprovers', 'step'));
    }

    public function submitForApproval(DispositionInvoice $invoice, Request $request)
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

                // $lc = new LogConverter('disposition_invoices', 'submitted for approval');
                // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' submitted the disposition invoice '.$invoice->id.' for approval')->save();

                $output['message'] = "The disposition invoice has been submitted for approval.";

                foreach ($invoice->dispositions as $disposition) {
                    guide_set_progress($disposition->id, 56, $status = 'completed'); // invoice submitted for approval
                }
            } else {
                $output['message'] = "Nothing to do here.";
            }
        } else {
            $output['message'] = "Something is wrong, I couldn't find a valid invoice.";
        }
        return $output;
    }

    public function requestRelease(DispositionInvoice $invoice, Request $request)
    {
        if ($invoice) {
            $disposition_id = $request->get('disposition_id');

            // Send email notification to LB
            $recipients = User::where('entity_id', '=', 1)
                                ->where('active', '=', 1)
                                ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                ->where('users_roles.role_id', '=', 28) // notified of lien release request
                                ->select('id')
                                ->get();
            $message_recipients_array = $recipients->toArray();

            if ($disposition_id == 0) {
                $sendemail = 0;
                // all dispositions from this invoice should be requested
                foreach ($invoice->dispositions as $disposition) {
                    // only if the disposition doesn't already have a release request date
                    if ($disposition->date_release_requested == null) {
                        $disposition->update([
                            'date_release_requested' => Carbon\Carbon::today()->toDateTimeString()
                        ]);
                        $disposition->parcel->update([
                            "landbank_property_status_id" => 51,
                            "hfa_property_status_id" => 52 // release requested
                        ]);
                        perform_all_parcel_checks($disposition->parcel);
                        guide_next_pending_step(2, $disposition->parcel->id);

                        // $lc = new LogConverter('Dispositions', 'release requested');
                        // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' requested the release for disposition '.$disposition->id)->save();

                        $sendemail = 1;
                    }
                }
                if ($sendemail) {
                    // only send emails if there were some actual requests done
                    try {
                        foreach ($message_recipients_array as $userToNotify) {
                            $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                            $emailNotification = new EmailNotificationDispositionReleaseRequested($userToNotify, $invoice->id, 0);
                            \Mail::to($current_recipient->email)->send($emailNotification);
                            //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        dd($ex->getMessage());
                    }
                }
            } else {
                // only that particular disposition should have the request
                $disposition = Disposition::where('id', '=', $disposition_id)->first();

                if ($disposition) {
                    $disposition->update([
                        'date_release_requested' => Carbon\Carbon::today()->toDateTimeString()
                    ]);
                    $disposition->parcel->update([
                        "landbank_property_status_id" => 51,
                        "hfa_property_status_id" => 52 // release requested
                    ]);
                    perform_all_parcel_checks($disposition->parcel);
                    guide_next_pending_step(2, $disposition->parcel->id);

                    // $lc = new LogConverter('Dispositions', 'release requested');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' requested the release for disposition '.$disposition->id)->save();

                    try {
                        foreach ($message_recipients_array as $userToNotify) {
                            $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                            $emailNotification = new EmailNotificationDispositionReleaseRequested($userToNotify, $invoice->id, $disposition_id);
                            \Mail::to($current_recipient->email)->send($emailNotification);
                            //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        dd($ex->getMessage());
                    }
                }
            }


            $output['message'] = "I have requested the release. Enjoy the rest of your day!";
            return $output;
        } else {
            $output['message'] = "This is so odd. The invoice that is tied to the release you requested... I cannot find it. Please try refreshing the page and try again.";
            return $output;
        }
    }

    public function released(DispositionInvoice $invoice, Request $request)
    {
        if ($invoice) {
            $disposition_id = $request->get('disposition_id');

            if ($disposition_id == 0) {
                foreach ($invoice->dispositions as $disposition) {
                    if ($disposition->release_date == null) {
                        $disposition_id = $request->get('disposition_id');

                        $disposition->update([
                            'release_date' => Carbon\Carbon::today()->toDateTimeString()
                        ]);
                        $disposition->parcel->update([
                            "landbank_property_status_id" => 17,
                            "hfa_property_status_id" => 33 // released
                        ]);
                        perform_all_parcel_checks($disposition->parcel);
                        guide_next_pending_step(2, $disposition->parcel->id);

                        // $lc = new LogConverter('Dispositions', 'released');
                        // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' released disposition '.$disposition->id)->save();

                        guide_set_progress($disposition->id, 14, $status = 'completed', 0); // step 3 - fiscal agent releases lien
                    }
                    $output['message'] = "The disposition was released successfully. Joy and celebrations! Too much? I just get so excited sometimes.";
                }
            } else {
                $disposition = Disposition::where('id', '=', $disposition_id)->first();

                if ($disposition) {
                    $disposition_id = $request->get('disposition_id');

                    $disposition->update([
                        'release_date' => Carbon\Carbon::today()->toDateTimeString()
                    ]);
                    $disposition->parcel->update([
                        "landbank_property_status_id" => 17,
                        "hfa_property_status_id" => 33 // released
                    ]);

                    perform_all_parcel_checks($disposition->parcel);
                    guide_next_pending_step(2, $disposition->parcel->id);

                    // $lc = new LogConverter('Dispositions', 'released');
                    // $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' released disposition '.$disposition->id)->save();

                    guide_set_progress($disposition->id, 14, $status = 'completed', 0); // step 3 - fiscal agent releases lien

                    $output['message'] = "The disposition was released successfully. Does that not feel great to get that done? It feels good to me!";
                }
            }

            $output['message'] = "Released. Nothing like the sweet release of a disposition. Good times.";
            return $output;
        } else {
            $output['message'] = "OK, that is weird. I cannot find the invoice this is tied to... try refreshing the page and doing it again. Sorry!";
            return $output;
        }
    }

    public function newNoteEntry(DispositionInvoice $invoice, Request $request)
    {
        if ($invoice && $request->get('invoice-note')) {
            $user = Auth::user();

            $note = new DispositionInvoiceNote([
                'owner_id' => $user->id,
                'disposition_invoice_id' => $invoice->id,
                'note' => $request->get('invoice-note')
            ]);
            $note->save();
            // $lc = new LogConverter('disposition_invoices', 'addnote');
            // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' added note to disposition invoice')->save();

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

    public function sendForPayment(DispositionInvoice $invoice)
    {
        if ((!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) || !Auth::user()->isActive()) {
            $output['message'] = 'Sorry the user doing this is not allowed to... Ask your manager to ensure they are a HFA Disposition Approver, an HFA Admin, and active. Thanks!';
            return $output;
        }

        if ($invoice) {
            $invoice->update([
                'status_id' => 8 // submitted to fiscal agent
            ]);

            // $lc = new LogConverter('disposition_invoices', 'payment pending');
            // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc('Disposition invoice is pending payment.')->save();

            // Send email notification to LB
            $LBDispositionApprovers = User::where('entity_id', '==', $invoice->entity_id)
                                ->where('active', '=', 1)
                                ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                ->where('users_roles.role_id', '=', 11)
                                ->select('id')
                                ->get();
            $message_recipients_array = $LBDispositionApprovers->toArray();
            try {
                foreach ($message_recipients_array as $userToNotify) {
                    $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                    $emailNotification = new EmailNotificationDispositionPaymentRequested($userToNotify, $invoice->id);
                    \Mail::to($current_recipient->email)->send($emailNotification);
                    //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                dd($ex->getMessage());
            }
            foreach ($invoice->dispositions as $disposition) {
                // all step 1,2,3 and 4 completed
                guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1
                guide_set_progress($disposition->id, 6, $status = 'completed', 0); // step 2
                guide_set_progress($disposition->id, 13, $status = 'completed', 0); // step 3
                guide_set_progress($disposition->id, 18, $status = 'completed', 0); // step 4
                guide_set_progress($disposition->id, 20, $status = 'completed', 0); // step 4 - send for payment
            }

            $data['message'] = 'The disposition invoice was sent to a fiscal agent!';
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    public function addHFAApproverToInvoice(DispositionInvoice $invoice, Request $request)
    {
        if ((!Auth::user()->isHFADispositionApprover() && !Auth::user()->isHFAAdmin()) ||  !Auth::user()->isActive()) {
            $output['message'] = 'Sorry the approver you are trying to add is either not active, no longer an eligible approver, or no longer an HFA Admin. All those things are required for that user to be an approver. Sorry! I know that this is probably annoyting, but we got to keep things legit!';
            return $output;
        }

        if ($invoice) {
            $approver_id = $request->get('user_id');
            if (!ApprovalRequest::where('approval_type_id', '=', 12)
                        ->where('link_type_id', '=', $invoice->id)
                        ->where('user_id', '=', $approver_id)
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 12,
                    "link_type_id" => $invoice->id,
                    "user_id" => $approver_id
                ]);
                $newApprovalRequest->save();
                // $lc = new LogConverter('disposition_invoices', 'add.approver');
                // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'added an approver.')->save();

                $data['message'] = 'The approver was added.';
                return $data;
            } else {
                $data['message'] = 'Looks like that person had already been added. Try refreshing the page and see if that is the case.';
                return $data;
            }
        } else {
            $data['message'] = 'Well that is weird. I cannot find the invoice you are trying to add this person to... I suggest refreshing the page and trying again.';
            return $data;
        }
    }

    public function approveInvoice(DispositionInvoice $invoice, $approvers = null, $document_ids = null, $approval_type = 12)
    {
        if (((!Auth::user()->isHFADispositionApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) || !Auth::user()->isActive) {
            $output['message'] = 'Sorry, that user does not have permission to do this action any longer. Please have your admin check your role as a disposition invoice approver, whether or not your user is active, or that your are a still a memeber of your organization.';
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

                        // $lc = new LogConverter('reimbursement_invoices', 'approval by proxy');
                        // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'approved the invoice for '.$approver->name)->save();
                    }
                }
                $data['message'] = 'This request was approved.';
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

                    // $lc = new LogConverter('disposition_invoices', 'approval');
                    // $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'approved the disposition invoice.')->save();

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

    public function approveInvoiceUploadSignature(DispositionInvoice $invoice, Request $request)
    {
        if (app('env') == 'local') {
            app('debugbar')->disable();
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';

            $categories_json = json_encode(['39'], true);

            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            $invoice->load('dispositions');

            // get parcels from req $req->parcels
            foreach ($invoice->dispositions as $disposition) {
                foreach ($files as $file) {
                    // Create filepath
                    $folderpath = 'documents/entity_'. $disposition->parcel->entity_id . '/program_' . $disposition->parcel->program_id . '/parcel_' . $disposition->parcel->id . '/';

                    // sanitize filename
                    $characters = [' ','�','`',"'",'~','"','\'','\\','/'];
                    $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                    // Create a record in documents table
                    $document = new Document([
                        'user_id' => $user->id,
                        'parcel_id' => $disposition->parcel->id,
                        'categories' => $categories_json,
                        'filename' => $original_filename
                    ]);

                    $document->save();

                    // automatically approve
                    $document->approve_categories([39]);

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
                    // $lc=new LogConverter('document', 'create');
                    // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' created document ' . $filepath)->save();
                    // store original file
                    Storage::put($filepath, File::get($file));

                    $uploadcount++;
                }
            }

            if ($request->get('approvaltype') !== null) {
                $approval_type = $request->get('approvaltype');
            } else {
                $approval_type = 12;
            }
            $approval_process = $this->approveInvoice($invoice, $approvers, $document_ids, $approval_type);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function approveInvoiceUploadSignatureComments(DispositionInvoice $invoice, Request $request)
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
                // $lc = new LogConverter('document', 'comment');
                // $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' added comment to document ')->save();
            }
            return 1;
        } else {
            return 0;
        }
    }
}

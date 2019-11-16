<?php

namespace App\Http\Controllers;

use Carbon;
use App\Models\Account;
// use App\LogConverter;
use App\Models\ReimbursementInvoice;
use App\Models\ReimbursementPurchaseOrders;
use App\Models\ReimbursementRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AccountingController extends Controller
{
    /**
     * Invoice List
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function invoiceList(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // $lc = new LogConverter('invoice', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed invoice list')->save();
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
            $sortedBy = $request->query('invoices_sort_by');
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['invoices_sorted_by_query'=>$sortedBy]);
                $invoices_sorted_by_query = $request->session()->get('invoices_sorted_by_query');
            } elseif (!is_null($request->session()->get('invoices_sorted_by_query'))) {
                // use the session value

                $invoices_sorted_by_query = $request->session()->get('invoices_sorted_by_query');
            } else {
                // set the default
                session(['invoices_sorted_by_query'=>'12']);
                $invoices_sorted_by_query = $request->session()->get('invoices_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query

            if (!is_null($sortedBy)) {
                switch ($request->query('invoices_asc_desc')) {
                    case '1':
                        # code...
                        session(['invoices_asc_desc'=> 'desc']);
                        $invoicesAscDesc =  $request->session()->get('invoices_asc_desc');
                        session(['invoices_asc_desc_opposite' => ""]);
                        $invoicesAscDescOpposite =  $request->session()->get('invoices_asc_desc_opposite');
                        break;

                    default:
                        session(['invoices_asc_desc'=> 'asc']);
                        $invoicesAscDesc =  $request->session()->get('invoices_asc_desc');
                        session(['invoices_asc_desc_opposite' => 1]);
                        $invoicesAscDescOpposite = $request->session()->get('invoices_asc_desc_opposite');
                        break;
                }
                switch ($sortedBy) {
                    case '12':
                        # created_at
                        session(['invoices_sort_by' => 'inv.created_at']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '2':
                        # invoice_id
                        session(['invoices_sort_by' => 'inv.id']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '3':
                        # account_id
                        session(['invoices_sort_by' => 'inv.account_id']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '4':
                        # program_id
                        session(['invoices_sort_by' =>'inv.program_id']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '5':
                        # entity_id
                        session(['invoices_sort_by' =>'inv.entity_id']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '6':
                        # total_parcels
                        session(['invoices_sort_by' => 'total_parcels']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '7':
                        # total_requested
                        session(['invoices_sort_by' => 'total_requested']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '8':
                        #  total_approved
                        session(['invoices_sort_by' => 'total_approved']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '9':
                        #  total_amount (invoiced)
                        session(['invoices_sort_by' => 'total_amount']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '10':
                        #  total_paid
                        session(['invoices_sort_by' => 'total_paid']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    case '11':
                        #  invoice_status_name
                        session(['invoices_sort_by' => 'invstat.invoice_status_name']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                    default:
                        # code...
                        session(['invoices_sort_by' => 'inv.created_at']);
                        $invoicessSortBy = $request->session()->get('invoices_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('invoices_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['invoices_sort_by' => 'inv.created_at']);
                $invoicessSortBy = $request->session()->get('invoices_sort_by');
                session(['invoices_asc_desc' => 'asc']);
                $invoicesAscDesc = $request->session()->get('invoices_asc_desc');
                session(['invoices_asc_desc_opposite' => '1']);
                $invoicesAscDescOpposite = $request->session()->get('invoices_asc_desc_opposite');
            } else {
                // use values in the session
                $invoicessSortBy = $request->session()->get('invoices_sort_by');
                $invoicesAscDesc = $request->session()->get('invoices_asc_desc');
                $invoicesAscDescOpposite = $request->session()->get('invoices_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('invoices_program_filter'))) {
                //Update the session
                session(['invoices_program_filter' => $request->query('invoices_program_filter')]);
                $invoicesProgramFilter = $request->session()->get('invoices_program_filter');
                session(['invoices_program_filter_operator' => '=']);
                $invoicesProgramFilterOperator = $request->session()->get('invoices_program_filter_operator');
            } elseif (is_null($request->session()->get('invoices_program_filter')) || $request->query('invoices_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['invoices_program_filter' => '%%']);
                $invoicesProgramFilter = $request->session()->get('invoices_program_filter');
                session(['invoices_program_filter_operator' => 'LIKE']);
                $invoicesProgramFilterOperator = $request->session()->get('invoices_program_filter_operator');
            } else {
                // use values in the session
                $invoicesProgramFilter = $request->session()->get('invoices_program_filter');
                $invoicesProgramFilterOperator = $request->session()->get('invoices_program_filter_operator');
            }

            if (is_numeric($request->query('invoices_status_filter'))) {
                //Update the session
                session(['invoices_status_filter' => $request->query('invoices_status_filter')]);
                $invoicesStatusFilter = $request->session()->get('invoices_status_filter');
                session(['invoices_status_filter_operator' => '=']);
                $invoicesStatusFilterOperator = $request->session()->get('invoices_program_filter_operator');
            } elseif (is_null($request->session()->get('invoices_status_filter')) || $request->query('invoices_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['invoices_status_filter' => '%%']);
                $invoicesStatusFilter = $request->session()->get('invoices_status_filter');
                session(['invoices_status_filter_operator' => 'LIKE']);
                $invoicesStatusFilterOperator = $request->session()->get('invoices_status_filter_operator');
            } else {
                // use values in the session
                $invoicesStatusFilter = $request->session()->get('invoices_status_filter');
                $invoicesStatusFilterOperator = $request->session()->get('invoices_status_filter_operator');
            }

            // Insert other Filters here

            $currentUser = Auth::user();

            /*->where('programs.id',$invoicesProgramFilterOperator,$invoicesProgramFilter)
                                ->where('inv.hfa_property_status_id',$invoicesStatusFilterOperator,$invoicesStatusFilter)
                                ->where('programs.entity_id',$where_entity_id_operator, $where_entity_id)
                                ->orderBy($invoicessSortBy,$invoicesAscDesc)
             */
            //// set the default beginning for just a status filter - should start with where if there
            //// is no program filter
            $and = ' WHERE ';
            if ($invoicesProgramFilter) {
                $invoicesWhereOrder = "WHERE inv.program_id ".$invoicesProgramFilterOperator." '".$invoicesProgramFilter."' \n";
                $and = ' AND ';
            }

            if ($invoicesStatusFilter) {
                $invoicesWhereOrder .= $and."inv.status_id ".$invoicesStatusFilterOperator." '".$invoicesStatusFilter."' \n";
                $and = ' AND ';
            }

            $invoicesWhereOrder .= $and." inv.entity_id $where_entity_id_operator '$where_entity_id' "." \n";

            if ($invoicessSortBy) {
                $invoicesWhereOrder .="ORDER BY ".$invoicessSortBy." ".$invoicesAscDesc;
            }

            $invoices = DB::select(
                DB::raw("
        								SELECT
											inv.id AS invoice_id ,
										        inv.po_id,
										        inv.account_id,
										        aab.req_id,
										        inv.created_at as 'date',
											inv.program_id ,
											inv.entity_id ,
											pr.program_name ,
											ent.entity_name ,
											pc.total_parcels ,
											ra.total_requested,
										        aa.total_approved,
											ta.total_amount ,
											ap.total_paid ,
											invstat.invoice_status_name

										FROM
											reimbursement_invoices inv

										#####################################

										INNER JOIN accounts a ON inv.entity_id = a.entity_id
										INNER JOIN programs pr ON inv.program_id = pr.id
										INNER JOIN entities ent ON inv.entity_id = ent.id
										INNER JOIN invoice_statuses invstat ON inv.status_id = invstat.id


										#####################################
										##### TOTAL PARCELS
										INNER JOIN(
											SELECT
												a.reimbursement_invoice_id AS invoice_id ,
												COUNT(a.parcel_id) AS total_parcels
											FROM
												parcels_to_reimbursement_invoices a
											GROUP BY
												a.reimbursement_invoice_id
										) pc ON inv.id = pc.invoice_id


										#####################################
										#### TOTAL AMOUNT (INVOICED)

										INNER JOIN(
											SELECT
												a.invoice_id AS invoice_id ,
												sum(a.amount) AS total_amount
											FROM
												invoice_items a
                                            WHERE EXISTS (
                                                    SELECT *
                                                    FROM po_items
                                                    WHERE po_items.id = a.ref_id
                                                    AND EXISTS (
                                                        SELECT *
                                                        FROM request_items
                                                        WHERE request_items.id = po_items.ref_id
                                                    )
                                                )
											GROUP BY
												a.invoice_id
										) ta ON inv.id = ta.invoice_id

										#####################################
										#### TOTAL PAID

										LEFT JOIN(
											SELECT
												a.link_to_type_id AS invoice_id ,
												SUM(
													CASE
													WHEN a.type_id = 1 THEN
														a.amount
													ELSE
														0
													END
												) AS total_paid
											FROM
												transactions a
											GROUP BY
												a.link_to_type_id
										) ap ON inv.id = ap.invoice_id



										#####################################
										##### TOTAL APPROVED (PO)

										INNER JOIN(
											SELECT
												a.po_id,
												sum(a.amount) AS total_approved
											FROM
												po_items a
                                            WHERE EXISTS (
                                                    SELECT *
                                                    FROM request_items
                                                    WHERE request_items.id = a.ref_id
                                                )
											GROUP BY
												a.po_id

										) aa ON inv.po_id = aa.po_id


										INNER JOIN(
											SELECT
												a.rq_id as req_id, a.id as po_id
											FROM
												reimbursement_purchase_orders a
										) aab ON inv.po_id = aab.po_id



										#####################################
										##### TOTAL  REQUESTED

										LEFT JOIN(
											SELECT
												a.req_id,
												sum(a.amount) AS total_requested
											FROM
												request_items a
											GROUP BY
												a.req_id

										) ra ON aab.req_id = ra.req_id



										".$invoicesWhereOrder."


									")
            );

            //////////////////////////////////////// THIS FUNCTION IS JUST HERE TO FIX STATUS
            ///////// RUN THIS ONCE ON PRODUCTION AFTER IMPORT AND THEN COMMENT OUT.
            if ($request->query('fixStatus') == 1) {
                foreach ($invoices as $data) {
                    if ($data->total_amount > 0 && $data->total_amount - $data->total_paid <= 0.01) {
                        ReimbursementInvoice::where('id', $data->invoice_id)->update(['status_id'=>6]);
                    } elseif ($data->total_amount > 0 && $data->total_amount - $data->total_paid == 0) {
                        ReimbursementInvoice::where('id', $data->invoice_id)->update(['status_id'=>6]);
                    } elseif ($data->total_amount > 0) {
                        ReimbursementInvoice::where('id', $data->invoice_id)->update(['status_id'=>4]);
                        $balance =  $data->total_amount - $data->total_paid;
                        session(['system_message' => session('systemMessage').'<br /> Invoice '.$data->invoice_id.' has a balance of '.$balance]);
                    } else {
                        ReimbursementInvoice::where('id', $data->invoice_id)->update(['status_id'=>1]);
                    }
                }
                return redirect('/dashboard');
            }

            $programs = ReimbursementInvoice::join('programs', 'reimbursement_invoices.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->orderBy('programs.program_name', 'ASC')->get();
            $statuses = ReimbursementInvoice::join('invoice_statuses', 'reimbursement_invoices.status_id', '=', 'invoice_statuses.id')->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')->get();

            return view('dashboard.invoices_list', compact('invoices', 'programs', 'statuses', 'currentUser', 'invoices_sorted_by_query', 'invoicesAscDesc', 'invoicesAscDescOpposite', 'programs', 'statuses', 'invoicesProgramFilter', 'invoicesStatusFilter'));
        } else {
            return 'Sorry you do not have access to Invoice Listing page. Please try logging in again or contact your admin to request access.';
        }
    }

    /**
     * Request List
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function requestList(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // $lc = new LogConverter('requests', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed requests')->save();
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
            $sortedBy = $request->query('requests_sort_by');
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['requests_sorted_by_query'=>$sortedBy]);
                $requests_sorted_by_query = $request->session()->get('requests_sorted_by_query');
            } elseif (!is_null($request->session()->get('requests_sorted_by_query'))) {
                // use the session value

                $requests_sorted_by_query = $request->session()->get('requests_sorted_by_query');
            } else {
                // set the default
                session(['requests_sorted_by_query'=>'12']);
                $requests_sorted_by_query = $request->session()->get('requests_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query

            if (!is_null($sortedBy)) {
                switch ($request->query('requests_asc_desc')) {
                    case '1':
                        # code...
                        session(['requests_asc_desc'=> 'desc']);
                        $requestsAscDesc =  $request->session()->get('requests_asc_desc');
                        session(['requests_asc_desc_opposite' => ""]);
                        $requestsAscDescOpposite =  $request->session()->get('requests_asc_desc_opposite');
                        break;

                    default:
                        session(['requests_asc_desc'=> 'asc']);
                        $requestsAscDesc =  $request->session()->get('requests_asc_desc');
                        session(['requests_asc_desc_opposite' => 1]);
                        $requestsAscDescOpposite = $request->session()->get('requests_asc_desc_opposite');
                        break;
                }
                switch ($sortedBy) {
                    case '1':
                        # created_at
                        session(['requests_sort_by' => 'req.created_at']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '2':
                        # request_id
                        session(['requests_sort_by' => 'req.id']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '3':
                        # account_id
                        session(['requests_sort_by' => 'req.account_id']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '4':
                        # program_id
                        session(['requests_sort_by' =>'req.program_id']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '5':
                        # entity_id
                        session(['requests_sort_by' =>'req.entity_id']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '6':
                        # total_parcels
                        session(['requests_sort_by' => 'total_parcels']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '7':
                        # total_requested
                        session(['requests_sort_by' => 'total_requested']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '8':
                        #  total_approved
                        session(['requests_sort_by' => 'total_approved']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '9':
                        #  total_amount
                        session(['requests_sort_by' => 'total_invoiced']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '12':
                        #  total_paid
                        session(['requests_sort_by' => 'total_paid']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    case '10':
                        #  breakout_item_status_name
                        session(['requests_sort_by' => 'reqstat.invoice_status_name']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                    default:
                        # code...
                        session(['requests_sort_by' => 'req.created_at']);
                        $requestssSortBy = $request->session()->get('requests_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('requests_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['requests_sort_by' => 'req.created_at']);
                $requestssSortBy = $request->session()->get('requests_sort_by');
                session(['requests_asc_desc' => 'asc']);
                $requestsAscDesc = $request->session()->get('requests_asc_desc');
                session(['requests_asc_desc_opposite' => '1']);
                $requestsAscDescOpposite = $request->session()->get('requests_asc_desc_opposite');
            } else {
                // use values in the session
                $requestssSortBy = $request->session()->get('requests_sort_by');
                $requestsAscDesc = $request->session()->get('requests_asc_desc');
                $requestsAscDescOpposite = $request->session()->get('requests_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('requests_program_filter'))) {
                //Update the session
                session(['requests_program_filter' => $request->query('requests_program_filter')]);
                $requestsProgramFilter = $request->session()->get('requests_program_filter');
                session(['requests_program_filter_operator' => '=']);
                $requestsProgramFilterOperator = $request->session()->get('requests_program_filter_operator');
            } elseif (is_null($request->session()->get('requests_program_filter')) || $request->query('requests_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['requests_program_filter' => '%%']);
                $requestsProgramFilter = $request->session()->get('requests_program_filter');
                session(['requests_program_filter_operator' => 'LIKE']);
                $requestsProgramFilterOperator = $request->session()->get('requests_program_filter_operator');
            } else {
                // use values in the session
                $requestsProgramFilter = $request->session()->get('requests_program_filter');
                $requestsProgramFilterOperator = $request->session()->get('requests_program_filter_operator');
            }

            if (is_numeric($request->query('requests_status_filter'))) {
                //Update the session
                session(['requests_status_filter' => $request->query('requests_status_filter')]);
                $requestsStatusFilter = $request->session()->get('requests_status_filter');
                session(['requests_status_filter_operator' => '=']);
                $requestsStatusFilterOperator = $request->session()->get('requests_program_filter_operator');
            } elseif (is_null($request->session()->get('requests_status_filter')) || $request->query('requests_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['requests_status_filter' => '%%']);
                $requestsStatusFilter = $request->session()->get('requests_status_filter');
                session(['requests_status_filter_operator' => 'LIKE']);
                $requestsStatusFilterOperator = $request->session()->get('requests_status_filter_operator');
            } else {
                // use values in the session
                $requestsStatusFilter = $request->session()->get('requests_status_filter');
                $requestsStatusFilterOperator = $request->session()->get('requests_status_filter_operator');
            }

            // Insert other Filters here

            $currentUser = Auth::user();

            /*->where('programs.id',$requestsProgramFilterOperator,$requestsProgramFilter)
                                ->where('req.hfa_property_status_id',$requestsStatusFilterOperator,$requestsStatusFilter)
                                ->where('programs.entity_id',$where_entity_id_operator, $where_entity_id)
                                ->orderBy($requestssSortBy,$requestsAscDesc)
             */
            //// set the defualt begining for just a status filter - should start with where if there
            //// is no program filter
            $and = ' WHERE ';
            if ($requestsProgramFilter) {
                $requestsWhereOrder = "WHERE req.program_id ".$requestsProgramFilterOperator." '".$requestsProgramFilter."' \n";
                $and = ' AND ';
            }

            if ($requestsStatusFilter) {
                $requestsWhereOrder .= $and."req.status_id ".$requestsStatusFilterOperator." '".$requestsStatusFilter."' \n";
                $and = ' AND ';
            }

            $requestsWhereOrder .= $and." req.entity_id $where_entity_id_operator '$where_entity_id' "." \n";
            if ($requestssSortBy) {
                $requestsWhereOrder .="ORDER BY ".$requestssSortBy." ".$requestsAscDesc;
            }



            $requests = DB::select(
                DB::raw("
        								SELECT
        									inv.invoice_id,
        									ia.total_invoiced,
											req.id AS req_id ,
											req.account_id,
										    req.created_at as 'date',
										    pos.po_id,
											req.program_id ,
											req.entity_id ,
											pr.program_name ,
											ent.entity_name ,
											pc.total_parcels ,
											ra.total_requested,
										    aa.total_approved,
											ta.total_amount ,
											ap.total_paid ,
											reqstat.invoice_status_name

										FROM
											reimbursement_requests req

										#####################################

										INNER JOIN accounts a ON req.entity_id = a.entity_id
										INNER JOIN programs pr ON req.program_id = pr.id
										INNER JOIN entities ent ON req.entity_id = ent.id
										INNER JOIN invoice_statuses reqstat ON req.status_id = reqstat.id


										#####################################
										##### TOTAL PARCELS
										INNER JOIN(
											SELECT
												a.reimbursement_request_id AS request_id ,
												COUNT(a.parcel_id) AS total_parcels
											FROM
												parcels_to_reimbursement_requests a
											GROUP BY
												a.reimbursement_request_id
										) pc ON req.id = pc.request_id


										#####################################
										#### TOTAL AMOUNT (requestD)

										LEFT JOIN(
											SELECT
												a.req_id AS request_id ,
												sum(a.amount) AS total_amount
											FROM
												request_items a
											GROUP BY
												a.req_id
										) ta ON req.id = ta.request_id



										########################################
										###### Get a po_id
										LEFT JOIN(
											SELECT pos.id as po_id, pos.rq_id as req_id FROM reimbursement_purchase_orders pos
										) pos on req.id = pos.req_id

										#####################################
										##### TOTAL APPROVED (PO)




											LEFT JOIN(
												SELECT
													a.po_id,
													sum(a.amount) AS total_approved
												FROM
													po_items a
                                                WHERE EXISTS (
                                                    SELECT *
                                                    FROM request_items
                                                    WHERE request_items.id = a.ref_id
                                                )
												GROUP BY
													a.po_id

											) aa ON ((pos.po_id IS NOT NULL) AND (pos.po_id = aa.po_id))

										########################################
										###### Get a invoice_id
										LEFT JOIN(
											SELECT inv.id as invoice_id, inv.po_id FROM reimbursement_invoices inv
										) inv on pos.po_id = inv.po_id

										#####################################
										##### TOTAL INVOICED


											LEFT JOIN(
												SELECT
													a.invoice_id,
													sum(a.amount) AS total_invoiced
												FROM
													invoice_items a
                                                WHERE EXISTS (
                                                    SELECT *
                                                    FROM po_items
                                                    WHERE po_items.id = a.ref_id
                                                    AND EXISTS (
                                                        SELECT *
                                                        FROM request_items
                                                        WHERE request_items.id = po_items.ref_id
                                                    )
                                                )
												GROUP BY
													a.invoice_id

											) ia ON ((inv.invoice_id IS NOT NULL) AND (inv.invoice_id = ia.invoice_id))

										#####################################
										#### TOTAL PAID

										LEFT JOIN(
											SELECT
												a.link_to_type_id AS invoice_id ,
												SUM(
													CASE
													WHEN a.type_id = 1 THEN
														a.amount
													ELSE
														0
													END
												) AS total_paid
											FROM
												transactions a
											GROUP BY
												a.link_to_type_id
										) ap ON ((inv.invoice_id IS NOT NULL) AND (inv.invoice_id = ap.invoice_id))



										#####################################
										##### TOTAL  REQUESTED

										LEFT JOIN(
											SELECT
												a.req_id,
												sum(a.amount) AS total_requested
											FROM
												request_items a
											GROUP BY
												a.req_id

										) ra ON req.id = ra.req_id



										".$requestsWhereOrder."

									")
            );
            //$count = count($requests);
            //dd($count,$requests);

            //////////////////////////////////////// THIS FUNCTION IS JUST HERE TO FIX STATUS
            ///////// RUN THIS ONCE ON PRODUCTION AFTER IMPORT AND THEN COMMENT OUT.
            if ($request->query('fixStatus') == 1) {
                foreach ($requests as $data) {
                    if ($data->total_approved > 0) {
                        ReimbursementRequest::where('id', $data->req_id)->update(['status_id'=>2]);
                    } else {
                        ReimbursementRequest::where('id', $data->req_id)->update(['status_id'=>1]);
                    }
                }
                return redirect('/dashboard/po_list?fixStatus=1');
            }

            $programs = ReimbursementRequest::join('programs', 'reimbursement_requests.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->orderBy('programs.program_name', 'ASC')->get()->all();
            //$statuses = ReimbursementRequest::join('breakout_items_statuses', 'reimbursement_requests.status_id', '=','breakout_items_statuses.id' )->select('breakout_items_statuses.breakout_item_status_name','breakout_items_statuses.id')->groupBy('breakout_items_statuses.id' ,'breakout_items_statuses.breakout_item_status_name')->get()->all();
            $statuses = ReimbursementRequest::join('invoice_statuses', 'reimbursement_requests.status_id', '=', 'invoice_statuses.id')
                            ->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')
                            ->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')
                            ->get()
                            ->all();


            return view('dashboard.requests_list', compact('requests', 'programs', 'statuses', 'currentUser', 'requests_sorted_by_query', 'requestsAscDesc', 'requestsAscDescOpposite', 'programs', 'statuses', 'requestsProgramFilter', 'requestsStatusFilter'));
        } else {
            return 'Sorry you do not have access to Request Listing page. Please try logging in again or contact your admin to request access.';
        }
    }
    public function poList(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // $lc = new LogConverter('polist', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . 'viewed polist')->save();
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


            // go through all po_items and make sure there is a corresponding request_item. If not delete.


            /// The sorting column
            $sortedBy = $request->query('pos_sort_by');
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['pos_sorted_by_query'=>$sortedBy]);
                $pos_sorted_by_query = $request->session()->get('pos_sorted_by_query');
            } elseif (!is_null($request->session()->get('pos_sorted_by_query'))) {
                // use the session value

                $pos_sorted_by_query = $request->session()->get('pos_sorted_by_query');
            } else {
                // set the default
                session(['pos_sorted_by_query'=>'12']);
                $pos_sorted_by_query = $request->session()->get('pos_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query

            if (!is_null($sortedBy)) {
                switch ($request->query('pos_asc_desc')) {
                    case '1':
                        # code...
                        session(['pos_asc_desc'=> 'desc']);
                        $posAscDesc =  $request->session()->get('pos_asc_desc');
                        session(['pos_asc_desc_opposite' => ""]);
                        $posAscDescOpposite =  $request->session()->get('pos_asc_desc_opposite');
                        break;

                    default:
                        session(['pos_asc_desc'=> 'asc']);
                        $posAscDesc =  $request->session()->get('pos_asc_desc');
                        session(['pos_asc_desc_opposite' => 1]);
                        $posAscDescOpposite = $request->session()->get('pos_asc_desc_opposite');
                        break;
                }
                switch ($sortedBy) {
                    case '1':
                        # created_at
                        session(['pos_sort_by' => 'pos.created_at']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '2':
                        # request_id
                        session(['pos_sort_by' => 'pos.id']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '3':
                        # account_id
                        session(['pos_sort_by' => 'pos.account_id']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '4':
                        # program_id
                        session(['pos_sort_by' =>'pos.program_id']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '5':
                        # entity_id
                        session(['pos_sort_by' =>'pos.entity_id']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '6':
                        # total_parcels
                        session(['pos_sort_by' => 'total_parcels']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '7':
                        # total_requested
                        session(['pos_sort_by' => 'total_requested']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '8':
                        #  total_approved
                        session(['pos_sort_by' => 'total_approved']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '9':
                        #  total_amount
                        session(['pos_sort_by' => 'total_invoiced']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '12':
                        #  total_paid
                        session(['pos_sort_by' => 'total_paid']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    case '10':
                        #  breakout_item_status_name
                        session(['pos_sort_by' => 'postat.invoice_status_name']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                    default:
                        # code...
                        session(['pos_sort_by' => 'pos.created_at']);
                        $possSortBy = $request->session()->get('pos_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('pos_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['pos_sort_by' => 'pos.created_at']);
                $possSortBy = $request->session()->get('pos_sort_by');
                session(['pos_asc_desc' => 'asc']);
                $posAscDesc = $request->session()->get('pos_asc_desc');
                session(['pos_asc_desc_opposite' => '1']);
                $posAscDescOpposite = $request->session()->get('pos_asc_desc_opposite');
            } else {
                // use values in the session
                $possSortBy = $request->session()->get('pos_sort_by');
                $posAscDesc = $request->session()->get('pos_asc_desc');
                $posAscDescOpposite = $request->session()->get('pos_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('pos_program_filter'))) {
                //Update the session
                session(['pos_program_filter' => $request->query('pos_program_filter')]);
                $posProgramFilter = $request->session()->get('pos_program_filter');
                session(['pos_program_filter_operator' => '=']);
                $posProgramFilterOperator = $request->session()->get('pos_program_filter_operator');
            } elseif (is_null($request->session()->get('pos_program_filter')) || $request->query('pos_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['pos_program_filter' => '%%']);
                $posProgramFilter = $request->session()->get('pos_program_filter');
                session(['pos_program_filter_operator' => 'LIKE']);
                $posProgramFilterOperator = $request->session()->get('pos_program_filter_operator');
            } else {
                // use values in the session
                $posProgramFilter = $request->session()->get('pos_program_filter');
                $posProgramFilterOperator = $request->session()->get('pos_program_filter_operator');
            }

            if (is_numeric($request->query('pos_status_filter'))) {
                //Update the session
                session(['pos_status_filter' => $request->query('pos_status_filter')]);
                $posStatusFilter = $request->session()->get('pos_status_filter');
                session(['pos_status_filter_operator' => '=']);
                $posStatusFilterOperator = $request->session()->get('pos_program_filter_operator');
            } elseif (is_null($request->session()->get('pos_status_filter')) || $request->query('pos_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['pos_status_filter' => '%%']);
                $posStatusFilter = $request->session()->get('pos_status_filter');
                session(['pos_status_filter_operator' => 'LIKE']);
                $posStatusFilterOperator = $request->session()->get('pos_status_filter_operator');
            } else {
                // use values in the session
                $posStatusFilter = $request->session()->get('pos_status_filter');
                $posStatusFilterOperator = $request->session()->get('pos_status_filter_operator');
            }

            // Insert other Filters here

            $currentUser = Auth::user();

            /*->where('programs.id',$posProgramFilterOperator,$posProgramFilter)
                                ->where('req.hfa_property_status_id',$posStatusFilterOperator,$posStatusFilter)
                                ->where('programs.entity_id',$where_entity_id_operator, $where_entity_id)
                                ->orderBy($possSortBy,$posAscDesc)
             */
            //// set the defualt begining for just a status filter - should start with where if there
            //// is no program filter
            $and = ' WHERE ';
            if ($posProgramFilter) {
                $posWhereOrder = "WHERE pos.program_id ".$posProgramFilterOperator." '".$posProgramFilter."' \n";
                $and = ' AND ';
            }

            if ($posStatusFilter) {
                $posWhereOrder .= $and."pos.status_id ".$posStatusFilterOperator." '".$posStatusFilter."' \n";
                $and = ' AND ';
            }

            $posWhereOrder .= $and." pos.entity_id $where_entity_id_operator '$where_entity_id' "." \n";
            if ($possSortBy) {
                $posWhereOrder .="ORDER BY ".$possSortBy." ".$posAscDesc;
            }



            $pos = DB::select(
                DB::raw("
        								SELECT
        									inv.invoice_id,
        									ia.total_invoiced,
											pos.id AS po_id ,
											pos.account_id,
										    pos.created_at as 'date',
										    pos.rq_id AS req_id,
										    pos.status_id,
											pos.program_id ,
											pos.entity_id ,
											pr.program_name ,
											ent.entity_name ,
											pc.total_parcels ,
											ra.total_requested,
										    aa.total_approved,
											#ta.total_amount ,
											ap.total_paid ,
											postat.invoice_status_name

										FROM
											reimbursement_purchase_orders pos

										#####################################

										INNER JOIN accounts a ON pos.entity_id = a.entity_id
										INNER JOIN programs pr ON pos.program_id = pr.id
										INNER JOIN entities ent ON pos.entity_id = ent.id
										INNER JOIN invoice_statuses postat ON pos.status_id = postat.id


										#####################################
										##### TOTAL PARCELS
										INNER JOIN(
											SELECT
												a.purchase_order_id AS po_id ,
												COUNT(a.parcel_id) AS total_parcels
											FROM
												parcels_to_purchase_orders a
											GROUP BY
												a.purchase_order_id
										) pc ON pos.id = pc.po_id


										#####################################
										##### TOTAL APPROVED (PO)

											LEFT JOIN(
												SELECT
													a.po_id,
													sum(a.amount) AS total_approved
												FROM
													po_items a
                                                WHERE EXISTS (
                                                    SELECT *
                                                    FROM request_items
                                                    WHERE request_items.id = a.ref_id
                                                )
												GROUP BY
													a.po_id

											) aa ON pos.id = aa.po_id

										########################################
										###### Get a invoice_id
											LEFT JOIN(
												SELECT inv.id as invoice_id, inv.po_id FROM reimbursement_invoices inv
											) inv on pos.id = inv.po_id

										#####################################
										##### TOTAL INVOICED

											LEFT JOIN(
												SELECT
													a.invoice_id,
													sum(a.amount) AS total_invoiced
												FROM
													invoice_items a
                                                WHERE EXISTS (
                                                    SELECT *
                                                    FROM po_items
                                                    WHERE po_items.id = a.ref_id
                                                    AND EXISTS (
                                                        SELECT *
                                                        FROM request_items
                                                        WHERE request_items.id = po_items.ref_id
                                                    )
                                                )
												GROUP BY
													a.invoice_id

											) ia ON ((inv.invoice_id IS NOT NULL) AND (inv.invoice_id = ia.invoice_id))

										#####################################
										#### TOTAL PAID

										LEFT JOIN(
											SELECT
												a.link_to_type_id AS invoice_id ,
												SUM(
													CASE
													WHEN a.type_id = 1 THEN
														a.amount
													ELSE
														0
													END
												) AS total_paid
											FROM
												transactions a
											GROUP BY
												a.link_to_type_id
										) ap ON ((inv.invoice_id IS NOT NULL) AND (inv.invoice_id = ap.invoice_id))


										#####################################
										##### TOTAL  REQUESTED

										LEFT JOIN(
											SELECT
												a.req_id,
												sum(a.amount) AS total_requested
											FROM
												request_items a
											GROUP BY
												a.req_id

										) ra ON pos.rq_id = ra.req_id

										".$posWhereOrder."

									")
            );
            //$count = count($requests);
            //dd($count,$requests);

            // foreach($pos as $po){
            //     $purchase_order = ReimbursementPurchaseOrders::where('id',$po->po_id)->first();
            //     $purchase_order->load('po_items')->load('po_items.request_item')->load('po_items.invoice_item');

            //     foreach($purchase_order->poItems as $po_item){

            //     }
            // }

            if ($request->query('fixStatus') == 1) {
                foreach ($pos as $data) {
                    if ($data->total_invoiced > 0) {
                        ReimbursementPurchaseOrders::where('id', $data->req_id)->update(['status_id'=>2]);
                    } else {
                        ReimbursementPurchaseOrders::where('id', $data->req_id)->update(['status_id'=>1]);
                    }
                }
                return redirect('/dashboard/invoice_list?fixStatus=1');
            }

            $programs = ReimbursementPurchaseOrders::join('programs', 'reimbursement_purchase_orders.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->orderBy('programs.program_name', 'ASC')->get()->all();
            // $statuses = ReimbursementPurchaseOrders::join('breakout_items_statuses', 'reimbursement_purchase_orders.status_id', '=','breakout_items_statuses.id' )->select('breakout_items_statuses.breakout_item_status_name','breakout_items_statuses.id')->groupBy('breakout_items_statuses.id' ,'breakout_items_statuses.breakout_item_status_name')->get()->all();
            $statuses = ReimbursementPurchaseOrders::join('invoice_statuses', 'reimbursement_purchase_orders.status_id', '=', 'invoice_statuses.id')
                            ->select('invoice_statuses.invoice_status_name', 'invoice_statuses.id')
                            ->groupBy('invoice_statuses.id', 'invoice_statuses.invoice_status_name')
                            ->get()
                            ->all();


            return view('dashboard.po_list', compact('pos', 'programs', 'statuses', 'currentUser', 'pos_sorted_by_query', 'posAscDesc', 'posAscDescOpposite', 'programs', 'posProgramFilter', 'posStatusFilter'));
        } else {
            return 'Sorry you do not have access to the PO Listing page. Please try logging in again or contact your admin to request access.';
        }
    }

    /**
     * Accounting
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function accounting(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // $lc = new LogConverter('accounting', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' viewed accounting')->save();
            /// process filters
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
            $sortedBy = $request->query('accounting_sort_by');
            /// Retain the original value submitted through the query
            if (strlen($sortedBy)>0) {
                // update the sort by
                session(['accounting_sorted_by_query'=>$sortedBy]);
                $accounting_sorted_by_query = $request->session()->get('accounting_sorted_by');
            } elseif (!is_null($request->session()->get('accounting_sorted_by'))) {
                // use the session value

                $accounting_sorted_by_query = $request->session()->get('accounting_sorted_by_query');
            } else {
                // set the default
                session(['accounting_sorted_by_query'=>'1']);
                $accounting_sorted_by_query = $request->session()->get('accounting_sorted_by_query');
            }


            /// If a new sort has been provided
            // Rebuild the query

            if (!is_null($sortedBy)) {
                switch ($request->query('accounting_asc_desc')) {
                    case '1':
                        # code...
                        session(['accounting_asc_desc'=> 'desc']);
                        $accountingAscDesc =  $request->session()->get('accounting_asc_desc');
                        session(['accounting_asc_desc_opposite' => ""]);
                        $accountingAscDescOpposite =  $request->session()->get('accounting_asc_desc_opposite');
                        break;

                    default:
                        session(['accounting_asc_desc'=> 'asc']);
                        $accountingAscDesc =  $request->session()->get('accounting_asc_desc');
                        session(['accounting_asc_desc_opposite' => 1]);
                        $accountingAscDescOpposite = $request->session()->get('accounting_asc_desc_opposite');
                        break;
                }
                switch ($sortedBy) {
                    case '1':
                        # date_entered
                        session(['accounting_sort_by' => 'transactions.date_entered']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '2':
                        # id
                        session(['accounting_sort_by' => 'transactions.id']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '3':
                        # type_id
                        session(['accounting_sort_by' => 'transactions.type_id']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '4':
                        # category_id
                        session(['accounting_sort_by' =>'transactions.transaction_category_id']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '5':
                        # transaction_note
                        session(['accounting_sort_by' =>'transactions.transaction_note']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '6':
                        # amount
                        session(['accounting_sort_by' => 'amount']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '7':
                        # total_requested
                        session(['accounting_sort_by' => 'amount']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '8':
                        #  total_approved
                        session(['accounting_sort_by' => 'total_approved']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '9':
                        #  total_amount
                        session(['accounting_sort_by' => 'total_invoiced']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '12':
                        #  total_paid
                        session(['accounting_sort_by' => 'total_paid']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    case '10':
                        #  breakout_item_status_name
                        session(['accounting_sort_by' => 'postat.breakout_item_status_name']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                    default:
                        # code...
                        session(['accounting_sort_by' => 'transactions.date_entered']);
                        $accountingSortBy = $request->session()->get('accounting_sort_by');
                        break;
                }
            } elseif (is_null($request->session()->get('accounting_sort_by'))) {
                // no values in the session - then store in simpler variables.
                session(['accounting_sort_by' => 'transactions.date_entered']);
                $accountingSortBy = $request->session()->get('accounting_sort_by');
                session(['accounting_asc_desc' => 'asc']);
                $accountingAscDesc = $request->session()->get('accounting_asc_desc');
                session(['accounting_asc_desc_opposite' => '1']);
                $accountingAscDescOpposite = $request->session()->get('accounting_asc_desc_opposite');
            } else {
                // use values in the session
                $accountingSortBy = $request->session()->get('accounting_sort_by');
                $accountingAscDesc = $request->session()->get('accounting_asc_desc');
                $accountingAscDescOpposite = $request->session()->get('accounting_asc_desc_opposite');
            }

            // Check if there is a Program Filter Provided
            if (is_numeric($request->query('accounting_program_filter'))) {
                //Update the session
                session(['accounting_program_filter' => $request->query('accounting_program_filter')]);
                $accountingProgramFilter = $request->session()->get('accounting_program_filter');
                session(['accounting_program_filter_operator' => '=']);
                $accountingProgramFilterOperator = $request->session()->get('accounting_program_filter_operator');
            } elseif (is_null($request->session()->get('accounting_program_filter')) || $request->query('accounting_program_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['accounting_program_filter' => '%%']);
                $accountingProgramFilter = $request->session()->get('accounting_program_filter');
                session(['accounting_program_filter_operator' => 'LIKE']);
                $accountingProgramFilterOperator = $request->session()->get('accounting_program_filter_operator');
            } else {
                // use values in the session
                $accountingProgramFilter = $request->session()->get('accounting_program_filter');
                $accountingProgramFilterOperator = $request->session()->get('accounting_program_filter_operator');
            }

            if (is_numeric($request->query('accounting_status_filter'))) {
                //Update the session
                session(['accounting_status_filter' => $request->query('accounting_status_filter')]);
                $accountingStatusFilter = $request->session()->get('accounting_status_filter');
                session(['accounting_status_filter_operator' => '=']);
                $accountingStatusFilterOperator = $request->session()->get('accounting_program_filter_operator');
            } elseif (is_null($request->session()->get('accounting_status_filter')) || $request->query('accounting_status_filter') == 'ALL') {
                // There is no Program Filter in the Session
                session(['accounting_status_filter' => '%%']);
                $accountingStatusFilter = $request->session()->get('accounting_status_filter');
                session(['accounting_status_filter_operator' => 'LIKE']);
                $accountingStatusFilterOperator = $request->session()->get('accounting_status_filter_operator');
            } else {
                // use values in the session
                $accountingStatusFilter = $request->session()->get('accounting_status_filter');
                $accountingStatusFilterOperator = $request->session()->get('accounting_status_filter_operator');
            }

            // Insert other Filters here

            $currentUser = Auth::user();



            /// run query
            $accounting = Transaction::select('transactions.*', 'accounts.account_name', 'program_name', 'entity_name', 'type_name', 'status_name', 'category_name')
                 ->join('accounts', 'account_id', '=', 'accounts.id')
                 ->join('programs', 'accounts.owner_id', '=', 'programs.id')
                 ->join('entities', 'accounts.entity_id', '=', 'entities.id')
                 ->join('transaction_types', 'transactions.type_id', '=', 'transaction_types.id')
                 ->join('transaction_statuses', 'transactions.status_id', '=', 'transaction_statuses.id')
                 ->join('transaction_categories', 'transactions.transaction_category_id', '=', 'transaction_categories.id')
                 /// FILTERS GO HERE
                 /// END FILTERS
                 ->orderBy("accounts.account_name", "asc")
                 ->orderBy("$accountingSortBy", "$accountingAscDesc")
                 ->where('programs.id', $accountingProgramFilterOperator, $accountingProgramFilter)
                 ->where('transaction_statuses.id', $accountingStatusFilterOperator, $accountingStatusFilter)
                 ->where('accounts.entity_id', $where_entity_id_operator, $where_entity_id)
                 ->get()
                 ->all();

            $accountingTotals = DB::select(DB::raw("
                SELECT p.program_name,
	               p.id as program_id,
	               p.entity_id,
	               pc.*,
	               ts.*,
	               tc.*,
	               tr.*,
	               tp.*,
	               ti.*

			        FROM programs p

			        INNER JOIN accounts a
			            ON p.entity_id = a.entity_id


			        INNER JOIN
			        (
			            SELECT a.id AS parcels_account_id,
			                   COUNT( pc.account_id ) AS Total_Parcels,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 1 THEN 1 END) AS LB__Pending,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 2 THEN 1 END) AS LB__Approved_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 3 THEN 1 END) AS LB__Withdrawn_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 4 THEN 1 END) AS LB__Declined_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 5 THEN 1 END) AS LB__InProcess_With_LB,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 6 THEN 1 END) AS LB__Ready_For_Signature_In_LB,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 7 THEN 1 END) AS LB__Ready_For_Submission_To_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 8 THEN 1 END) AS LB__Requested_Reimbursement,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 9 THEN 1 END) AS LB__Corrections_Requested_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 10 THEN 1 END) AS LB__Reimbursement_Approved_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 11 THEN 1 END) AS LB__Reimbursement_Declined_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 12 THEN 1 END) AS LB__Reimbursement_Withdrawn,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 13 THEN 1 END) AS LB__Invoiced_To_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 14 THEN 1 END) AS LB__Paid_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 15 THEN 1 END) AS LB__Disposition_Requested_To_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 16 THEN 1 END) AS LB__Disposition_Approved_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 17 THEN 1 END) AS LB__Disposition_Released_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 18 THEN 1 END) AS LB__Disposition_Declined_By_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 19 THEN 1 END) AS LB__Repayment_Required_From_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 20 THEN 1 END) AS LB__Repayment_Paid_To_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 41 THEN pc.account_id END) AS LB__Disposition_Invoice_Due_To_HFA,
			                   COUNT(CASE WHEN pc.landbank_property_status_id = 42 THEN pc.account_id END) AS LB__Dispostion_Paid_To_HFA,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 21 THEN 1 END) AS HFA__Compliance_Review,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 22 THEN 1 END) AS HFA__Processing,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 23 THEN 1 END) AS HFA__Corrections_Requested_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 24 THEN 1 END) AS HFA__Ready_For_Signators_In_HFA,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 25 THEN 1 END) AS HFA__Reimbursement_Denied_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 26 THEN 1 END) AS HFA__Reimbursement_Approved_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 27 THEN 1 END) AS HFA__Invoice_Received_From_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 28 THEN 1 END) AS HFA__Paid_Reimbursement,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 29 THEN 1 END) AS HFA__Disposition_Requested_By_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 30 THEN 1 END) AS HFA__Disposition_Approved_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 31 THEN 1 END) AS HFA__Disposition_Invoiced_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 32 THEN 1 END) AS HFA__Disposition_Paid_By_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 33 THEN 1 END) AS HFA__Disposition_Released_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 34 THEN 1 END) AS HFA__Repayment_Required_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 35 THEN pc.account_id END) AS HFA__Repayment_Invoiced_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 36 THEN pc.account_id END) AS HFA__Repayment_Received_From_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 37 THEN pc.account_id END) AS HFA__Withdrawn_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 38 THEN pc.account_id END) AS HFA__Unsubmitted,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 39 THEN pc.account_id END) AS HFA__Declined_To_LB,
			                   COUNT(CASE WHEN pc.hfa_property_status_id = 40 THEN pc.account_id END) AS HFA__PO_Sent_To_LB


			            FROM accounts a
			            LEFT JOIN parcels pc
			                ON a.id = pc.account_id
			            GROUP BY a.id
			        ) pc
			            ON a.id = pc.parcels_account_id


			        INNER JOIN
			        (
			            SELECT a.id AS transactions_account_id,
			                   SUM(CASE WHEN ts.transaction_category_id = 1 THEN ts.amount ELSE 0 END) AS Deposits_Made,
			                   SUM(CASE WHEN ts.transaction_category_id = 3 THEN ts.amount ELSE 0 END) AS Reimbursements_Paid,
			                   SUM(CASE WHEN ts.transaction_category_id = 2 THEN ts.amount ELSE 0 END) AS Recaptures_Received,
			                   SUM(CASE WHEN ts.transaction_category_id = 6 THEN ts.amount ELSE 0 END) AS Dispositions_Received,
			                   SUM(CASE WHEN ts.transaction_category_id = 4 THEN ts.amount ELSE 0 END) AS Transfers_Made,
			                   SUM(CASE WHEN ts.transaction_category_id = 5 THEN ts.amount ELSE 0 END) AS Line_Of_Credit


			            FROM accounts a
			            LEFT JOIN transactions ts
			                ON a.id = ts.account_id
			            GROUP BY a.id
			        ) ts
			            ON a.id = ts.transactions_account_id

			        INNER JOIN
			        (
			            SELECT a.id AS cost_account_id,
			                   SUM(CASE WHEN c.expense_category_id = 9 THEN c.amount ELSE 0 END) AS NIP_Loan_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 2 THEN c.amount ELSE 0 END) AS Acquisition_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 3 THEN c.amount ELSE 0 END) AS PreDemo_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 4 THEN c.amount ELSE 0 END) AS Demolition_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 5 THEN c.amount ELSE 0 END) AS Greening_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 6 THEN c.amount ELSE 0 END) AS Maintenance_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 7 THEN c.amount ELSE 0 END) AS Administration_Cost,
			                   SUM(CASE WHEN c.expense_category_id = 8 THEN c.amount ELSE 0 END) AS Other_Cost,
			               COALESCE(SUM(c.amount),0) AS Total_Cost
			            FROM accounts a
			            LEFT JOIN cost_items c
			                ON a.id = c.account_id
			            GROUP BY a.id
			        ) tc
			            ON a.id = tc.cost_account_id

			        INNER JOIN
			        (
			            SELECT a.id AS request_account_id,
			                   SUM(CASE WHEN r.expense_category_id = 9 THEN r.amount ELSE 0 END) AS NIP_Loan_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 2 THEN r.amount ELSE 0 END) AS Acquisition_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 3 THEN r.amount ELSE 0 END) AS PreDemo_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 4 THEN r.amount ELSE 0 END) AS Demolition_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 5 THEN r.amount ELSE 0 END) AS Greening_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 6 THEN r.amount ELSE 0 END) AS Maintenance_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 7 THEN r.amount ELSE 0 END) AS Administration_Requested,
			                   SUM(CASE WHEN r.expense_category_id = 8 THEN r.amount ELSE 0 END) AS Other_Requested,
			               COALESCE(SUM(r.amount),0) AS Total_Requested
			            FROM accounts a
			            LEFT JOIN request_items r
			                ON a.id = r.account_id
			            GROUP BY a.id
			        ) tr
			            ON a.id = tr.request_account_id

			        INNER JOIN
			        (
			            SELECT a.id AS po_account_id,
			                   SUM(CASE WHEN po.expense_category_id = 9 THEN po.amount ELSE 0 END) AS NIP_Loan_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 2 THEN po.amount ELSE 0 END) AS Acquisition_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 3 THEN po.amount ELSE 0 END) AS PreDemo_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 4 THEN po.amount ELSE 0 END) AS Demolition_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 5 THEN po.amount ELSE 0 END) AS Greening_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 6 THEN po.amount ELSE 0 END) AS Maintenance_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 7 THEN po.amount ELSE 0 END) AS Administration_Approved,
			                   SUM(CASE WHEN po.expense_category_id = 8 THEN po.amount ELSE 0 END) AS Other_Approved,
			               COALESCE(SUM(po.amount),0) AS Total_Approved
			            FROM accounts a
			            LEFT JOIN po_items po
			                ON a.id = po.account_id
			            GROUP BY a.id
			        ) tp
			            ON a.id = tp.po_account_id

			        INNER JOIN
			        (
			            SELECT a.id AS inv_account_id,
			                   SUM(CASE WHEN inv.expense_category_id = 9 THEN inv.amount ELSE 0 END) AS NIP_Loan_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 2 THEN inv.amount ELSE 0 END) AS Acquisition_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 3 THEN inv.amount ELSE 0 END) AS PreDemo_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 4 THEN inv.amount ELSE 0 END) AS Demolition_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 5 THEN inv.amount ELSE 0 END) AS Greening_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 6 THEN inv.amount ELSE 0 END) AS Maintenance_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 7 THEN inv.amount ELSE 0 END) AS Administration_Invoiced,
			                   SUM(CASE WHEN inv.expense_category_id = 8 THEN inv.amount ELSE 0 END) AS Other_Invoiced,
			               COALESCE(SUM(inv.amount),0) AS Total_Invoiced
			            FROM accounts a
			            LEFT JOIN invoice_items inv
			                ON a.id = inv.account_id
			            GROUP BY a.id
			        ) ti
			            ON a.id = ti.inv_account_id
			            #WHERE p.id <> 1
                        WHERE p.entity_id $where_entity_id_operator $where_entity_id
			            ORDER BY p.program_name
            "));

            $sumAcctData = [];

            foreach ($accountingTotals as $k => $subArray) {
                foreach ($subArray as $id => $value) {
                    if (is_numeric($value)) {
                        array_key_exists($id, $sumAcctData) ? $sumAcctData[$id] += $value : $sumAcctData[$id] = $value;
                    }
                }
            }

            $programs = Transaction::join('programs', 'transactions.owner_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->where('programs.entity_id', $where_entity_id_operator, $where_entity_id)->groupBy('programs.id', 'programs.program_name')->orderBy('program_name')->get()->all();
            $statuses = Transaction::join('transaction_statuses', 'transactions.status_id', '=', 'transaction_statuses.id')->select('transaction_statuses.status_name', 'transaction_statuses.id')->groupBy('transaction_statuses.id', 'transaction_statuses.status_name')->get()->all();

            $accounts = Account::where('accounts.entity_id', $where_entity_id_operator, $where_entity_id)->get();

            // $unpaidReimbursementInvoices = ReimbursementInvoice::join('programs', 'reimbursement_invoices.program_id', '=','programs.id' )
            //                                 ->select('programs.program_name','programs.id as program_id','reimbursement_invoices.id as invoice_id','reimbursement_invoices.created_at')
            //                                 ->where('programs.entity_id',$where_entity_id_operator,$where_entity_id)
            //                                 ->where('reimbursement_invoices.status_id',4)
            //                                 ->orderBy('program_name')
            //                                 ->orderBy('reimbursement_invoices.created_at')
            //                                 ->get()
            //                                 ->all();

            $unpaidReimbursementInvoices = ReimbursementInvoice::with('program')
            ->where('entity_id', $where_entity_id_operator, $where_entity_id)
            ->where('status_id', '<>', 6)

            ->orderBy('created_at', 'ASC')
            ->get()
            ->sortBy(function ($invoice) {
                return $invoice->program->program_name  ;
            });

            // $unpaid_disposition_invoices = DispositionInvoice::with('program')
            // ->where('entity_id',$where_entity_id_operator,$where_entity_id)
            // // ->where(function($query){
            // //     $query->balance();
            // // })
            // ->orderBy('created_at','ASC')
            // ->get()
            // ->sortBy(function($invoice)
            // {
            //     return $invoice->program->program_name  ;
            // });
            // $unpaid_disposition_invoices = $unpaid_disposition_invoices->filter(function($model){
            //     return $model->balance() > 0;
            //     });

            $unpaid_disposition_invoices = \App\Models\DispositionInvoice::with('program')
            ->where('entity_id', $where_entity_id_operator, $where_entity_id)
             ->where('status_id', '<>', 6)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->sortBy(function ($invoice) {
                return $invoice->program->program_name;
            });


            $accounts = $accounts->keyBy('id');
            //dd($accounts[34]->account_name);
            $accountingTotals = collect($accountingTotals);
            $accountingTotals = $accountingTotals->keyBy('program_id');


            /// return results

            return view('dashboard.accounting', compact(
                'accounts',
                'accounting',
                'accountingTotals',
                'programs',
                'statuses',
                'currentUser',
                'accounting_sorted_by_query',
                'accountingAscDesc',
                'accountingAscDescOpposite',
                'accountingProgramFilter',
                'accountingStatusFilter',
                'unpaidReimbursementInvoices',
                'unpaid_disposition_invoices'
            ));
        } else {
            return 'Sorry you do not have access to the Accounting page. Please try logging in again or contact your admin to request access.';
        }
    }

    /**
     * Edit Transaction
     *
     * @param \App\Models\Transaction $transaction
     * @param null              $reload
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function editTransaction(Transaction $transaction, $reload = null)
    {
        if (Auth::user() != null && (Auth::user()->isHFAFiscalAgent() || Auth::user()->isHFAAdmin())) {
            $statuses = \App\Models\TransactionStatus::get()->toArray();
            $status_array = [];
            foreach ($statuses as $status) {
                $status_array[$status['id']]['name'] = $status['status_name'];
                $status_array[$status['id']]['id'] = $status['id'];
            }


            return view('modals.transaction-edit', compact('transaction', 'status_array', 'reload'));
        } else {
            return "Sorry, you are not allowed to modify transactions.";
        }
    }

    /**
     * Save Transaction
     *
     * @param \App\Models\Transaction        $transaction
     * @param \Illuminate\Http\Request $request
     *
     * @return int|string
     */
    public function saveTransaction(Transaction $transaction, Request $request)
    {
        if (Auth::user()->isHFAFiscalAgent() || Auth::user()->isHFAAdmin()) {
            $forminputs = $request->get('inputs');
            parse_str($forminputs, $forminputs);
            if (!isset($forminputs['active'])) {
                $forminputs['active'] = 0;
            }

            if ($forminputs['created_at']) {
                $forminput_created_at = $forminputs['created_at'];
                $created = Carbon\Carbon::createFromFormat('Y-m-d', $forminput_created_at)->format('Y-m-d H:i:s');
            } else {
                $created = null;
            }
            if ($forminputs['date_cleared']) {
                $forminput_date_cleared = $forminputs['date_cleared'];
                $date_cleared = Carbon\Carbon::createFromFormat('Y-m-d', $forminput_date_cleared)->format('Y-m-d H:i:s');
            } else {
                $date_cleared = null;
            }

            $transaction->update([
                'date_entered' => $created,
                'date_cleared' => $date_cleared,
                'transaction_note'=>$forminputs['transaction_note'],
                'amount'=>$forminputs['amount'],
                'status_id' => $forminputs['status_id']
                ]);

            return 1;
        } else {
            return "Sorry, you are not allowed to see this transaction.";
        }
    }

    /**
     * State Breakdown
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $program
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function statBreakDown(Request $request, $program)
    {
        $stat = 1;
        if ($stat == 1) {
            // $lc = new LogConverter('stats', 'view');
            // $lc->setFrom(Auth::user())->setTo(Auth::user())->setDesc(Auth::user()->email . ' Viewed statBreakDown')->save();

            $program = \App\Models\Program::select('id as program_id')->where('id', $program)->first();

            if (is_numeric($program->program_id)) {
                $averageData = DB::select(
                    DB::raw("
		                SELECT
			               	p.id as program_id,
			            	p.entity_id,
			               	tc.*,
			               	tr.*,
			               	tp.*,
			               	ti.*


				        FROM programs p

				        INNER JOIN accounts a
				            ON p.entity_id = a.entity_id

				        INNER JOIN
				        (
				            SELECT a.id AS cost_account_id,
				                   AVG(CASE WHEN c.expense_category_id = 9 AND c.amount > 0 THEN c.amount ELSE 0 END) AS NIP_Loan_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 2 THEN c.amount ELSE 0 END) AS Acquisition_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 3 THEN c.amount ELSE 0 END) AS PreDemo_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 4 THEN c.amount ELSE 0 END) AS Demolition_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 5 THEN c.amount ELSE 0 END) AS Greening_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 6 THEN c.amount ELSE 0 END) AS Maintenance_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 7 THEN c.amount ELSE 0 END) AS Administration_Cost_Average,
				                   AVG(CASE WHEN c.expense_category_id = 8 THEN c.amount ELSE 0 END) AS Other_Cost_Average,
				               COALESCE(AVG(c.amount),0) AS Total_Cost_Average
				            FROM accounts a
				            LEFT JOIN cost_items c
				                ON a.id = c.account_id

				            GROUP BY a.id

				        ) tc
				            ON a.id = tc.cost_account_id






				        INNER JOIN
				        (
				            SELECT a.id AS request_account_id,
				                   AVG(CASE WHEN r.expense_category_id = 9 THEN r.amount ELSE 0 END) AS NIP_Loan_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 2 THEN r.amount ELSE 0 END) AS Acquisition_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 3 THEN r.amount ELSE 0 END) AS PreDemo_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 4 THEN r.amount ELSE 0 END) AS Demolition_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 5 THEN r.amount ELSE 0 END) AS Greening_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 6 THEN r.amount ELSE 0 END) AS Maintenance_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 7 THEN r.amount ELSE 0 END) AS Administration_Requested_Average,
				                   AVG(CASE WHEN r.expense_category_id = 8 THEN r.amount ELSE 0 END) AS Other_Requested_Average,
				               COALESCE(AVG(r.amount),0) AS Total_Requested_Average
				            FROM accounts a
				            LEFT JOIN request_items r
				                ON a.id = r.account_id
				            GROUP BY a.id
				        ) tr
				            ON a.id = tr.request_account_id

				        INNER JOIN
				        (
				            SELECT a.id AS po_account_id,
				                   AVG(CASE WHEN po.expense_category_id = 9 THEN po.amount ELSE 0 END) AS NIP_Loan_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 2 THEN po.amount ELSE 0 END) AS Acquisition_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 3 THEN po.amount ELSE 0 END) AS PreDemo_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 4 THEN po.amount ELSE 0 END) AS Demolition_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 5 THEN po.amount ELSE 0 END) AS Greening_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 6 THEN po.amount ELSE 0 END) AS Maintenance_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 7 THEN po.amount ELSE 0 END) AS Administration_Approved_Average,
				                   AVG(CASE WHEN po.expense_category_id = 8 THEN po.amount ELSE 0 END) AS Other_Approved_Average,
				               COALESCE(AVG(po.amount),0) AS Total_Approved_Average
				            FROM accounts a
				            LEFT JOIN po_items po
				                ON a.id = po.account_id
				            GROUP BY a.id
				        ) tp
				            ON a.id = tp.po_account_id

				        INNER JOIN
				        (
				            SELECT a.id AS inv_account_id,
				                   AVG(CASE WHEN inv.expense_category_id = 9 THEN inv.amount ELSE 0 END) AS NIP_Loan_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 2 THEN inv.amount ELSE 0 END) AS Acquisition_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 3 THEN inv.amount ELSE 0 END) AS PreDemo_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 4 THEN inv.amount ELSE 0 END) AS Demolition_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 5 THEN inv.amount ELSE 0 END) AS Greening_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 6 THEN inv.amount ELSE 0 END) AS Maintenance_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 7 THEN inv.amount ELSE 0 END) AS Administration_Invoiced_Average,
				                   AVG(CASE WHEN inv.expense_category_id = 8 THEN inv.amount ELSE 0 END) AS Other_Invoiced_Average,
				               COALESCE(AVG(inv.amount),0) AS Total_Invoiced_Average
				            FROM accounts a
				            LEFT JOIN invoice_items inv
				                ON a.id = inv.account_id
				            GROUP BY a.id
				        ) ti
				            ON a.id = ti.inv_account_id

				        WHERE p.id = $program->program_id

		            ")
                );

                $dropTempTables = DB::unprepared(

                    DB::raw("
						#########################################################
						#####################################
						###DROP COST TABLES IF THEY EXIST
						DROP TABLE IF EXISTS nip_loan_cost_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_cost_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_cost_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_cost_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_cost_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_cost_table_temp_b ;

						DROP TABLE IF EXISTS demolition_cost_table_temp_a ;
						DROP TABLE IF EXISTS demolition_cost_table_temp_b ;

						DROP TABLE IF EXISTS greening_cost_table_temp_a ;
						DROP TABLE IF EXISTS greening_cost_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_cost_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_cost_table_temp_b ;

						DROP TABLE IF EXISTS administration_cost_table_temp_a ;
						DROP TABLE IF EXISTS administration_cost_table_temp_b ;

						DROP TABLE IF EXISTS other_cost_table_temp_a ;
						DROP TABLE IF EXISTS other_cost_table_temp_b ;

						DROP TABLE IF EXISTS total_cost_table_temp_a ;
						DROP TABLE IF EXISTS total_cost_table_temp_b ;

						###DROP REQUEST TABLES IF THEY EXIST

						DROP TABLE IF EXISTS nip_loan_request_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_request_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_request_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_request_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_request_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_request_table_temp_b ;

						DROP TABLE IF EXISTS demolition_request_table_temp_a ;
						DROP TABLE IF EXISTS demolition_request_table_temp_b ;

						DROP TABLE IF EXISTS greening_request_table_temp_a ;
						DROP TABLE IF EXISTS greening_request_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_request_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_request_table_temp_b ;

						DROP TABLE IF EXISTS administration_request_table_temp_a ;
						DROP TABLE IF EXISTS administration_request_table_temp_b ;

						DROP TABLE IF EXISTS other_request_table_temp_a ;
						DROP TABLE IF EXISTS other_request_table_temp_b ;

						DROP TABLE IF EXISTS total_request_table_temp_a ;
						DROP TABLE IF EXISTS total_request_table_temp_b ;

						###DROP PO TABLES IF THEY EXIST

						DROP TABLE IF EXISTS nip_loan_po_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_po_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_po_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_po_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_po_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_po_table_temp_b ;

						DROP TABLE IF EXISTS demolition_po_table_temp_a ;
						DROP TABLE IF EXISTS demolition_po_table_temp_b ;

						DROP TABLE IF EXISTS greening_po_table_temp_a ;
						DROP TABLE IF EXISTS greening_po_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_po_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_po_table_temp_b ;

						DROP TABLE IF EXISTS administration_po_table_temp_a ;
						DROP TABLE IF EXISTS administration_po_table_temp_b ;

						DROP TABLE IF EXISTS other_po_table_temp_a ;
						DROP TABLE IF EXISTS other_po_table_temp_b ;

						DROP TABLE IF EXISTS total_po_table_temp_a ;
						DROP TABLE IF EXISTS total_po_table_temp_b ;

						###DROP INVOICE TABLES IF THEY EXIST

						DROP TABLE IF EXISTS nip_loan_invoice_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_invoice_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_invoice_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_invoice_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_invoice_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_invoice_table_temp_b ;

						DROP TABLE IF EXISTS demolition_invoice_table_temp_a ;
						DROP TABLE IF EXISTS demolition_invoice_table_temp_b ;

						DROP TABLE IF EXISTS greening_invoice_table_temp_a ;
						DROP TABLE IF EXISTS greening_invoice_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_invoice_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_invoice_table_temp_b ;

						DROP TABLE IF EXISTS administration_invoice_table_temp_a ;
						DROP TABLE IF EXISTS administration_invoice_table_temp_b ;

						DROP TABLE IF EXISTS other_invoice_table_temp_a ;
						DROP TABLE IF EXISTS other_invoice_table_temp_b ;

						DROP TABLE IF EXISTS total_invoice_table_temp_a ;
						DROP TABLE IF EXISTS total_invoice_table_temp_b ;



					")
                );
                $createTempTables = DB::unprepared(
                    DB::raw("


						#########################################################
						#####################################
						###NIP LOAN PAYOFF COST
						CREATE TEMPORARY TABLE nip_loan_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE nip_loan_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###ACQUISITION COST
						CREATE TEMPORARY TABLE acquisition_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE acquisition_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###PRE-DEMO COST
						CREATE TEMPORARY TABLE pre_demo_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE pre_demo_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###DEMOLITION COST
						CREATE TEMPORARY TABLE demolition_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE demolition_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###GREENING COST
						CREATE TEMPORARY TABLE greening_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE greening_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###MAINTENANCE COST
						CREATE TEMPORARY TABLE maintenance_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE maintenance_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###ADMINISTRATION COST
						CREATE TEMPORARY TABLE administration_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE administration_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###OTHER COST
						CREATE TEMPORARY TABLE other_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE other_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###TOTAL COST
						CREATE TEMPORARY TABLE total_cost_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE total_cost_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM cost_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################
						#####################################
						#########################################################
						#########################################################
						#####################################
						###REQUEST TABLES


						#########################################################
						#####################################
						###NIP LOAN PAYOFF REQUEST
						CREATE TEMPORARY TABLE nip_loan_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE nip_loan_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###ACQUISITION REQUEST
						CREATE TEMPORARY TABLE acquisition_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE acquisition_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###PRE-DEMO REQUEST
						CREATE TEMPORARY TABLE pre_demo_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE pre_demo_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###DEMOLITION REQUEST
						CREATE TEMPORARY TABLE demolition_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE demolition_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###GREENING REQUEST
						CREATE TEMPORARY TABLE greening_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE greening_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###MAINTENANCE REQUEST
						CREATE TEMPORARY TABLE maintenance_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE maintenance_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###ADMINISTRATION REQUEST
						CREATE TEMPORARY TABLE administration_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE administration_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###OTHER REQUEST
						CREATE TEMPORARY TABLE other_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE other_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###TOTAL REQUEST
						CREATE TEMPORARY TABLE total_request_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE total_request_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM request_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################
						#####################################
						#########################################################
						#########################################################
						#####################################
						###PO TABLES


						#########################################################
						#####################################
						###NIP LOAN PAYOFF PO
						CREATE TEMPORARY TABLE nip_loan_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE nip_loan_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###ACQUISITION PO
						CREATE TEMPORARY TABLE acquisition_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE acquisition_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###PRE-DEMO PO
						CREATE TEMPORARY TABLE pre_demo_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE pre_demo_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###DEMOLITION PO
						CREATE TEMPORARY TABLE demolition_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE demolition_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###GREENING PO
						CREATE TEMPORARY TABLE greening_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE greening_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###MAINTENANCE PO
						CREATE TEMPORARY TABLE maintenance_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE maintenance_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###ADMINISTRATION PO
						CREATE TEMPORARY TABLE administration_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE administration_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###OTHER PO
						CREATE TEMPORARY TABLE other_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE other_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###TOTAL PO
						CREATE TEMPORARY TABLE total_po_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE total_po_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM po_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################
						#####################################
						#########################################################
						#########################################################
						#####################################
						###INVOICE TABLES


						#########################################################
						#####################################
						###NIP LOAN PAYOFF INVOICE
						CREATE TEMPORARY TABLE nip_loan_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE nip_loan_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 9 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###ACQUISITION INVOICE
						CREATE TEMPORARY TABLE acquisition_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE acquisition_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 2 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###PRE-DEMO INVOICE
						CREATE TEMPORARY TABLE pre_demo_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE pre_demo_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 3 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###DEMOLITION INVOICE
						CREATE TEMPORARY TABLE demolition_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE demolition_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 4 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

						#########################################################
						#####################################
						###GREENING INVOICE
						CREATE TEMPORARY TABLE greening_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE greening_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 5 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###MAINTENANCE INVOICE
						CREATE TEMPORARY TABLE maintenance_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE maintenance_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 6 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###ADMINISTRATION INVOICE
						CREATE TEMPORARY TABLE administration_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE administration_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 7 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###OTHER INVOICE
						CREATE TEMPORARY TABLE other_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE other_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE expense_category_id = 8 AND program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);


						#########################################################
						#####################################
						###TOTAL INVOICE
						CREATE TEMPORARY TABLE total_invoice_table_temp_a
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);
						CREATE TEMPORARY TABLE total_invoice_table_temp_b
						AS (
							SELECT
								parcel_id,
								program_id,
								sum(amount) as amount
							FROM invoice_items
							WHERE program_id = $program->program_id
							AND amount > 0
							GROUP BY parcel_id
							);

					")
                );
                ##
                if ($createTempTables) {
                    $medianData = DB::select(
                        DB::raw("



						#########################################################
						#####################################
						###SELECTION
						SELECT

						p.id as program_id,
						nc.median_nip_loan_cost,
						ac.median_acquisition_cost,
						pc.median_pre_demo_cost,
						dc.median_demolition_cost,
						gc.median_greening_cost,
						mc.median_maintenance_cost,
						adc.median_administration_cost,
						oc.median_other_cost,
						tc.median_total_cost,

						nr.median_nip_loan_request,
						ar.median_acquisition_request,
						pr.median_pre_demo_request,
						dr.median_demolition_request,
						gr.median_greening_request,
						mr.median_maintenance_request,
						adr.median_administration_request,
						otr.median_other_request,
						tr.median_total_request,

						npo.median_nip_loan_po,
						apo.median_acquisition_po,
						ppo.median_pre_demo_po,
						dpo.median_demolition_po,
						gpo.median_greening_po,
						mpo.median_maintenance_po,
						adpo.median_administration_po,
						otpo.median_other_po,
						tpo.median_total_po,

						ni.median_nip_loan_invoice,
						ai.median_acquisition_invoice,
						pi.median_pre_demo_invoice,
						di.median_demolition_invoice,
						gi.median_greening_invoice,
						mi.median_maintenance_invoice,
						adi.median_administration_invoice,
						oti.median_other_invoice,
						ti.median_total_invoice

						FROM programs p

						### MEDIAN COST JOINS
						LEFT JOIN(
							#NIP LOAN PAYOFF COST
							SELECT
								avg(t1.amount) AS median_nip_loan_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												nip_loan_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													nip_loan_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)

							)nc on 1 = 1

						LEFT JOIN(
							#ACQUISITION COST
							SELECT
								avg(t1.amount) AS median_acquisition_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												acquisition_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													acquisition_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)ac on 1 = 1

						LEFT JOIN(
							#PREDEMO COST
							SELECT
								avg(t1.amount) AS median_pre_demo_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												pre_demo_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													pre_demo_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)pc on 1 = 1



						LEFT JOIN(
							#DEMOLITION COST
							SELECT
								avg(t1.amount) AS median_demolition_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												demolition_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													demolition_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)dc on 1 = 1

						LEFT JOIN(
							#GREENING COST
							SELECT
								avg(t1.amount) AS median_greening_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												greening_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													greening_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)gc on 1 = 1

						LEFT JOIN(
							#MAINTENANCE COST
							SELECT
								avg(t1.amount) AS median_maintenance_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												maintenance_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													maintenance_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)mc on 1 = 1

						LEFT JOIN(
							#ADMINISTRATION COST
							SELECT
								avg(t1.amount) AS median_administration_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												administration_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													administration_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)adc on 1 = 1

						LEFT JOIN(
							#OTHER COST
							SELECT
								avg(t1.amount) AS median_other_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												other_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													other_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)oc on 1 = 1

						LEFT JOIN(
							#TOTAL COST
							SELECT
								avg(t1.amount) AS median_total_cost
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												total_cost_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													total_cost_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)tc on 1 = 1

						### MEDIAN REQUEST JOINS
						LEFT JOIN(
							#NIP LOAN PAYOFF REQUEST
							SELECT
								avg(t1.amount) AS median_nip_loan_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												nip_loan_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													nip_loan_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)

							)nr on 1 = 1

						LEFT JOIN(
							#ACQUISITION REQUEST
							SELECT
								avg(t1.amount) AS median_acquisition_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												acquisition_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													acquisition_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)ar on 1 = 1

						LEFT JOIN(
							#PREDEMO REQUEST
							SELECT
								avg(t1.amount) AS median_pre_demo_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												pre_demo_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													pre_demo_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)pr on 1 = 1



						LEFT JOIN(
							#DEMOLITION REQUEST
							SELECT
								avg(t1.amount) AS median_demolition_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												demolition_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													demolition_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)dr on 1 = 1

						LEFT JOIN(
							#GREENING REQUEST
							SELECT
								avg(t1.amount) AS median_greening_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												greening_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													greening_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)gr on 1 = 1

						LEFT JOIN(
							#MAINTENANCE REQUEST
							SELECT
								avg(t1.amount) AS median_maintenance_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												maintenance_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													maintenance_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)mr on 1 = 1

						LEFT JOIN(
							#ADMINISTRATION REQUEST
							SELECT
								avg(t1.amount) AS median_administration_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												administration_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													administration_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)adr on 1 = 1

						LEFT JOIN(
							#OTHER REQUEST
							SELECT
								avg(t1.amount) AS median_other_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												other_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													other_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)otr on 1 = 1

						LEFT JOIN(
							#TOTAL REQUEST
							SELECT
								avg(t1.amount) AS median_total_request
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												total_request_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													total_request_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)tr on 1 = 1


						### MEDIAN PO JOINS
						LEFT JOIN(
							#NIP LOAN PAYOFF PO
							SELECT
								avg(t1.amount) AS median_nip_loan_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												nip_loan_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													nip_loan_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)

							)npo on 1 = 1

						LEFT JOIN(
							#ACQUISITION PO
							SELECT
								avg(t1.amount) AS median_acquisition_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												acquisition_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													acquisition_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)apo on 1 = 1

						LEFT JOIN(
							#PREDEMO PO
							SELECT
								avg(t1.amount) AS median_pre_demo_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												pre_demo_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													pre_demo_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)ppo on 1 = 1



						LEFT JOIN(
							#DEMOLITION PO
							SELECT
								avg(t1.amount) AS median_demolition_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												demolition_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													demolition_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)dpo on 1 = 1

						LEFT JOIN(
							#GREENING PO
							SELECT
								avg(t1.amount) AS median_greening_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												greening_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													greening_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)gpo on 1 = 1

						LEFT JOIN(
							#MAINTENANCE PO
							SELECT
								avg(t1.amount) AS median_maintenance_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												maintenance_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													maintenance_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)mpo on 1 = 1

						LEFT JOIN(
							#ADMINISTRATION PO
							SELECT
								avg(t1.amount) AS median_administration_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												administration_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													administration_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)adpo on 1 = 1

						LEFT JOIN(
							#OTHER PO
							SELECT
								avg(t1.amount) AS median_other_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												other_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													other_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)otpo on 1 = 1

						LEFT JOIN(
							#TOTAL PO
							SELECT
								avg(t1.amount) AS median_total_po
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												total_po_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													total_po_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)tpo on 1 = 1


						### MEDIAN INVOICE JOINS
						LEFT JOIN(
							#NIP LOAN PAYOFF INVOICE
							SELECT
								avg(t1.amount) AS median_nip_loan_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												nip_loan_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													nip_loan_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)

							)ni on 1 = 1

						LEFT JOIN(
							#ACQUISITION INVOICE
							SELECT
								avg(t1.amount) AS median_acquisition_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												acquisition_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													acquisition_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)ai on 1 = 1

						LEFT JOIN(
							#PREDEMO INVOICE
							SELECT
								avg(t1.amount) AS median_pre_demo_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												pre_demo_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													pre_demo_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)pi on 1 = 1



						LEFT JOIN(
							#DEMOLITION INVOICE
							SELECT
								avg(t1.amount) AS median_demolition_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												demolition_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													demolition_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)di on 1 = 1

						LEFT JOIN(
							#GREENING INVOICE
							SELECT
								avg(t1.amount) AS median_greening_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												greening_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													greening_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)gi on 1 = 1

						LEFT JOIN(
							#MAINTENANCE INVOICE
							SELECT
								avg(t1.amount) AS median_maintenance_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												maintenance_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													maintenance_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)mi on 1 = 1

						LEFT JOIN(
							#ADMINISTRATION INVOICE
							SELECT
								avg(t1.amount) AS median_administration_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												administration_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													administration_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)adi on 1 = 1

						LEFT JOIN(
							#OTHER INVOICE
							SELECT
								avg(t1.amount) AS median_other_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												other_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													other_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)oti on 1 = 1

						LEFT JOIN(
							#TOTAL INVOICE
							SELECT
								avg(t1.amount) AS median_total_invoice
									FROM
										(
											SELECT
												@rownum :=@rownum + 1 AS `row_number` ,
												d.amount,
												d.program_id
												FROM
												total_invoice_table_temp_a d,
												(SELECT @rownum := 0) r
													WHERE
													1

													ORDER BY
														d.amount
												) AS t1 ,
												(SELECT
													count(*) AS total_rows
												FROM
													total_invoice_table_temp_b d
												WHERE
												1

											) AS t2

									WHERE
										1
									AND t1.row_number IN(
										floor((t2.total_rows + 1) / 2) ,
										floor((t2.total_rows + 2) / 2)
										)
							)ti on 1 = 1



						WHERE p.id = $program->program_id

						#GROUP BY p.id;

					")
                    );
                } else {
                    return "There was a problem trying to create a temporary table to hold the aggregate totals for this program with an id number of ".$program->program_id.". Please let the nearest IT person know. Tell them Allita sent you, and to check out accounting line 1744. More than likely this failed because there isn't any data for the stats on the program yet.";
                }
                DB::unprepared(
                    DB::raw("
						#########################################################
						#####################################
						###DROP COST TABLES IF THEY EXIST
						DROP TABLE IF EXISTS nip_loan_cost_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_cost_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_cost_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_cost_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_cost_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_cost_table_temp_b ;

						DROP TABLE IF EXISTS demolition_cost_table_temp_a ;
						DROP TABLE IF EXISTS demolition_cost_table_temp_b ;

						DROP TABLE IF EXISTS greening_cost_table_temp_a ;
						DROP TABLE IF EXISTS greening_cost_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_cost_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_cost_table_temp_b ;

						DROP TABLE IF EXISTS administration_cost_table_temp_a ;
						DROP TABLE IF EXISTS administration_cost_table_temp_b ;

						DROP TABLE IF EXISTS other_cost_table_temp_a ;
						DROP TABLE IF EXISTS other_cost_table_temp_b ;

						DROP TABLE IF EXISTS total_cost_table_temp_a ;
						DROP TABLE IF EXISTS total_cost_table_temp_b ;

						###DROP REQUEST TABLES IF THEY EXIST

						DROP TABLE IF EXISTS nip_loan_request_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_request_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_request_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_request_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_request_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_request_table_temp_b ;

						DROP TABLE IF EXISTS demolition_request_table_temp_a ;
						DROP TABLE IF EXISTS demolition_request_table_temp_b ;

						DROP TABLE IF EXISTS greening_request_table_temp_a ;
						DROP TABLE IF EXISTS greening_request_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_request_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_request_table_temp_b ;

						DROP TABLE IF EXISTS administration_request_table_temp_a ;
						DROP TABLE IF EXISTS administration_request_table_temp_b ;

						DROP TABLE IF EXISTS other_request_table_temp_a ;
						DROP TABLE IF EXISTS other_request_table_temp_b ;

						DROP TABLE IF EXISTS total_request_table_temp_a ;
						DROP TABLE IF EXISTS total_request_table_temp_b ;

						###DROP PO TABLES IF THEY EXIST

						DROP TABLE IF EXISTS nip_loan_po_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_po_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_po_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_po_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_po_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_po_table_temp_b ;

						DROP TABLE IF EXISTS demolition_po_table_temp_a ;
						DROP TABLE IF EXISTS demolition_po_table_temp_b ;

						DROP TABLE IF EXISTS greening_po_table_temp_a ;
						DROP TABLE IF EXISTS greening_po_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_po_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_po_table_temp_b ;

						DROP TABLE IF EXISTS administration_po_table_temp_a ;
						DROP TABLE IF EXISTS administration_po_table_temp_b ;

						DROP TABLE IF EXISTS other_po_table_temp_a ;
						DROP TABLE IF EXISTS other_po_table_temp_b ;

						DROP TABLE IF EXISTS total_po_table_temp_a ;
						DROP TABLE IF EXISTS total_po_table_temp_b ;

						###DROP INVOICE TABLES IF THEY EXIST

						DROP TABLE IF EXISTS nip_loan_invoice_table_temp_a ;
						DROP TABLE IF EXISTS nip_loan_invoice_table_temp_b ;

						DROP TABLE IF EXISTS acquisition_invoice_table_temp_a ;
						DROP TABLE IF EXISTS acquisition_invoice_table_temp_b ;

						DROP TABLE IF EXISTS pre_demo_invoice_table_temp_a ;
						DROP TABLE IF EXISTS pre_demo_invoice_table_temp_b ;

						DROP TABLE IF EXISTS demolition_invoice_table_temp_a ;
						DROP TABLE IF EXISTS demolition_invoice_table_temp_b ;

						DROP TABLE IF EXISTS greening_invoice_table_temp_a ;
						DROP TABLE IF EXISTS greening_invoice_table_temp_b ;

						DROP TABLE IF EXISTS maintenance_invoice_table_temp_a ;
						DROP TABLE IF EXISTS maintenance_invoice_table_temp_b ;

						DROP TABLE IF EXISTS administration_invoice_table_temp_a ;
						DROP TABLE IF EXISTS administration_invoice_table_temp_b ;

						DROP TABLE IF EXISTS other_invoice_table_temp_a ;
						DROP TABLE IF EXISTS other_invoice_table_temp_b ;

						DROP TABLE IF EXISTS total_invoice_table_temp_a ;
						DROP TABLE IF EXISTS total_invoice_table_temp_b ;


					")
                );
                $averageData = $averageData[0];
                $medianData = $medianData[0];
                //dd($averageData, $medianData);
                $data = $averageData;

                return view('modals.stat-av-median-mode', compact('data'));
            } else {
                return 'Sorry an invalid id supplied';
            }
        } else {
            return 'Sorry you do not have access to the Stats Breakdown page. Please try logging in again or contact your admin to request access.';
        }
    }
}

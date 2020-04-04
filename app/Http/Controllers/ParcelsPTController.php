<?php
// To combine with ParcelsController.php

namespace App\Http\Controllers;

use Auth;
use Gate;
use Carbon;
use File;
use Storage;
use App\Models\Programs;
use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Models\Parcel;
use App\LogConverter;
use App\Models\ExpenseCategory;
use App\Models\CostItem;
use App\Models\ReimbursementRequest;
use App\Models\RequestItem;
use App\Models\Entity;
use App\Models\ParcelsToReimbursementRequest;
use App\Models\RequestNote;
use App\Models\PurchaseOrderNote;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentRule;
use App\Models\DocumentRuleEntry;
use App\Models\ApprovalRequest;
use App\Models\ApprovalAction;
use App\Models\ReimbursementPurchaseOrders;
use App\Models\ParcelsToPurchaseOrder;
use App\Models\ParcelsToReimbursementInvoice;
use App\Models\PoItems;
use App\Models\InvoiceItem;
use App\Models\ReimbursementInvoice;
use App\Models\Mail\EmailNotificationPOApproved;
use App\Models\Compliance;
use App\Models\ProgramRule;

class ParcelsPTController extends Controller
{
     public function __construct(){
        $this->allitapc();
    }

    public function breakouts(Parcel $parcel, Request $request, $format = null)
    {
        $status = DB::table('property_status_options')->where('property_status_options.id', $parcel->hfa_property_status_id)->get()->all();
        //DB::enableQueryLog();
        $breakouts = DB::table('cost_items')
                        ->leftJoin('request_items', 'request_items.ref_id', '=', 'cost_items.id')
                        ->leftJoin('po_items', 'po_items.ref_id', '=', 'request_items.id')
                        ->leftJoin('invoice_items', 'invoice_items.ref_id', '=', 'po_items.id')
                        ->leftJoin('break_out_types', 'cost_items.breakout_type', '=', 'break_out_types.id')
                        ->leftJoin('expense_categories', 'cost_items.expense_category_id', '=', 'expense_categories.id')
                        ->leftJoin('breakout_items_statuses as invoice_status', 'invoice_items.breakout_item_status_id', '=', 'invoice_status.id')
                        ->leftJoin('breakout_items_statuses as po_status', 'po_items.breakout_item_status_id', '=', 'po_status.id')
                        ->leftJoin('breakout_items_statuses as request_status', 'request_items.breakout_item_status_id', '=', 'request_status.id')
                        ->leftJoin('breakout_items_statuses as cost_status', 'cost_items.breakout_item_status_id', '=', 'cost_status.id')
                        ->leftJoin('retainages', 'retainages.cost_item_id', '=', 'cost_items.id')
                        ->join('vendors', 'cost_items.vendor_id', '=', 'vendors.id')
                        ->select(
                            'cost_items.created_at as date',
                            'cost_items.description',
                            'cost_items.notes',
                            'request_items.req_id',
                            'po_items.po_id',
                            'invoice_items.id as invoice_item_id',
                            'invoice_items.amount as invoice_amount',
                            'po_items.po_id',
                            'po_items.id as approved_item_id',
                            'po_items.amount as approved_amount',
                            'request_items.req_id',
                            'request_items.id as requested_item_id',
                            'request_items.amount as requested_amount',
                            'cost_items.id as cost_item_id',
                            'cost_items.amount as cost_amount',
                            'vendors.id as vendor_id',
                            'vendors.vendor_name',
                            'invoice_status.id',
                            'po_status.id',
                            'request_status.id',
                            'cost_status.id',
                            'invoice_status.breakout_item_status_name as invoice_status',
                            'po_status.breakout_item_status_name as po_status',
                            'request_status.breakout_item_status_name as request_status',
                            'cost_status.breakout_item_status_name as cost_status',
                            'break_out_types.id as breakout_type_id',
                            'break_out_types.breakout_type_name',
                            'expense_categories.id as expense_category_id',
                            'expense_categories.expense_category_name',
                            'cost_items.advance as advance',
                            'retainages.id as retainage_id'
                        )
                        ->where('cost_items.parcel_id', $parcel->id)
                        ->get()
                        ->all();

        $parcel_id = $parcel->id;

        // check if all documents have been uploaded
        // get categories that are needed
        $docRules = DocumentRule::where('program_rules_id', $parcel->program_rules_id)->pluck('id');
        $docCatIds = DocumentRuleEntry::whereIn('document_rule_id', $docRules)->pluck('document_category_id');
        $parcelRules = \App\Models\ProgramRule::find($parcel->program_rules_id);
        if (is_array($docCatIds)) {
            $categories_needed =  $docCatIds;
        } else {
            $categories_needed =  [];
        }

        // get categoties that were submitted
        $documents = Document::where('parcel_id', $parcel->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();
        // build a list of all categories used for uploaded documents in this parcel
        $categories_used = [];
        // category keys for name reference ['id' => 'name']
        $document_categories_key = [];
        $all_documents_approved = 0;
        $all_documents_uploaded = 0;
        $previous_document_approved = 1;

        if (count($documents)) {
            // create an associative array to simplify category references for each document
            foreach ($documents as $document) {
                $categories = []; // store the new associative array cat id, cat name
                 
                if ($document->categories) {
                    $categories_decoded = json_decode($document->categories, true); // cats used by the doc

                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = [];
                }

                foreach ($document_categories as $document_category) {
                    $document_categories_key[$document_category->id] = $document_category->document_category_name;

                    // sub key for each document's categories for quick reference
                    if (in_array($document_category->id, $categories_decoded)) {
                        $categories[$document_category->id] = $document_category->document_category_name;
                    }
                }
                $document->categoriesarray = $categories;

                // get approved category id in an array
                if ($document->approved) {
                    $document->approved_array = json_decode($document->approved, true);
                } else {
                    $document->approved_array = [];
                }

                // get notapproved category id in an array
                if ($document->notapproved) {
                    $document->notapproved_array = json_decode($document->notapproved, true);
                } else {
                    $document->notapproved_array = [];
                }
                
                $count_approved_cat_matched = 0;
                foreach ($document->approved_array as $approved_cat_id) {
                    if (array_key_exists($approved_cat_id, $document->categoriesarray)) {
                        $count_approved_cat_matched++;
                    }
                }
               
                if ($count_approved_cat_matched == count($document->categoriesarray) && $previous_document_approved == 1) {
                    $all_documents_approved = 1;
                } else {
                    $all_documents_approved = 0;
                    $previous_document_approved = 0;
                }
            }
        } else {
            $documents = [];
        }
        // if there are pending categories left, set the status id
        $pending_categories = array_diff($categories_needed, $categories_used);
        
        if ($all_documents_uploaded) {
            $parcel->update([
                'lb_documents_complete' => 1
            ]);
        } else {
            $parcel->update([
                'lb_documents_complete' => 0
            ]);
        }
        if ($all_documents_approved) {
            $parcel->update([
                'hfa_documents_complete' => 1
            ]);
        } else {
            $parcel->update([
                'hfa_documents_complete' => 0
            ]);
        }

        // check if all cost items have a requested amount
        if (CostItem::where('parcel_id', '=', $parcel->id)->count() != RequestItem::where('parcel_id', '=', $parcel->id)->count()) {
            $requestedAmountsAreMissing = 1;
        } else {
            $requestedAmountsAreMissing = 0;
        }

        // check if parcel already part of a request
        $parceltorequest = ParcelsToReimbursementRequest::where('parcel_id', '=', $parcel->id)->first();

        if (isset($parceltorequest->id) && ($parcel->legacy != 1 && $parcel->sf_parcel_id == null)) {
            $parcelAlreadyInRequest = 1;
            if ($parcel->landbank_property_status_id == 7) {
                //                $parcel = updateStatus("parcel", $parcel, 'landbank_property_status_id', 6, 0, "");
                // $parcel->update([
             //   		'landbank_property_status_id' => 6
             //    ]); // should already be set... but just in case
            }
        } else {
            $parcelAlreadyInRequest = 0;
        }

        // check if parcel already part of a po
        $parceltopo = ParcelsToPurchaseOrder::where('parcel_id', '=', $parcel->id)->first();
        if (isset($parceltopo->id)) {
            $parcelAlreadyInPO = 1;
        } else {
            $parcelAlreadyInPO = 0;
        }

        // check if parcel already part of an invoice
        $parceltoinvoice = ParcelsToReimbursementInvoice::where('parcel_id', '=', $parcel->id)->first();
        if (isset($parceltoinvoice->id)) {
            $parcelAlreadyInInvoice = 1;
            $invoice = ReimbursementInvoice::where('id', '=', $parceltoinvoice->reimbursement_invoice_id)->first();
        } else {
            $parcelAlreadyInInvoice = 0;
            $invoice = null;
        }

        if ($parcel->landbank_property_status_id == 14 || $parcel->landbank_property_status_id == 15 || $parcel->landbank_property_status_id == 16 || $parcel->landbank_property_status_id == 17 || $parcel->landbank_property_status_id == 18 || $parcel->landbank_property_status_id == 41 || $parcel->landbank_property_status_id == 42 || $parcel->landbank_property_status_id == 49) {
            // nothing to do here, the parcel is in disposition mode.
        } elseif ($parcel->landbank_property_status_id == 10) {
            // nothing to do here, the PO has been approved by HFA, LB needs to issue the invoice.
        } elseif (!CostItem::where('parcel_id', '=', $parcel->id)->count() || $requestedAmountsAreMissing) {
            //            $parcel = updateStatus("parcel", $parcel, 'landbank_property_status_id', 46, 0, "");
            // $parcel->update([
            //     'landbank_property_status_id' => 46
            // ]);
        } elseif ($parcel->landbank_property_status_id == 6) {
            // nothing to do here, the parcel has already been submitted.
        } elseif ($parcel->landbank_property_status_id == 8) {
            // nothing to do here, the request has been submitted to HFA, PO created.
        } elseif (count($pending_categories)) {
            //            $parcel = updateStatus("parcel", $parcel, 'landbank_property_status_id', 45, 0, "");
            // $parcel->update([
               // 	'landbank_property_status_id' => 45
            // ]);
        } else {
            // if no more documents needed, ready to be submitted
            //            $parcel = updateStatus("parcel", $parcel, 'landbank_property_status_id', 7, 0, "");
            // $parcel->update([
         //   		'landbank_property_status_id' => 7
         //    ]);
        }

        // requests available to this entity
        $availableRequests = DB::table('reimbursement_requests')->select('id as req_id')->where('program_id', $parcel->program_id)->orderBy('id', 'desc')->get()->all();
        
        if (($parcel->compliance == 1 || $parcel->compliance_manual == 1) &&
            $parcel->compliance_score != "Pass" && $parcel->compliance_score != "1") {
            $compliance_started_and_not_all_approved = 1;
        } else {
            $compliance_started_and_not_all_approved = 0;
        }

        // checks are already performed when the detail page of the parcel is loaded.
        //perform_all_parcel_checks($parcel);
        //guide_next_pending_step(2, $parcel->id);

        if ($format == 'json') {
            if ($parceltoinvoice) {
                $invoice_id = $parceltoinvoice->reimbursement_invoice_id;
            } else {
                $invoice_id = null;
            }
            if ($parcel->retainages) {
                $retainages = $parcel->retainages;
            } else {
                $retainages = null;
            }
            
            return json_encode(compact('breakouts', 'compliance_started_and_not_all_approved', 'status', 'parcel_id', 'parcel', 'requestedAmountsAreMissing', 'parcelAlreadyInRequest', 'parceltorequest', 'parcelAlreadyInPO', 'parceltopo', 'parcelAlreadyInInvoice', 'parceltoinvoice', 'all_documents_approved', 'availableRequests', 'parcelRules', 'invoice_id', 'retainages', 'invoice'));
        } else {
            return view('parcels.breakouts_table', compact('breakouts', 'compliance_started_and_not_all_approved', 'status', 'parcel_id', 'parcel', 'requestedAmountsAreMissing', 'parcelAlreadyInRequest', 'parceltorequest', 'parcelAlreadyInPO', 'parceltopo', 'parcelAlreadyInInvoice', 'parceltoinvoice', 'all_documents_approved', 'availableRequests', 'parcelRules', 'invoice'));
        }
    }

    public function HFAApproveParcel(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }
        //updateStatus("parcel", $parcel, 'landbank_property_status_id', 10, 0, "");
        //updateStatus("parcel", $parcel, 'hfa_property_status_id', 26, 0, "");
        updateStatus("parcel", $parcel, 'approved_in_po', 1, 0, "");
        updateStatus("parcel", $parcel, 'declined_in_po', 0, 0, "");
        guide_set_progress($parcel->id, 55, $status = 'completed', 1); // parcel approved in PO
        
        // after approving that parcel checks if all the parcels in the po are approved
        // and run compliance if needed

        if ($parcel->associatedPo) {
            $po = ReimbursementPurchaseOrders::where('id', '=', $parcel->associatedPo->purchase_order_id)->first();
        
            if ($this->areParcelsApprovedInPO($po) && $po->legacy != 1 && !$this->hasComplianceStarted($po)) {
                $this->startCompliance($po);
            }
        }
        
        $lc = new LogConverter('Parcel', 'approved by HFA');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' approved a parcel.')->save();
        return ['message'=>"It's done!", 'error'=>0];
    }

    public function HFADeclineParcel(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        updateStatus("parcel", $parcel, 'approved_in_po', 0, 0, "");
        updateStatus("parcel", $parcel, 'declined_in_po', 1, 0, "");

        updateStatus("parcel", $parcel, 'landbank_property_status_id', 8, 0, "");
        updateStatus("parcel", $parcel, 'hfa_property_status_id', 23, 0, "");

        guide_set_progress($parcel->id, 55, $status = 'started', 1); // parcel not approved in PO
        
        // $parcel->update([
        //     'landbank_property_status_id' => 8,
        //     'hfa_property_status_id' => 23
        // ]);
        $lc = new LogConverter('Parcel', 'declined by HFA');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' declined a parcel.')->save();
        return ['message'=>"It's done!", 'error'=>0];
    }

    public function breakoutViewCostItem()
    {
        return "yeah";
    }

    public function advanceDesignation(Parcel $parcel, Request $request)
    {
        // get program rules
        $program_rules = ProgramRule::where('id', '=', $parcel->program_rules_id)->first();
        dd($program_rules);
        // check that category selected has an advanced rule

        // if so,
    }

    /*
    //
    //  BREAKOUTS
    //
    */

    public function editCostAmount(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isLandbankAdmin() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $output['new_amount'] = '';
        $request_amount = (float) $request->get('amount');
        $cost_item_id = (int) $request->get('cost_id');

        $cost_item = CostItem::where('id', '=', $cost_item_id)->first();

        if ($request_amount >= 0) {
            $cost_item->update([
                    "amount" => $request_amount
                ]);
            $output['message'] = "I've updated the cost amount.";
            $output['new_amount'] = $request_amount;
        } else {
            $output['message'] = "Oops, I couldn't save this cost amount. Something went wrong.";
        }

        return $output;
    }

    public function deleteCostItem(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isLandbankAdmin() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?"];
        }

        $cost_item_id = (int) $request->get('cost_id');

        // get cost item
        $cost_item = CostItem::where('id', '=', $cost_item_id)->first();

        if (count($cost_item)) {
            // if cost item has a retainage, it cannot be deleted
            if ($cost_item->retainage) {
                $output['message'] = "This item cannot be deleted because it has a retainage!";
                return $output;
            }

            // get request item
            $request_item = RequestItem::where('ref_id', '=', $cost_item->id)->first();

            if (count($request_item)) {
                // get po item
                $po_item = PoItems::where('ref_id', '=', $request_item->id)->first();

                if (count($po_item)) {
                    // get invoice item
                    $invoice_item = InvoiceItem::where('ref_id', '=', $po_item->id)->first();

                    if (count($invoice_item)) {
                        $invoice_item->delete();
                    }
                    $po_item->delete();
                }
                $request_item->delete();
            }
            $cost_item->delete();
        }

        $lc = new LogConverter('Parcel', 'cost item deleted');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' deleted a cost item '.$cost_item_id)->save();
        
        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        $output['message'] = "This item has been deleted!";

        return $output;
    }

    public function addRequestedAmount(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isLandbankAdmin() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $output['new_amount'] = '';
        $request_amount = (float) $request->get('amount');
        $cost_item_id = (int) $request->get('cost_id');

        if ($request_amount >= 0) {
            $output['new_amount'] = $request_amount;
            $request_item = RequestItem::where('ref_id', '=', $cost_item_id)->first();
            if (count($request_item) == 1) {
                $request_item->update([
                    "amount" => $request_amount
                ]);
                $lc = new LogConverter('Parcel', 'request item updated');
                $expenseCategory = ExpenseCategory::find($request_item->expense_category_id);
                $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' updated a request item '.$request_item->id.' to $'.$request_amount.' for expense category '.$expenseCategory->expense_category_name.' (id '.$request_item->expense_category_id.') for request #'.$request_item->request_id)->save();
            } elseif (count($request_item) == 0) {
                // create the request_item using data from parcel and cost_item
                $cost_item = CostItem::where('id', '=', $cost_item_id)->first();

                if ($cost_item) {
                    // check if there is already a request first
                    $req_id = null;
                    $existing_request = ParcelsToReimbursementRequest::where('parcel_id', '=', $parcel->id)->first();
                    if (count($existing_request) == 1) {
                        $req_id = $existing_request->reimbursement_request_id;
                    }

                    // new request item
                    $new_request_item = new RequestItem([
                            'req_id' => $req_id,
                            'breakout_type' => $cost_item->breakout_type,
                            'parcel_id' => $cost_item->parcel_id,
                            'account_id' => $cost_item->account_id,
                            'program_id' => $cost_item->program_id,
                            'entity_id' => $cost_item->entity_id,
                            'expense_category_id' => $cost_item->expense_category_id,
                            'amount' => $request_amount,
                            'vendor_id' => $cost_item->vendor_id,
                            'description' => $cost_item->description,
                            'notes' => $cost_item->notes,
                            'ref_id' => $cost_item->id,
                            'advance' => $cost_item->advance
                    ]);
                    $new_request_item->save();
                    $expenseCategory = ExpenseCategory::find($cost_item->expense_category_id);
                    $lc = new LogConverter('Parcel', 'request item added');
                    $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' added a request item '.$new_request_item->id.' for expense category '.$expenseCategory->expense_category_name.'(id '.$cost_item->expense_category_id.') for $'.$request_amount)->save();
                } else {
                    $output['message'] = "Oops, I couldn't find a corresponding cost item. Something went wrong.";
                }
            }

            $output['message'] = "I've added the requested amount to this item!";
        } else {
            $output['message'] = "Oops, I couldn't save this request amount. Something went wrong.";
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return $output;
    }

    public function addApprovedAmount(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $output['new_amount'] = '';
        $po_amount = (float) $request->get('amount');
        $request_item_id = (int) $request->get('request_id');

        if ($po_amount >= 0) {
            $output['new_amount'] = $po_amount;
            $po_item = PoItems::where('ref_id', '=', $request_item_id)->first();
            if ($po_item) {
                $po_item->update([
                    "amount" => $po_amount
                ]);
                $lc = new LogConverter('Parcel', 'approved item updated');
                $expenseCategory = ExpenseCategory::find($po_item->expense_category_id);
                $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' updated an po item '.$po_item->id.' to $'.$po_amount.' for expense category '.$expenseCategory->expense_category_name.' (id '.$po_item->expense_category_id.') for PO #'.$po_item->po_id)->save();
            } else {
                // create the request_item using data from parcel and cost_item
                $request_item = RequestItem::where('id', '=', $request_item_id)->first();

                if ($request_item) {
                    // check if there is already a PO first
                    $po_id = null;
                    $existing_po = ParcelsToPurchaseOrder::where('parcel_id', '=', $parcel->id)->first();
                    if (count($existing_po) == 1) {
                        $po_id = $existing_po->purchase_order_id;
                    }

                    // new request item
                    $new_po_item = new PoItems([
                            'po_id' => $po_id,
                            'breakout_type'=>$request_item->breakout_type,
                            'parcel_id' => $request_item->parcel_id,
                            'account_id' => $request_item->account_id,
                            'program_id' => $request_item->program_id,
                            'entity_id' => $request_item->entity_id,
                            'expense_category_id' => $request_item->expense_category_id,
                            'amount' => $po_amount,
                            'vendor_id' => $request_item->vendor_id,
                            'description' => $request_item->description,
                            'notes' => $request_item->notes,
                            'ref_id' => $request_item->id,
                            'advance' => $request_item->advance
                    ]);
                    $new_po_item->save();
                    $expenseCategory = ExpenseCategory::find($request_item->expense_category_id);
                    $lc = new LogConverter('Parcel', 'po item created');
                    $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' created a po item for expense category '.$expenseCategory->expense_category_name.'(id'.$request_item->expense_category_id.') for the amount of $'.$po_amount.' for PO#'.$new_po_item->id)->save();
                } else {
                    $output['message'] = "Oops, I couldn't find a corresponding request item. Something went wrong.";
                }
            }

            $output['message'] = "I've added the approved amount to this item!";
        } else {
            $output['message'] = "Oops, I couldn't save this approved amount. Something went wrong.";
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return $output;
    }

    public function addInvoicedAmount(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isHFAPrimaryInvoiceApprover() && !Auth::user()->isHFASecondaryInvoiceApprover() && !Auth::user()->isHFATertiaryInvoiceApprover() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }
        $output['new_amount'] = '';
        $invoice_amount = (float) $request->get('amount');
        $po_item_id = (int) $request->get('po_id');

        if ($invoice_amount >= 0) {
            $output['new_amount'] = $invoice_amount;
            $invoice_item = InvoiceItem::where('ref_id', '=', $po_item_id)->first();
            if ($invoice_item) {
                $invoice_item->update([
                    "amount" => $invoice_amount
                ]);
                $lc = new LogConverter('Parcel', 'invoice item updated');
                $expenseCategory = ExpenseCategory::find($invoice_item->expense_category_id);
                $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' updated an invoice item '.$invoice_item->id.' to $'.$invoice_amount.' for expense category '.$expenseCategory->expense_category_name.' (id '.$invoice_item->expense_category_id.') for Invoice #'.$invoice_item->invoice_id)->save();
            } else {
                // create the po_item using data from parcel and request_item
                $po_item = PoItems::where('id', '=', $po_item_id)->first();

                if ($po_item) {
                    // check if there is already a request first
                    $inv_id = null;
                    $existing_invoice = ParcelsToReimbursementInvoice::where('parcel_id', '=', $parcel->id)->first();
                    if (count($existing_invoice) == 1) {
                        $inv_id = $existing_invoice->reimbursement_invoice_id;
                    }

                    // new request item
                    $new_invoice_item = new InvoiceItem([
                            'invoice_id' => $inv_id,
                            'breakout_type' => $po_item->breakout_type,
                            'parcel_id' => $po_item->parcel_id,
                            'account_id' => $po_item->account_id,
                            'program_id' => $po_item->program_id,
                            'entity_id' => $po_item->entity_id,
                            'expense_category_id' => $po_item->expense_category_id,
                            'amount' => $invoice_amount,
                            'vendor_id' => $po_item->vendor_id,
                            'description' => $po_item->description,
                            'notes' => $po_item->notes,
                            'ref_id' => $po_item->id,
                            'advance' => $po_item->advance
                    ]);
                    $new_invoice_item->save();
                    $expenseCategory = ExpenseCategory::find($po_item->expense_category_id);
                    
                    $lc = new LogConverter('Parcel', 'invoice item created');
                    $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' added an invoice item '.$new_invoice_item->id.' for expense category '.$expenseCategory->expense_category_name.' (id '.$po_item->expense_category_id.') in the amount of $'.$invoice_amount.' for Invoice #'.$inv_id)->save();
                } else {
                    $output['message'] = "Oops, I couldn't find a corresponding request item. Something went wrong.";
                }
            }

            $output['message'] = "I've added the approved amount to this item!";
        } else {
            $output['message'] = "Oops, I couldn't save this approved amount. Something went wrong.";
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return $output;
    }

    public function landbankRemoveParcelFromRequest(Parcel $parcel)
    {
        // check if user is allowed to remove parcel from request
        if (!Auth::user()->isLandbankParcelApprover() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?"];
        }

        // check if parcel belongs to user's entity
        if (!Auth::user()->isHFAAdmin() && Parcel::where('id', '=', $parcel->id)->where('entity_id', '=', Auth::user()->entity_id)->count() == 0) {
            return ['message'=>"Oops, looks like this parcel doesn't belong to you..."];
        }

        // remove from Invoice if it applies
        $parceltoinvoice = ParcelsToReimbursementInvoice::where('parcel_id', '=', $parcel->id)->first();
        if (count($parceltoinvoice) != 0) {
            $parceltoinvoice->delete();
        }

        // delete all invoice items
        $invoice_items = InvoiceItem::where('parcel_id', '=', $parcel->id)->delete();

        // also remove from PO if it applies
        $parceltopo = ParcelsToPurchaseOrder::where('parcel_id', '=', $parcel->id)->first();
        if (count($parceltopo) != 0) {
            $parceltopo->delete();
        }

        // delete all po items
        PoItems::where('parcel_id', '=', $parcel->id)->delete();
        
        // check if parcel already removed from the request, a parcel should only be in one request ever
        $parceltorequest = ParcelsToReimbursementRequest::where('parcel_id', '=', $parcel->id)->first();
        if (count($parceltorequest) != 0) {
            $parceltorequest->delete();
        }

        // remove request_id
        RequestItem::where('parcel_id', '=', $parcel->id)->update(['req_id' => null]);

        // update the parcel status
        updateStatus("parcel", $parcel, 'landbank_property_status_id', 48, 0, "");
        updateStatus("parcel", $parcel, 'hfa_property_status_id', 39, 0, "");
        
        //   	$parcel->update([
        // 	"landbank_property_status_id" => 48,
        //           "hfa_property_status_id" => 39
        // ]);

        $lc = new LogConverter('Parcel', 'removed from request');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . 'Removed a parcel from the current request.')->save();

        $output['message'] = "This parcel was removed from the current request.";

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return $output;
    }

    public function landbankSubmitParcelToRequest(Parcel $parcel)
    {
        // check if user is allowed to add parcel to request
        if (!Auth::user()->isLandbankParcelApprover() && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", "error"=>1];
        }

        // check if parcel belongs to user's entity
        if (Parcel::where('id', '=', $parcel->id)->where('entity_id', '=', Auth::user()->entity_id)->count() == 0 && !Auth::user()->isHFAAdmin()) {
            return ['message'=>"Oops, looks like this parcel doesn't belong to you...", "error"=>1];
        }

        // check if parcel not already in a request
        if (ParcelsToReimbursementRequest::where('parcel_id', '=', $parcel->id)->count() > 0) {
            return ['message'=>"Oops, looks like that parcel is already in a request...", "error"=>1];
        }

        // get current draft request
        $current_request = ReimbursementRequest::where('entity_id', '=', $parcel->entity_id)
                                ->where('account_id', '=', $parcel->account_id)
                                ->where('program_id', '=', $parcel->program_id)
                                ->where('active', '=', 1)
                                ->where('status_id', '=', 1)
                                ->first();

        // if no request exist, create one
        if (!$current_request) {
            $current_request = new ReimbursementRequest([
                            'entity_id' => $parcel->entity_id,
                            'program_id' => $parcel->program_id,
                            'account_id' => $parcel->account_id,
                            'status_id' => 1,
                            'active' => 1
            ]);
            $current_request->save();

            $lc = new LogConverter('reimbursement_requests', 'create');
            $lc->setFrom(Auth::user())->setTo($current_request)->setDesc(Auth::user()->email . 'Created a new reimbursement request draft')->save();
        }

        // add parcel to request
        $parcel_to_request = new ParcelsToReimbursementRequest([
                            'parcel_id' => $parcel->id,
                            'reimbursement_request_id' => $current_request->id
        ]);
        $parcel_to_request->save();

        // update all the request_items with the current request_id
        $request_items = RequestItem::where('parcel_id', '=', $parcel->id)
                            ->where('req_id', '=', null)
                            ->where('entity_id', '=', $parcel->entity_id)
                            ->where('account_id', '=', $parcel->account_id)
                            ->where('program_id', '=', $parcel->program_id)
                            ->update([
                                "req_id" => $current_request->id
                            ]);

        // set parcel "landbank_property_status_id" to 6
        updateStatus("parcel", $parcel, 'landbank_property_status_id', 6, 0, "");
        //   	$parcel->update([
        // 	"landbank_property_status_id" => 6
        // ]);

        $lc = new LogConverter('Parcel', 'add to request');
        $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . 'Added a parcel to the current request.')->save();

        $output['message'] = "This parcel was submitted to the current request.";

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return $output;
    }

    /*
    //
    //  REQUEST
    //
    */

    public function getRequest(ReimbursementRequest $request)
    {
        // check if allowed
        if (!Gate::allows('view-request') && !Auth::user()->isLandbankRequestApprover() || (Auth::user()->entity_id != $request->entity_id && Auth::user()->entity_id !=1)) {
            return 'Sorry you do not have access to the request.';
        }

        setlocale(LC_MONETARY, 'en_US');

        $request->load('status')
                ->load('parcels')
                ->load('entity')
                ->load('account')
                ->load('notes')
                ->load('account.transactions')
                ->load('program');

        $stat = [];
        $stat = $stat + $request->account->statsParcels->toArray()[0]
                        + $request->account->statsTransactions->toArray()[0]
                        + $request->account->statsCostItems->toArray()[0]
                        + $request->account->statsRequestItems->toArray()[0]
                        + $request->account->statsPoItems->toArray()[0]
                        + $request->account->statsInvoiceItems->toArray()[0];

        $total = 0;
        $request->legacy = 0;
        foreach ($request->parcels as $parcel) {
            $parcel->costTotal = $parcel->costTotal();
            $parcel->requestedTotal = $parcel->requestedTotal();
            $parcel->requested_total_formatted = money_format('%n', $parcel->requestedTotal);
            $parcel->approved_total = $parcel->approvedTotal();
            $parcel->invoiced_total = $parcel->invoicedTotal();
            $total = $total + $parcel->requestedTotal;
            if ($parcel->legacy == 1 || $parcel->sf_parcel_id != null) {
                $request->legacy = 1;
            }
        }
        $total = money_format('%n', $total);

        
        $owners_array = [];
        foreach ($request->notes as $note) {
            // create initials
            $words = explode(" ", $note->owner->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $note->initials = $initials;

            // create associative arrays for initials and names
            if (!array_key_exists($note->owner->id, $owners_array)) {
                $owners_array[$note->owner->id]['initials'] = $initials;
                $owners_array[$note->owner->id]['name'] = $note->owner->name;
                $owners_array[$note->owner->id]['color'] = $note->owner->badge_color;
                $owners_array[$note->owner->id]['id'] = $note->owner->id;
            }
        }
                        
        $lc = new LogConverter('reimbursement_requests', 'view');
        $lc->setFrom(Auth::user())->setTo($request)->setDesc(Auth::user()->email . 'Viewed reimbursement request')->save();

        // get NIP entity
        $nip = Entity::where('id', 1)->with('state')->with('user')->first();

        // get approvers (type id 2 is for reimbursement requests)
        $landbankRequestApprovers = User::where('entity_id', '=', $request->entity_id)
                                            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                            ->where('users_roles.role_id', '=', 14)
                                            ->where('active', '=', 1)
                                            ->select('users.id', 'users.name')
                                            ->get();

        // check if there are any approval_requests, if not add all potential approvers
        // to approval requests
        $added_approvers = ApprovalRequest::where('approval_type_id', '=', 2)
                                        ->where('link_type_id', '=', $request->id)
                                        ->pluck('user_id as id');
        $pending_approvers = [];

        if (count($added_approvers) == 0 && count($landbankRequestApprovers) > 0) {
            foreach ($landbankRequestApprovers as $landbankRequestApprover) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 2,
                    "link_type_id" => $request->id,
                    "user_id" => $landbankRequestApprover->id
                ]);
                $newApprovalRequest->save();
            }
        } elseif (count($landbankRequestApprovers) > 0) {
            // list all approvers who are not already in the approval_request table

            $pending_approvers = User::where('entity_id', '=', $request->entity_id)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 14)
                                    ->where('active', '=', 1)
                                    ->whereNotIn('id', $added_approvers)
                                    ->select('users.id', 'users.name')
                                    ->get();
        }

        $approvals = ApprovalRequest::where('approval_type_id', '=', 2)
                        ->where('link_type_id', '=', $request->id)
                        ->with('actions')
                        ->with('actions.action_type')
                        ->with('approver')
                        ->get();

        $hasApprovals = 0;
        $isApproved = 0;
        $user_approved_already = 0;
        $tmp_previous_approved = 1;
        $isApprover = 0;
        // check if there is a approval action 1 for each approver for this request
        if (count($approvals)) {
            foreach ($approvals as $approval) {
                if (Auth::user()->id == $approval->user_id) {
                    $isApprover = 1;
                }

                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved = 1;
                        $hasApprovals = 1;
                        if ($approval->user_id == Auth::user()->id) {
                            $user_approved_already = 1;
                        }
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                        $hasApprovals = 1;
                        if ($approval->user_id == Auth::user()->id) {
                            $user_approved_already = 1;
                        }
                    } else {
                        $isApproved = 0;
                    }
                    $tmp_previous_approved = $action->approval_action_type_id;
                } else {
                    // there is an approval request, but no action
                    // we are missing decisions
                    $isApproved =0;
                    $tmp_previous_approved = 0;
                }
            }
        }

        $associatedPo = ReimbursementPurchaseOrders::where('rq_id', '=', $request->id)
                                                ->where('active', '=', 1)
                                                ->first();
        if (count($associatedPo)) {
            $submitted_on = $associatedPo->created_at;
            if ($submitted_on === null) {
                if ($associatedPo->updated_at === null) {
                    $submitted_on_formatted = null;
                } else {
                    $submitted_on_formatted = date('m/d/Y', strtotime($associatedPo->updated_at));
                }
            } else {
                $submitted_on_formatted = date('m/d/Y', strtotime($submitted_on));
            }
        } else {
            $submitted_on_formatted = null;
        }

        return view('pages.request', compact('request', 'nip', 'submitted_on_formatted', 'total', 'stat', 'approvals', 'hasApprovals', 'isApproved', 'user_approved_already', 'landbankRequestApprovers', 'isApprover', 'pending_approvers'));
    }

    public function requestSubmit(ReimbursementRequest $current_request, Request $request)
    {
        if ((!Auth::user()->isLandbankRequestApprover() || Auth::user()->entity_id != $current_request->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            $output['error'] = 1;
            return $output;
        }

        // check that all approvals are in
        $approvals = ApprovalRequest::where('approval_type_id', '=', 2)
                        ->where('link_type_id', '=', $current_request->id)
                        ->with('actions')
                        ->with('actions.action_type')
                        ->with('approver')
                        ->get();

        $isApproved = 0;
        $tmp_previous_approved = 1;
        if (count($approvals)) {
            foreach ($approvals as $approval) {
                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved = 1;
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                    } else {
                        $isApproved = 0;
                    }
                    $tmp_previous_approved = $action->approval_action_type_id;
                } else {
                    // there is an approval request, but no action
                    // we are missing decisions
                    $isApproved =0;
                    $tmp_previous_approved = 0;
                }
            }
        }

        if ($isApproved == 0) {
            $output['message'] = "Some approvals are missing. I couldn't submit this request.";
            $output['error'] = 1;
            return $output;
        }

        updateStatus("request", $current_request, 'status_id', 7, 0, "");
        // $current_request->update([
        //     "status_id" => 7 //approved
        // ]);

        // Create PO if there isn't already one!
        $po = ReimbursementPurchaseOrders::where('rq_id', '=', $current_request->id)->first();

        if (!$po) {
            $po = new ReimbursementPurchaseOrders([
                    'entity_id' => $current_request->entity_id,
                    'program_id' => $current_request->program_id,
                    'account_id' => $current_request->account_id,
                    'rq_id' => $current_request->id,
                    'status_id' => 1,
                    'active' => 1
            ]);
            $po->save();

            // Attach parcels to PO & change the status of each parcel in the request
            foreach ($current_request->parcels as $parcel) {
                $parcel_to_po = new ParcelsToPurchaseOrder([
                        'parcel_id' => $parcel->id,
                        'purchase_order_id' => $po->id
                ]);
                $parcel_to_po->save();

                updateStatus("parcel", $parcel, 'landbank_property_status_id', 8, 0, "");
                updateStatus("parcel", $parcel, 'hfa_property_status_id', 22, 0, "");
                // $parcel->update([
                //         "landbank_property_status_id" => 8,
                //         "hfa_property_status_id" => 22
                // ]);

                // also make sure the req_id is set in every request_item
                $request_items = RequestItem::where('parcel_id', '=', $parcel->id);
                $request_items->update([
                        "req_id" => $current_request->id
                ]);

                // also make sure the po_id is set in every po_item (if they exist)
                $existing_po_items = PoItems::where('parcel_id', '=', $parcel->id);
                $existing_po_items->update([
                        "po_id" => $po->id
                ]);
            }

            // Create po_items from request_items
            $current_request->load('requestItems');

            foreach ($current_request->requestItems as $request_item) {
                // first check that there isn't already a po item for that request item
                if (!PoItems::where('parcel_id', '=', $parcel->id)->where('ref_id', '=', $request_item->id)->count()) {
                    // if not then create one with request values
                    $new_po_item = new PoItems([
                            'po_id' => $po->id,
                            'parcel_id' => $request_item->parcel_id,
                            'account_id' => $request_item->account_id,
                            'program_id' => $request_item->program_id,
                            'entity_id' => $request_item->entity_id,
                            'expense_category_id' => $request_item->expense_category_id,
                            'amount' => $request_item->amount,
                            'vendor_id' => $request_item->vendor_id,
                            'description' => $request_item->description,
                            'notes' => $request_item->notes,
                            'ref_id' => $request_item->id,
                            'advance' => $request_item->advance
                    ]);
                    $new_po_item->save();
                }
            }

            // send message to HFA
            // TBD

            // Update steps
            guide_set_progress($parcel->id, 31, $status = 'completed', 1);

            // output message
            $output['message'] = 'This request has been approved!';
        } else {
            // output message
            $output['message'] = 'This request has been approved! (Warning: a PO already existed)';
        }

        return $output;
    }

    public function requestRemoveApprover(ReimbursementRequest $current_request, Request $request)
    {
        if ((!Auth::user()->isLandbankRequestApprover() || Auth::user()->entity_id != $current_request->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($current_request) {
            $approver_id = $request->get('id');
            $approver = ApprovalRequest::where('approval_type_id', '=', 2)
                            ->where('link_type_id', '=', $current_request->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            if (count($approver)) {
                $approver->delete();
            }
 
            $lc = new LogConverter('reimbursement_requests', 'remove.approver');
            $lc->setFrom(Auth::user())->setTo($current_request)->setDesc(Auth::user()->email . 'removed approver '.$approver_id)->save();


            $data['message'] = '';
            $data['id'] = $request->get('id');
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function requestAddHFAApprover(ReimbursementRequest $current_request, Request $request)
    {
        if (!Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($current_request) {
            if (!ApprovalRequest::where('approval_type_id', '=', 2)
                        ->where('link_type_id', '=', $current_request->id)
                        ->where('user_id', '=', Auth::user()->id)
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 2,
                    "link_type_id" => $current_request->id,
                    "user_id" => Auth::user()->id
                ]);
                $newApprovalRequest->save();
                $lc = new LogConverter('reimbursement_requests', 'add.hfa.approver');
                $lc->setFrom(Auth::user())->setTo($current_request)->setDesc(Auth::user()->email . 'added a HFA approver.')->save();

                $data['message'] = 'You now are an approver.';
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

    public function requestAddLBApprover(ReimbursementRequest $current_request, Request $request)
    {
        if ((!Auth::user()->isLandbankRequestApprover() || Auth::user()->entity_id != $current_request->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($current_request && $request->get('user_id') > 0) {
            if (!ApprovalRequest::where('approval_type_id', '=', 2)
                        ->where('link_type_id', '=', $current_request->id)
                        ->where('user_id', '=', $request->get('user_id'))
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 2,
                    "link_type_id" => $current_request->id,
                    "user_id" => $request->get('user_id')
                ]);
                $newApprovalRequest->save();
                $lc = new LogConverter('reimbursement_requests', 'add.lb.approver');
                $lc->setFrom(Auth::user())->setTo($current_request)->setDesc(Auth::user()->email . 'added LB approver '.$request->get('user_id'))->save();

                $data['message'] = 'Approver added.';
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

    public function approveRequestUploadSignature(ReimbursementRequest $req, Request $request)
    {
        if (app('env') == 'local') {
            app('debugbar')->disable();
        }
        
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';
            $categories_json = json_encode(['30'], true); // 30 is "Landbank Request Signature"

            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            $req->load('parcels');

            // get parcels from req $req->parcels
            foreach ($req->parcels as $parcel) {
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
                    $document->approve_categories([30]);

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

            $approval_process = $this->approveRequest($req, $approvers, $document_ids);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function approveRequestUploadSignatureComments(ReimbursementRequest $req, Request $request)
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

    public function approveRequest(ReimbursementRequest $request, $approvers = null, $document_ids = null)
    {
        if ((!Auth::user()->isLandbankRequestApprover() || Auth::user()->entity_id != $request->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($request) {
            // it is possible that a HFA admin uploads a signature file for multiple LB users
            // if current user is HFA admin, make sure that person is added as the approver
            // in the records
            if (Auth::user()->isHFAAdmin()) {
                // create an approval request for HFA user
                if (!ApprovalRequest::where('approval_type_id', '=', 2)
                            ->where('link_type_id', '=', $request->id)
                            ->where('user_id', '=', Auth::user()->id)
                            ->count()) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 2,
                        "link_type_id" => $request->id,
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
                    $approver = ApprovalRequest::where('approval_type_id', '=', 2)
                                ->where('link_type_id', '=', $request->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                    if (count($approver)) {
                        $action = new ApprovalAction([
                                'approval_request_id' => $approver->id,
                                'approval_action_type_id' => 5, //by proxy
                                'documents' => $documents_json
                            ]);
                        $action->save();
             
                        $lc = new LogConverter('reimbursement_requests', 'approval by proxy');
                        $lc->setFrom(Auth::user())->setTo($request)->setDesc(Auth::user()->email . 'approved the request for '.$approver->name)->save();
                    }
                }
                $data['message'] = 'This request was approved.';
                $data['id'] = $approver_id;
                return $data;
            } else {
                $approver_id = Auth::user()->id;
                $approver = ApprovalRequest::where('approval_type_id', '=', 2)
                                ->where('link_type_id', '=', $request->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                if (count($approver)) {
                    $action = new ApprovalAction([
                            'approval_request_id' => $approver->id,
                            'approval_action_type_id' => 1
                        ]);
                    $action->save();
         
                    $lc = new LogConverter('reimbursement_requests', 'approval');
                    $lc->setFrom(Auth::user())->setTo($request)->setDesc(Auth::user()->email . 'approved the request.')->save();

                    $data['message'] = 'Your request was approved.';
                    $data['id'] = $approver_id;
                    return $data;
                } else {
                    $data['message'] = 'Something went wrong.';
                    $data['id'] = null;
                    return $data;
                }
            }
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function declineRequest(ReimbursementRequest $request)
    {
        // check user belongs to request entity
        if ((!Auth::user()->isLandbankRequestApprover() || Auth::user()->entity_id != $request->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($request) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 2)
                            ->where('link_type_id', '=', $request->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();
 
            $lc = new LogConverter('reimbursement_requests', 'decline');
            $lc->setFrom(Auth::user())->setTo($request)->setDesc(Auth::user()->email . 'declined the request.')->save();


            $data['message'] = 'This request has been declined.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function newNoteEntry(ReimbursementRequest $current_request, Request $request)
    {
        if ($current_request && $request->get('request-note')) {
            $user = Auth::user();

            $note = new RequestNote([
                'owner_id' => $user->id,
                'reimbursement_request_id' => $current_request->id,
                'note' => $request->get('request-note')
            ]);
            $note->save();
            $lc = new LogConverter('reimbursement_requests', 'addnote');
            $lc->setFrom(Auth::user())->setTo($current_request)->setDesc(Auth::user()->email . ' added note to reimbursement request')->save();

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

    /*
    //
    //  PURCHASE ORDER
    //
    */

    public function getPO(ReimbursementPurchaseOrders $po)
    {
        // check if allowed
        if ((!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $po->entity_id) && !Auth::user()->isHFAAdmin() && !Auth::user()->isHFAPOApprover()) {
            return 'Sorry you do not have access to this purchase order.';
        }

        setlocale(LC_MONETARY, 'en_US');

        $po->load('status')
                ->load('parcels')
                ->load('entity')
                ->load('account')
                ->load('notes')
                ->load('account.transactions')
                ->load('program');

        $stat = [];
        $stat = $stat + $po->account->statsParcels->toArray()[0]
                        + $po->account->statsTransactions->toArray()[0]
                        + $po->account->statsCostItems->toArray()[0]
                        + $po->account->statsRequestItems->toArray()[0]
                        + $po->account->statsPoItems->toArray()[0]
                        + $po->account->statsInvoiceItems->toArray()[0];

        $total = 0;
        $po->legacy = 0;

       
        $compliance_started_and_not_all_approved = 0;
        // check if parcels are out of compliance
        foreach ($po->parcels as $parcel) {
            $parcel_approved_total = $parcel->approvedTotal();
            $parcel_invoiced_total = $parcel->invoicedTotal();
            $total = $total + $parcel_approved_total;
            if ($parcel->legacy == 1 || $parcel->sf_parcel_id != null) {
                $po->legacy = 1;
            }

            if (($parcel->compliance == 1 || $parcel->compliance_manual == 1) &&
                $parcel->compliance_score != "Pass" && $parcel->compliance_score != "1") {
                $compliance_started_and_not_all_approved = 1;
            }

            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);
        }
        $total = money_format('%n', $total);
        
        $owners_array = [];
        foreach ($po->notes as $note) {
            // create initials
            $words = explode(" ", $note->owner->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $note->initials = $initials;

            // create associative arrays for initials and names
            if (!array_key_exists($note->owner->id, $owners_array)) {
                $owners_array[$note->owner->id]['initials'] = $initials;
                $owners_array[$note->owner->id]['name'] = $note->owner->name;
                $owners_array[$note->owner->id]['color'] = $note->owner->badge_color;
                $owners_array[$note->owner->id]['id'] = $note->owner->id;
            }
        }
                        
        $lc = new LogConverter('reimbursement_purchase_orders', 'view');
        $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . 'Viewed purchase order')->save();

        // get NIP entity
        $nip = Entity::where('id', 1)->with('state')->with('user')->first();

        // get approvers (type id 2 is for reimbursement requests)
        $HFAPOApprovers = User::where('entity_id', '=', 1)
                                            ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                            ->where('users_roles.role_id', '=', 15)
                                            ->where('active', '=', 1)
                                            ->select('users.id', 'users.name')
                                            ->get();

        // check if there are any approval_requests, if not add all potential approvers
        $added_approvers = ApprovalRequest::where('approval_type_id', '=', 3)
                                        ->where('link_type_id', '=', $po->id)
                                        ->pluck('user_id as id');
        $pending_approvers = [];

        if (count($added_approvers) == 0 && count($HFAPOApprovers) > 0) {
            foreach ($HFAPOApprovers as $HFAPOApprover) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 3,
                    "link_type_id" => $po->id,
                    "user_id" => $HFAPOApprover->id
                ]);
                $newApprovalRequest->save();
            }
        } elseif (count($HFAPOApprovers) > 0) {
            // list all approvers who are not already in the approval_request table

            $pending_approvers = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 15)
                                    ->where('active', '=', 1)
                                    ->whereNotIn('id', $added_approvers)
                                    ->select('users.id', 'users.name')
                                    ->get();
        }

        $approvals = ApprovalRequest::where('approval_type_id', '=', 3)
                        ->where('link_type_id', '=', $po->id)
                        ->with('actions')
                        ->with('actions.action_type')
                        ->with('approver')
                        ->get();

        $hasApprovals = 0;
        $isApproved = 0;
        $user_approved_already = 0;
        $tmp_previous_approved = 1;
        $isApprover = 0;

        // check if there is a approval action 1 for each approver for this request
        if (count($approvals)) {
            foreach ($approvals as $approval) {
                if (Auth::user()->id == $approval->user_id) {
                    $isApprover = 1;
                }

                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved = 1;
                        $hasApprovals = 1;
                        if ($approval->user_id == Auth::user()->id) {
                            $user_approved_already = 1;
                        }
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                        $hasApprovals = 1;
                        if ($approval->user_id == Auth::user()->id) {
                            $user_approved_already = 1;
                        }
                    } else {
                        $isApproved = 0;
                    }
                    $tmp_previous_approved = $action->approval_action_type_id;
                } else {
                    // there is an approval request, but no action
                    // we are missing decisions
                    $isApproved =0;
                    $tmp_previous_approved = 0;
                }
            }
        }

        //
        //
        // COMPLIANCE

        // if all parcels approved and none under compliance yet
        $compliance_started = $this->hasComplianceStarted($po);
        if ($this->areParcelsApprovedInPO($po) && $po->legacy != 1 && !$compliance_started) {
            $this->startCompliance($po);
        }

        $associated_invoice = ReimbursementInvoice::where('po_id', '=', $po->id)
                                                ->where('active', '=', 1)
                                                ->first();

        if (count($associated_invoice) && $po->legacy != 1) {
            $approved_on = $associated_invoice->created_at;
            if ($approved_on === null) {
                $approved_on_formatted = null;
            } else {
                $approved_on_formatted = date('m/d/Y', strtotime($approved_on));
            }
        } else {
            $approved_on_formatted = null;
        }

        return view('pages.po', compact('po', 'nip', 'submitted_on_formatted', 'total', 'stat', 'approvals', 'hasApprovals', 'isApproved', 'user_approved_already', 'landbankRequestApprovers', 'isApprover', 'pending_approvers', 'all_parcels_approved', 'compliance_started_and_not_all_approved', 'approved_on_formatted', 'compliance_started'));
    }

    public function areParcelsApprovedInPO(ReimbursementPurchaseOrders $po)
    {
        if (count($po->parcels) == 0) {
            return 0;
        }

        // check if all parcels have been approved
        $all_parcels_approved = 1;
        foreach ($po->parcels as $parcel) {
            if ($parcel->approved_in_po != 1) {
                $all_parcels_approved = 0;
            }
        }
        return $all_parcels_approved;
    }

    public function hasComplianceStarted(ReimbursementPurchaseOrders $po)
    {
        if (count($po->parcels) == 0) {
            return 0;
        }

        $compliance_started = 0;
        foreach ($po->parcels as $parcel) {
            if (($parcel->compliance == 1 || $parcel->compliance_manual == 1) &&
                $parcel->compliance_score != "Pass" && $parcel->compliance_score != "1") {
                $compliance_started_and_not_all_approved = 1;
            }

            if ($parcel->compliance == 1 || $parcel->compliance_manual == 1) {
                $compliance_started = 1;
            }
        }
        return $compliance_started;
    }

    public function startCompliance(ReimbursementPurchaseOrders $po)
    {
        // if all parcels have been approved, change theirs statuses
        // randomly choose 5% of the parcels for compliance review
        $count_parcels = count($po->parcels);

        $count_parcels_with_compliance = count($po->parcels()->where('compliance', 1));


        if ($count_parcels == 0) {
            return 0;
        }
        
        // ensure we don't add to an existing set of compliance reiviews - and if we are, only add the number needed.
        $number_of_parcels_to_review = ceil($count_parcels * 0.05);
        // create array of random picks

        //if($number_of_parcels_to_review > 0){
        $randomizer = [];
        for ($i=0; $i<$count_parcels; $i++) {
            $randomizer[] = $i;
        }
        // array_rand returns an int if only one value, otherwise an array, but we always need an array
            
        $rand_keys = array_rand($randomizer, $number_of_parcels_to_review);
        if (!is_array($rand_keys)) {
            $rand_keys = [$rand_keys];
        }
        //}
        // change the status of each parcel in the po
        $i = 0;
        foreach ($po->parcels as $parcel) {
            updateStatus("parcel", $parcel, 'landbank_property_status_id', 10, 0, "");
            updateStatus("parcel", $parcel, 'hfa_property_status_id', 26, 0, "");

            if (in_array($i, $rand_keys)) {
                updateStatus("parcel", $parcel, 'hfa_property_status_id', 21, 0, "");
                $parcel->update([
                        "compliance" => 1
                ]);
            }
            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);
            $i++;
        }
        // we have to use another instance of the po because the $po may contain "legacy" attribute that can't be updated.
        $po_to_update = ReimbursementPurchaseOrders::where('id', '=', $po->id)->first();
        $po_to_update->update([
                "status_id" => 3 // pending HFA approval
        ]);

        return 1;
    }

    public function approvePOUploadSignature(ReimbursementPurchaseOrders $po, Request $request)
    {
        if (app('env') == 'local') {
            app('debugbar')->disable();
        }
        
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';
            $categories_json = json_encode(['32'], true); // 32 is "HFA PO signature"

            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            $po->load('parcels');

            // get parcels from req $req->parcels
            foreach ($po->parcels as $parcel) {
                foreach ($files as $file) {
                    // Create filepath
                    $folderpath = 'documents/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';
                    
                    // sanitize filename
                    $characters = [' ','¥','`',"'",'~','"','\'','\\','/'];
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
                    $document->approve_categories([32]);

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

            $approval_process = $this->approvePO($po, $approvers, $document_ids);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function approvePOUploadSignatureComments(ReimbursementPurchaseOrders $po, Request $request)
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

    public function approvePO(ReimbursementPurchaseOrders $po, $approvers = null, $document_ids = null)
    {
        if ((!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $po->entity_id) && !Auth::user()->isHFAAdmin() && !Auth::user()->isHFAPOApprover()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($po) {
            // check if multiple people need to record approvals
            if (count($approvers) > 0) {
                if ($document_ids !== null) {
                    $documents = explode(",", $document_ids);
                } else {
                    $documents = [];
                }
                $documents_json = json_encode($documents, true);

                foreach ($approvers as $approver_id) {
                    $approver = ApprovalRequest::where('approval_type_id', '=', 3)
                                ->where('link_type_id', '=', $po->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                    if (count($approver)) {
                        $action = new ApprovalAction([
                                'approval_request_id' => $approver->id,
                                'approval_action_type_id' => 5, //by proxy
                                'documents' => $documents_json
                            ]);
                        $action->save();
             
                        $lc = new LogConverter('reimbursement_purchase_orders', 'approval by proxy');
                        $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . 'approved the po for '.$approver->name)->save();
                    }
                }
                $output['message'] = 'This PO was approved.';
                $output['id'] = $approver_id;
                return $output;
            } else {
                $approver_id = Auth::user()->id;
                $approver = ApprovalRequest::where('approval_type_id', '=', 3)
                                ->where('link_type_id', '=', $po->id)
                                ->where('user_id', '=', $approver_id)
                                ->first();
                if (count($approver)) {
                    $action = new ApprovalAction([
                            'approval_request_id' => $approver->id,
                            'approval_action_type_id' => 1
                        ]);
                    $action->save();
         
                    $lc = new LogConverter('reimbursement_purchase_orders', 'approval');
                    $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . 'approved the PO.')->save();

                    $output['message'] = 'Your PO was approved.';
                    $output['id'] = $approver_id;
                    return $output;
                } else {
                    $output['message'] = 'Something went wrong.';
                    $output['id'] = null;
                }
            }

            $po->load('parcels');

            foreach ($po->parcels as $parcel) {
                perform_all_parcel_checks($parcel);
                guide_next_pending_step(2, $parcel->id);
            }
        } else {
            $output['message'] = 'Something went wrong.';
            $output['id'] = null;
            return $output;
        }

        // output message
        $output['message'] = 'This purchase order has been approved!';
        return $output;
    }

    public function poRemoveApprover(ReimbursementPurchaseOrders $po, Request $request)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($po) {
            $approver_id = $request->get('id');
            $approver = ApprovalRequest::where('approval_type_id', '=', 3)
                            ->where('link_type_id', '=', $po->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            if (count($approver)) {
                $approver->delete();
            }
 
            $lc = new LogConverter('reimbursement_purchase_orders', 'remove.approver');
            $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . 'removed approver '.$approver_id)->save();


            $data['message'] = '';
            $data['id'] = $request->get('id');
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function poAddHFAApprover(ReimbursementPurchaseOrders $po, Request $request)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($po) {
            $approver_id = $request->get('user_id');
            if (!ApprovalRequest::where('approval_type_id', '=', 3)
                        ->where('link_type_id', '=', $po->id)
                        ->where('user_id', '=', $approver_id)
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 3,
                    "link_type_id" => $po->id,
                    "user_id" => $approver_id
                ]);
                $newApprovalRequest->save();
                $lc = new LogConverter('reimbursement_purchase_orders', 'add.hfa.approver');
                $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . 'added an approver.')->save();

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

    public function newPONoteEntry(ReimbursementPurchaseOrders $po, Request $request)
    {
        if ($po && $request->get('po-note')) {
            $user = Auth::user();

            $note = new PurchaseOrderNote([
                'owner_id' => $user->id,
                'purchase_order_id' => $po->id,
                'note' => $request->get('po-note')
            ]);
            $note->save();
            $lc = new LogConverter('reimbursement_purchase_orders', 'addnote');
            $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . ' added note to reimbursement purchase order')->save();

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

    public function poNotifyLB(ReimbursementPurchaseOrders $po, Request $request)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        $po->load('parcels');

        // change PO to approved (7)
        updateStatus("po", $po, 'status_id', 7, 0, "");
        // $po->update([
        //     'status_id' => 7
        // ]);

        // change parcels'status to LB (approved by HFA 10) and HFA (PO Sent 40)
        foreach ($po->parcels as $parcel) {
            updateStatus("parcel", $parcel, 'landbank_property_status_id', 10, 0, "");
            updateStatus("parcel", $parcel, 'hfa_property_status_id', 40, 0, "");
            // $parcel->update([
            //         "landbank_property_status_id" => 10,
            //         "hfa_property_status_id" => 40
            // ]);

            guide_set_progress($parcel->id, 38, $status = 'started'); // final PO approval
            guide_set_progress($parcel->id, 39, $status = 'completed', 1); // sent po to LB
        }

        // Send email notification to LB
        $landbankInvoiceApprovers = User::where('entity_id', '=', $po->entity_id)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 17)
                                        ->where('active', '=', 1)
                                        ->select('id')
                                        ->get();
        $message_recipients_array = $landbankInvoiceApprovers->toArray();
        try {
            foreach ($message_recipients_array as $userToNotify) {
                $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                $emailNotification = new EmailNotificationPOApproved($userToNotify, $po->id);
                \Mail::to($current_recipient->email)->send($emailNotification);
                //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            dd($ex->getMessage());
        }

        $output['message'] = 'The PO is on its way!';
        return $output;
    }

    public function declinePO(ReimbursementPurchaseOrders $po)
    {
        if (!Auth::user()->isHFAPOApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($po) {
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id', '=', 3)
                            ->where('link_type_id', '=', $po->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            $action = new ApprovalAction([
                        'approval_request_id' => $approver->id,
                        'approval_action_type_id' => 4
                    ]);
            $action->save();
 
            $lc = new LogConverter('reimbursement_purchase_orders', 'decline');
            $lc->setFrom(Auth::user())->setTo($po)->setDesc(Auth::user()->email . 'declined the po.')->save();


            $data['message'] = 'This PO has been declined.';
            $data['id'] = $approver_id;
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    /*
    //
    //  INVOICE
    //
    */

    // for some reason the getInvoice and all its related functions are in InvoiceController :)

   

    /*
    //
    //  COMPLIANCE
    //
    */

    public function getCompliances(Parcel $parcel)
    {
        $parcel->load('compliances');
        foreach ($parcel->compliances as $compliance) {
            $compliance->load('auditor')
                ->load('analyst');
        }

        // check for compliance status and update parcel's statuses if legacy
        if ($parcel->compliance_score === null) {
            $last_compliance = $parcel->compliances->first();
            if ($last_compliance) {
                if ($last_compliance->score == "Pass" || $last_compliance->score == "1") {
                    $parcel->update([
                        'compliance_score' => 1
                    ]);
                }
            }
        }

        return view('parcels.compliance', compact('parcel'));
    }

    public function viewCompliance(compliance $compliance)
    {
        $compliance->load('parcel')
                    ->load('auditor')
                    ->load('analyst');
        // check roles, HFA or entity id can view
        if (Auth::user()->entity_id != $compliance->parcel->entity_id && Auth::user()->entity_id != 1) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $compliance->audit_date_formatted = date('m/d/Y', strtotime($compliance->audit_date));

        return view('modals.compliance-view', compact('compliance'));
    }

    public function editCompliance(Parcel $parcel, compliance $compliance)
    {
        // check roles, HFA admin or auditor can edit
        if (!Auth::user()->isHFAAdmin() && !Auth::user()->isHFAComplianceAuditor()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $hfa_users = User::where('entity_id', '=', 1)
                ->where('name', '!=', '')
                ->select('users.id', 'users.email', 'users.name', 'active')
                ->get();
        
        $compliance->audit_date_formatted = date('m/d/Y', strtotime($compliance->audit_date));

        return view('pages.compliance-edit', compact('compliance', 'hfa_users'));
    }

    public function deleteCompliance(Parcel $parcel, Request $request)
    {
        // check roles, HFA admin or auditor can edit
        if (!Auth::user()->isHFAAdmin() && !Auth::user()->isHFAComplianceAuditor()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }
        $compliance_id = $request->get('compliance_id');
        if ($compliance_id > 0) {
            $compliance = Compliance::where('id', '=', $compliance_id)->first();

            $lc = new LogConverter('Compliance', 'deleted');
            $lc->setFrom(Auth::user())->setTo($compliance)->setDesc(Auth::user()->email . ' deleted a compliance review.')->save();

            $compliance = Compliance::where('id', '=', $compliance_id)->first();
            $compliance->delete();
            
            // check if there are any active compliances and if not remove flags in parcel table
            $any_manual_compliances = Compliance::where('parcel_id', '=', $parcel->id)
                                            ->where('random_audit', '=', 1)
                                            ->count();
            if ($any_manual_compliances == 0) {
                $parcel->update([
                    'compliance_manual' => 0
                ]);
            }

            // check if there are any active compliances and if not remove flags in parcel table
            $any_manual_compliances = Compliance::where('parcel_id', '=', $parcel->id)
                                            ->where('random_audit', '=', 1)
                                            ->count();
            if ($any_manual_compliances == 0) {
                $parcel->update([
                    'compliance_manual' => 0
                ]);
            }

            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);

            return ['message'=>"This compliance review has been deleted.", 'error'=>0];
        } else {
            return ['message'=>"Something isn't right, I didn't find anything to delete.", 'error'=>1];
        }
    }

    public function createCompliance(Parcel $parcel, Request $request)
    {
        if (!Auth::user()->isHFAAdmin() && !Auth::user()->isHFAComplianceAuditor()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $random_audit = 0;
        if ($parcel->compliance) {
            $random_audit = 1;
        }

        // new Compliance
        $compliance = new Compliance([
                'property_type_id' => $parcel->parcel_type_id,
                'parcel_id' => $parcel->id,
                'program_id' => $parcel->program_id,
                'created_by_user_id' => Auth::user()->id,
                'random_audit' => $random_audit,
                'parcel_hfa_status_id' => $parcel->hfa_property_status_id
        ]);
        $compliance->save();

        // if parcel wasn't in random compliance, set compliance_manual flag
        if (!$parcel->compliance) {
            // $parcel->update([
            //     'compliance_manual' => 1,
            //     'hfa_property_status_id' => 21
            // ]);
            $parcel->update([
                'compliance_manual' => 1
            ]);
            updateStatus("parcel", $parcel, 'hfa_property_status_id', 21, 0, "");
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        $lc = new LogConverter('Compliance', 'created');
        $lc->setFrom(Auth::user())->setTo($compliance)->setDesc(Auth::user()->email . ' created a compliance review.')->save();

        // $hfa_users = User::where('entity_id','=',1)
        //         ->where('name','!=','')
        //         ->select('users.id','users.email','users.name','active')
        //         ->get();
        
        // $compliance->audit_date_formatted = date('m/d/Y',strtotime($compliance->audit_date));

        return ['message'=>"A compliance review has been created.", 'error'=>0];
        ;
    }

    public function saveCompliance(Parcel $parcel, compliance $compliance, Request $request)
    {
        if (!Auth::user()->isHFAAdmin() && !Auth::user()->isHFAComplianceAuditor()) {
            return ['message'=>"Oops, are you sure you're allowed to do this?", 'error'=>1];
        }

        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        if (!isset($forminputs['property_yes'])) {
            $forminputs['property_yes'] = 0;
        }
        if (!isset($forminputs['property_notes'])) {
            $forminputs['property_notes'] = null;
        }
        if (!isset($forminputs['analyst_id'])) {
            $forminputs['analyst_id'] = null;
        }
        if (!isset($forminputs['auditor_id'])) {
            $forminputs['auditor_id'] = null;
        }
        if (!isset($forminputs['checklist_yes'])) {
            $forminputs['checklist_yes'] = 0;
        }
        if (!isset($forminputs['checklist_notes'])) {
            $forminputs['checklist_notes'] = null;
        }
        if (!isset($forminputs['consolidated_certs_pass'])) {
            $forminputs['consolidated_certs_pass'] = 0;
        }
        if (!isset($forminputs['contractors_yes'])) {
            $forminputs['contractors_yes'] = 0;
        }
        if (!isset($forminputs['contractors_notes'])) {
            $forminputs['contractors_notes'] = null;
        }
        if (!isset($forminputs['environmental_yes'])) {
            $forminputs['environmental_yes'] = 0;
        }
        if (!isset($forminputs['environmental_notes'])) {
            $forminputs['environmental_notes'] = null;
        }
        if (!isset($forminputs['funding_limits_pass'])) {
            $forminputs['funding_limits_pass'] = 0;
        }
        if (!isset($forminputs['funding_limits_notes'])) {
            $forminputs['funding_limits_notes'] = null;
        }
        if (!isset($forminputs['inelligible_costs_yes'])) {
            $forminputs['inelligible_costs_yes'] = 0;
        }
        if (!isset($forminputs['inelligible_costs_notes'])) {
            $forminputs['inelligible_costs_notes'] = null;
        }
        if (!isset($forminputs['items_Reimbursed'])) {
            $forminputs['items_Reimbursed'] = null;
        }
        if (!isset($forminputs['note_mortgage_pass'])) {
            $forminputs['note_mortgage_pass'] = 0;
        }
        if (!isset($forminputs['note_mortgage_notes'])) {
            $forminputs['note_mortgage_notes'] = null;
        }
        if (!isset($forminputs['payment_processing_pass'])) {
            $forminputs['payment_processing_pass'] = 0;
        }
        if (!isset($forminputs['payment_processing_notes'])) {
            $forminputs['payment_processing_notes'] = null;
        }
        if (!isset($forminputs['loan_requirements_pass'])) {
            $forminputs['loan_requirements_pass'] = 0;
        }
        if (!isset($forminputs['loan_requirements_notes'])) {
            $forminputs['loan_requirements_notes'] = null;
        }
        if (!isset($forminputs['photos_yes'])) {
            $forminputs['photos_yes'] = 0;
        }
        if (!isset($forminputs['photos_notes'])) {
            $forminputs['photos_notes'] = null;
        }
        if (!isset($forminputs['salesforce_yes'])) {
            $forminputs['salesforce_yes'] = 0;
        }
        if (!isset($forminputs['salesforce_notes'])) {
            $forminputs['salesforce_notes'] = null;
        }
        if (!isset($forminputs['right_to_demo_pass'])) {
            $forminputs['right_to_demo_pass'] = 0;
        }
        if (!isset($forminputs['right_to_demo_notes'])) {
            $forminputs['right_to_demo_notes'] = null;
        }
        if (!isset($forminputs['reimbursement_doc_pass'])) {
            $forminputs['reimbursement_doc_pass'] = 0;
        }
        if (!isset($forminputs['reimbursement_doc_notes'])) {
            $forminputs['reimbursement_doc_notes'] = null;
        }
        if (!isset($forminputs['target_area_yes'])) {
            $forminputs['target_area_yes'] = 0;
        }
        if (!isset($forminputs['target_area_notes'])) {
            $forminputs['target_area_notes'] = null;
        }
        if (!isset($forminputs['sdo_pass'])) {
            $forminputs['sdo_pass'] = 0;
        }
        if (!isset($forminputs['sdo_notes'])) {
            $forminputs['sdo_notes'] = null;
        }
        if (!isset($forminputs['score'])) {
            $forminputs['score'] = null;
        }
        if ($forminputs['score'] == -1) {
            $forminputs['score'] = null;
        }
        if (!isset($forminputs['if_fail_corrected'])) {
            $forminputs['if_fail_corrected'] = 0;
        }
        if (!isset($forminputs['property_pass'])) {
            $forminputs['property_pass'] = 0;
        }
        if (!isset($forminputs['property_pass_notes'])) {
            $forminputs['property_pass_notes'] = null;
        }
        if (!isset($forminputs['random_audit'])) {
            $forminputs['random_audit'] = 0;
        }

        if ($forminputs['audit_date']) {
            $audit_date = $forminputs['audit_date'];
            $audit_date = Carbon\Carbon::createFromFormat('Y-m-d', $audit_date)->format('Y-m-d H:i:s');
        } else {
            $audit_date = null;
        }

        try {
            $compliance->update([
                'property_yes' => $forminputs['property_yes'],
                'property_notes' => $forminputs['property_notes'],
                'analyst_id' => $forminputs['analyst_id'],
                'auditor_id' => $forminputs['auditor_id'],
                'audit_date' => $audit_date,
                'checklist_yes' => $forminputs['checklist_yes'],
                'checklist_notes' => $forminputs['checklist_notes'],
                'consolidated_certs_pass' => $forminputs['consolidated_certs_pass'],
                'consolidated_certs_notes' => $forminputs['consolidated_certs_notes'],
                'contractors_yes' => $forminputs['contractors_yes'],
                'contractors_notes' => $forminputs['contractors_notes'],
                'environmental_yes' => $forminputs['environmental_yes'],
                'environmental_notes' => $forminputs['environmental_notes'],
                'funding_limits_pass' => $forminputs['funding_limits_pass'],
                'funding_limits_notes' => $forminputs['funding_limits_notes'],
                'inelligible_costs_yes' => $forminputs['inelligible_costs_yes'],
                'inelligible_costs_notes' => $forminputs['inelligible_costs_notes'],
                'items_Reimbursed' => $forminputs['items_Reimbursed'],
                'note_mortgage_pass' => $forminputs['note_mortgage_pass'],
                'note_mortgage_notes' => $forminputs['note_mortgage_notes'],
                'payment_processing_pass' => $forminputs['payment_processing_pass'],
                'payment_processing_notes' => $forminputs['payment_processing_notes'],
                'loan_requirements_pass' => $forminputs['loan_requirements_pass'],
                'loan_requirements_notes' => $forminputs['loan_requirements_notes'],
                'photos_yes' => $forminputs['photos_yes'],
                'photos_notes' => $forminputs['photos_notes'],
                'salesforce_yes' => $forminputs['salesforce_yes'],
                'salesforce_notes' => $forminputs['salesforce_notes'],
                'right_to_demo_pass' => $forminputs['right_to_demo_pass'],
                'right_to_demo_notes' => $forminputs['right_to_demo_notes'],
                'reimbursement_doc_pass' => $forminputs['reimbursement_doc_pass'],
                'reimbursement_doc_notes' => $forminputs['reimbursement_doc_notes'],
                'target_area_yes' => $forminputs['target_area_yes'],
                'target_area_notes' => $forminputs['target_area_notes'],
                'sdo_pass' => $forminputs['sdo_pass'],
                'sdo_notes' => $forminputs['sdo_notes'],
                'score' => $forminputs['score'],
                'if_fail_corrected' => $forminputs['if_fail_corrected'],
                'property_pass' => $forminputs['property_pass'],
                'property_pass_notes' => $forminputs['property_pass_notes'],
                'random_audit' => $forminputs['random_audit']
            ]);
        } catch (Exception $e) {
            // do task when error
            dd($e->getMessage()) ;   // insert query
        }

        //if pass then update the parcel
        $parcel = $compliance->parcel;

        if ($compliance->parcel_hfa_status_id == 21 && ($forminputs['score'] == 1 || $forminputs['score'] == "Pass")) {
            $new_hfa_property_status_id = 24; // ready for signator
        } else {
            $new_hfa_property_status_id = $compliance->parcel_hfa_status_id;
        }

        if ($forminputs['score'] == 1 || $forminputs['score'] == "Pass") {
            if ($compliance->random_audit == 1) {
                // $parcel->update([
                //     'compliance_manual' => null,
                //     'compliance_score' => $forminputs['score'],
                //     'hfa_property_status_id' => $new_hfa_property_status_id
                // ]);
                $parcel->update([
                    'compliance_manual' => null,
                    'compliance_score' => $forminputs['score']
                ]);
                updateStatus("parcel", $parcel, 'hfa_property_status_id', $new_hfa_property_status_id, 0, "");
            } else {
                //  $parcel->update([
                //     'compliance_manual' => 1,
                //     'compliance_score' => $forminputs['score'],
                //     'hfa_property_status_id' => $new_hfa_property_status_id
                // ]);
                $parcel->update([
                    'compliance_manual' => 1,
                    'compliance_score' => $forminputs['score']
                ]);
                updateStatus("parcel", $parcel, 'hfa_property_status_id', $new_hfa_property_status_id, 0, "");
            }

            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);
            
            $lc = new LogConverter('Compliance', 'passed');
            $lc->setFrom(Auth::user())->setTo($compliance)->setDesc(Auth::user()->email . ' passed a compliance review.')->save();
        } else {
            // $parcel->update([
            //     'compliance_score' => $forminputs['score'],
            //     'hfa_property_status_id' => 23
            // ]);
            $parcel->update([
                'compliance_score' => $forminputs['score']
            ]);
            updateStatus("parcel", $parcel, 'hfa_property_status_id', 23, 0, "");

            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);

            $lc = new LogConverter('Compliance', 'failed');
            $lc->setFrom(Auth::user())->setTo($compliance)->setDesc(Auth::user()->email . ' failed a compliance review.')->save();
        }

        //$compliance = Compliance::where('id','=',$complianceid)->first();
        $lc = new LogConverter('Compliance', 'saved');
        $lc->setFrom(Auth::user())->setTo($compliance)->setDesc(Auth::user()->email . ' saved a compliance review.')->save();

        return 1;
    }

    public function viewParcel($parcel = 0, $subtab = '')
    {
        if ($parcel) {
            session(['subtab' => $subtab]);
            return redirect('/home')->with('open_parcel_id', $parcel);
        }
    }
}

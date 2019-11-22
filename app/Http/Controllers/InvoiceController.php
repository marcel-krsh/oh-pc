<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use File;
use Carbon;
use Storage;
use Session;
use App\Models\Programs;
use Illuminate\Http\Request;
use DB;
use App\Models\Entity;
use App\Models\User;
use App\Models\ParcelsToReimbursementInvoice;
use App\Models\ReimbursementInvoice;
use App\Models\ReimbursementPurchaseOrders;
use App\Models\InvoiceNote;
use App\Models\InvoiceStatus;
use App\LogConverter;
use App\Models\ApprovalRequest;
use App\Models\ApprovalAction;
use App\Models\InvoiceItem;
use App\Models\Document;
use App\Models\Mail\EmailNotificationPaymentRequested;

class InvoiceController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    public function getInvoice(ReimbursementInvoice $invoice)
    {
        if (!Gate::allows('view-invoices')) {
            return 'Sorry you do not have access to the invoice.';
        }

        setlocale(LC_MONETARY, 'en_US');

        $invoice->load('parcels')
                ->load('entity')
                ->load('account')
                ->load('notes')
                ->load('account.transactions')
                ->load('program')
                ->load('transactions')
                ->load('clearedTransactions');

        $stat = [];
        $stat = $stat + $invoice->account->statsParcels->toArray()[0]
                        + $invoice->account->statsTransactions->toArray()[0]
                        + $invoice->account->statsCostItems->toArray()[0]
                        + $invoice->account->statsRequestItems->toArray()[0]
                        + $invoice->account->statsPoItems->toArray()[0]
                        + $invoice->account->statsInvoiceItems->toArray()[0];

        // get parcels
        $total = 0;
        $legacy = 0;
        foreach ($invoice->parcels as $parcel) {
            $parcel->cost_total = $parcel->costTotal();
            $parcel->requested_total = $parcel->requestedTotal();
            $parcel->requested_total_formatted = money_format('%n', $parcel->requested_total);
            $parcel->approved_total = $parcel->approvedTotal();
            $parcel->invoiced_total = $parcel->invoicedTotal();
            $parcel->invoiced_total_formatted = money_format('%n', $parcel->invoicedTotal());
            $total = $total + $parcel->invoiced_total;
            if ($parcel->legacy == 1 || $parcel->sf_parcel_id != null) {
                $legacy = 1;
            }
        }
        $total_unformatted = $total;
        $total = money_format('%n', $total);

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

        $lc = new LogConverter('reimbursement_invoices', 'view');
        $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' viewed reimbursement invoice')->save();
        
        // get entities
        $nip = Entity::where('id', 1)->with('state')->with('user')->first();

        $approvers = [];
        $approvers['landbank'] = null;
        $approvers['hfa_primary'] = null;
        $approvers['hfa_secondary'] = null;
        $approvers['hfa_tertiary'] = null;

        // get HFA primary approvers (type id 18)
        $approvers['hfa_primary'] = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 18)
                                    ->select('users.id', 'users.name')
                                    ->where('active', '=', 1)
                                    ->get();

        // get HFA secondary approvers (type id 19)
        $approvers['hfa_secondary'] = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 19)
                                    ->select('users.id', 'users.name')
                                    ->where('active', '=', 1)
                                    ->get();

        // get HFA tertiary approvers (type id 20)
        $approvers['hfa_tertiary'] = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 20)
                                    ->select('users.id', 'users.name')
                                    ->where('active', '=', 1)
                                    ->get();

        // get LB approvers (type id 17)
        $approvers['landbank'] = User::where('entity_id', '=', $invoice->entity_id)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 17)
                                    ->select('users.id', 'users.name')
                                    ->where('active', '=', 1)
                                    ->get();
        //
        //
        // check if there are any approval_requests, if not add all potential approvers
        // to approval requests
        //
        //
        $added_approvers['landbank'] = ApprovalRequest::where('approval_type_id', '=', 4)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->pluck('user_id as id');
        $added_approvers['primary'] = ApprovalRequest::where('approval_type_id', '=', 8)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->pluck('user_id as id');
        $added_approvers['secondary'] = ApprovalRequest::where('approval_type_id', '=', 9)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->pluck('user_id as id');
        $added_approvers['tertiary'] = ApprovalRequest::where('approval_type_id', '=', 10)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->pluck('user_id as id');

        $pending_approvers = [];
        $pending_approvers['landbank'] = null;
        $pending_approvers['hfa_primary'] = null;
        $pending_approvers['hfa_secondary'] = null;
        $pending_approvers['hfa_tertiary'] = null;

        // add all approvers if none in the system yet
        if (count($added_approvers['landbank']) == 0) {
            if (count($approvers['landbank'])) {
                foreach ($approvers['landbank'] as $landbankInvoiceApprover) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 4,
                        "link_type_id" => $invoice->id,
                        "user_id" => $landbankInvoiceApprover->id
                    ]);
                    $newApprovalRequest->save();
                }
            }
        } else {
            // get pending approvers for each group
            if (count($approvers['landbank']) > 0) {
                $pending_approvers['landbank'] = User::where('entity_id', '=', $invoice->entity_id)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 17)
                                        ->where('active', '=', 1)
                                        ->whereNotIn('id', $added_approvers['landbank'])
                                        ->select('users.id', 'users.name')
                                        ->get();
            }
        }

        if (count($added_approvers['primary']) == 0) {
            if (count($approvers['hfa_primary'])) {
                foreach ($approvers['hfa_primary'] as $HFAPrimaryApprover) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 8,
                        "link_type_id" => $invoice->id,
                        "user_id" => $HFAPrimaryApprover->id
                    ]);
                    $newApprovalRequest->save();
                }
            }
        } else {
            if (count($approvers['hfa_primary']) > 0) {
                $pending_approvers['hfa_primary'] = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 18)
                                        ->where('active', '=', 1)
                                        ->whereNotIn('id', $added_approvers['primary'])
                                        ->select('users.id', 'users.name')
                                        ->get();
            }
        }

        if (count($added_approvers['secondary']) == 0) {
            if (count($approvers['hfa_secondary'])) {
                foreach ($approvers['hfa_secondary'] as $HFASecondaryApprover) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 9,
                        "link_type_id" => $invoice->id,
                        "user_id" => $HFASecondaryApprover->id
                    ]);
                    $newApprovalRequest->save();
                }
            }
        } else {
            if (count($approvers['hfa_secondary']) > 0) {
                $pending_approvers['hfa_secondary'] = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 19)
                                        ->where('active', '=', 1)
                                        ->whereNotIn('id', $added_approvers['secondary'])
                                        ->select('users.id', 'users.name')
                                        ->get();
            }
        }

        if (count($added_approvers['tertiary']) == 0) {
            if (count($approvers['hfa_tertiary'])) {
                foreach ($approvers['hfa_tertiary'] as $HFATertiaryApprover) {
                    $newApprovalRequest = new  ApprovalRequest([
                        "approval_type_id" => 10,
                        "link_type_id" => $invoice->id,
                        "user_id" => $HFATertiaryApprover->id
                    ]);
                    $newApprovalRequest->save();
                }
            }
        } else {
            if (count($approvers['hfa_tertiary']) > 0) {
                $pending_approvers['hfa_tertiary'] = User::where('entity_id', '=', 1)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 20)
                                        ->where('active', '=', 1)
                                        ->whereNotIn('id', $added_approvers['tertiary'])
                                        ->select('users.id', 'users.name')
                                        ->get();
            }
        }

        //
        //
        // Get approvals for each approver group (including actions)
        //
        //
        $approvals = [];
        $approvals['landbank'] = ApprovalRequest::where('approval_type_id', '=', 4)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->with('actions')
                                    ->with('actions.action_type')
                                    ->get();
        $approvals['hfa_primary'] = ApprovalRequest::where('approval_type_id', '=', 8)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->with('actions')
                                    ->with('actions.action_type')
                                    ->get();
        $approvals['hfa_secondary'] = ApprovalRequest::where('approval_type_id', '=', 9)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->with('actions')
                                    ->with('actions.action_type')
                                    ->get();
        $approvals['hfa_tertiary'] = ApprovalRequest::where('approval_type_id', '=', 10)
                                    ->where('link_type_id', '=', $invoice->id)
                                    ->with('actions')
                                    ->with('actions.action_type')
                                    ->get();




        //dd($this->getApprovalsByRoleId($invoice->id, 18, 8));
        //
        //
        // Checks for each group
        // - are all groups fully approved
        // - do each group have some approvals
        // - do each group have all approvals
        // - has current user already approved
        // - is current user an approver
        //
        //

        $isFullyApprovedByAll = 0;

        $hasApprovals = [];
        $hasApprovals['landbank'] = 0;
        $hasApprovals['hfa_primary'] = 0;
        $hasApprovals['hfa_secondary'] = 0;
        $hasApprovals['hfa_tertiary'] = 0;

        $isApproved = [];
        $isApproved['landbank'] = 0;
        $isApproved['hfa_primary'] = 0;
        $isApproved['hfa_secondary'] = 0;
        $isApproved['hfa_tertiary'] = 0;

        $isApprover = [];
        $isApprover['landbank'] = 0;
        $isApprover['hfa_primary'] = 0;
        $isApprover['hfa_secondary'] = 0;
        $isApprover['hfa_tertiary'] = 0;
        //dd($approvals['landbank']);
        // check if there is a approval action 1 for each approver for this request
        $tmp_previous_approved = 1;
        if (count($approvals['landbank'])) {
            foreach ($approvals['landbank'] as $approval) {
                if (Auth::user()->id == $approval->user_id) {
                    $isApprover['landbank'] = 1;
                }
                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved['landbank'] = 1;
                        $hasApprovals['landbank'] = 1;
                        $tmp_previous_approved = 1;
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                        $hasApprovals['landbank'] = 1;
                        $isApproved['landbank'] = 0;
                    //$tmp_previous_approved = 1;
                    } else {
                        $isApproved['landbank'] = 0;
                        $tmp_previous_approved = 0;
                    }
                } else {
                    $tmp_previous_approved = 0;
                    $isApproved['landbank'] = 0;
                }
            }
        }
        //dd($isApproved['landbank']);

        $tmp_previous_approved = 1;
        if (count($approvals['hfa_primary'])) {
            foreach ($approvals['hfa_primary'] as $approval) {
                if (Auth::user()->id == $approval->user_id) {
                    $isApprover['hfa_primary'] = 1;
                }
                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved['hfa_primary'] = 1;
                        $hasApprovals['hfa_primary'] = 1;
                        $tmp_previous_approved = 1;
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                        $hasApprovals['hfa_primary'] = 1;
                        $isApproved['hfa_primary'] = 0;
                        $tmp_previous_approved = 1;
                    } else {
                        $isApproved['hfa_primary'] = 0;
                        $tmp_previous_approved = 0;
                    }
                } else {
                    $tmp_previous_approved = 0;
                    $isApproved['hfa_primary'] = 0;
                }
            }
        }
        //dd($approvals['hfa_secondary']);
        $tmp_previous_approved = 1;
        if (count($approvals['hfa_secondary'])) {
            foreach ($approvals['hfa_secondary'] as $approval) {
                if (Auth::user()->id == $approval->user_id) {
                    $isApprover['hfa_secondary'] = 1;
                }
                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved['hfa_secondary'] = 1;
                        $hasApprovals['hfa_secondary'] = 1;
                        $tmp_previous_approved = 1;
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                        $hasApprovals['hfa_secondary'] = 1;
                        $isApproved['hfa_secondary'] = 0;
                        $tmp_previous_approved = 1;
                    } else {
                        $isApproved['hfa_secondary'] = 0;
                        $tmp_previous_approved = 0;
                    }
                } else {
                    $tmp_previous_approved = 0;
                    $isApproved['hfa_secondary'] = 0;
                }
            }
        }
    
        $tmp_previous_approved = 1;
        if (count($approvals['hfa_tertiary'])) {
            foreach ($approvals['hfa_tertiary'] as $approval) {
                if (Auth::user()->id == $approval->user_id) {
                    $isApprover['hfa_tertiary'] = 1;
                }
                if (count($approval->actions)) {
                    $action = $approval->actions->first();
                    if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                        $isApproved['hfa_tertiary'] = 1;
                        $hasApprovals['hfa_tertiary'] = 1;
                        $tmp_previous_approved = 1;
                    } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                        $hasApprovals['hfa_tertiary'] = 1;
                        $isApproved['hfa_tertiary'] = 0;
                        $tmp_previous_approved = 1;
                    } else {
                        $isApproved['hfa_tertiary'] = 0;
                        $tmp_previous_approved = 0;
                    }
                } else {
                    $tmp_previous_approved = 0;
                    $isApproved['hfa_tertiary'] = 0;
                }
            }
        }

        $isReadyForPayment = 0;
        if ($isApproved['landbank'] && $isApproved['hfa_primary'] && $isApproved['hfa_secondary'] && $isApproved['hfa_tertiary']) {
            $isReadyForPayment = 1;

            if ($invoice->status_id == 5) {
                $invoice = ReimbursementInvoice::where('id', '=', $invoice->id)->with('parcels')->first();
                $invoice->update([
                    'status_id' => 3 // back to pending HFA
                ]);

                if ($invoice->parcels) {
                    foreach ($invoice->parcels as $parcel) {
                        $parcel->update([
                            "landbank_property_status_id" => 13,
                            "hfa_property_status_id" => 27
                        ]);
                        perform_all_parcel_checks($parcel);
                        guide_next_pending_step(2, $parcel->id);
                    }
                }
                $lc = new LogConverter('reimbursement_invoices', 'approved by all HFA');
                $lc->setFrom(Auth::user())->setTo($invoice)->setDesc('All HFA approved the invoice.')->save();
            }
        }

        // additional fields (cannot update if they are set before update)
        
        $sum_transactions = 0;
        if ($invoice->clearedTransactions) {
            foreach ($invoice->clearedTransactions as $transaction) {
                if ($transaction->credit_debit == 'c') {
                    $sum_transactions = $sum_transactions - $transaction->amount;
                } else {
                    $sum_transactions = $sum_transactions + $transaction->amount;
                }
            }
        }
        $total_unformatted = round($total_unformatted, 2);
        $sum_transactions = round($sum_transactions, 2);
        $balance = round($total_unformatted - $sum_transactions, 2);
        
        // change invoice status if fully paid
        if ($balance <= 0) {
            $invoice->update([
                'status_id' => 6
            ]);
            $invoice->status_id = 6;
        } elseif ($invoice->status_id == 6) {
            // invoice marked as paid, but not all transactions have been cleared.
            $invoice->update([
                'status_id' => 4
            ]);
            $invoice->status_id = 4;
        }

        $invoice->legacy = $legacy;

        // reload parcels
        $invoice->load('parcels');

        // quick fix to make existing transaction edit modal work:
        // flash invoice page in case transaction modals are being open to reload the correct tab or the whole page
        Session::flash('is_invoice_view', 1);
        // session()->flash('is_invoice_view', 1);


        return view('pages.invoice', compact(
            'invoice',
            'nip',
            'total',
            'stat',
            'approvals',
            'approvers',
            'hasApprovals',
            'isApproved',
            'isApprover',
            'isReadyForPayment',
            'pending_approvers',
            'sum_transactions',
            'balance'
        ));
    }

    public function sendForPayment(ReimbursementInvoice $invoice)
    {
        if (!Auth::user()->isHFAPrimaryInvoiceApprover() && !Auth::user()->isHFASecondaryInvoiceApprover() && !Auth::user()->isHFATertiaryInvoiceApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $invoice->update([
                'status_id' => 4 // pending payment
            ]);

            $lc = new LogConverter('reimbursement_invoices', 'payment pending');
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
                    $emailNotification = new EmailNotificationPaymentRequested($userToNotify, $invoice->id);
                    \Mail::to($current_recipient->email)->send($emailNotification);
                    //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                dd($ex->getMessage());
            }

            // update parcel steps
            if ($invoice->parcels) {
                foreach ($invoice->parcels as $invoice_parcel) {
                    guide_set_progress($invoice_parcel->id, 52, $status = 'completed', 1); // notify fiscal agent
                }
            }

            $data['message'] = 'The invoice was sent to a fiscal agent!';
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    private function HFADeclinedInvoice(ReimbursementInvoice $invoice)
    {
        if (!Auth::user()->isHFAPrimaryInvoiceApprover() && !!Auth::user()->isHFASecondaryInvoiceApprover() && !!Auth::user()->isHFATertiaryInvoiceApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $invoice->update([
                'status_id' => 5 // declined
            ]);

            $invoice->load('parcels');

            if ($invoice->parcels) {
                foreach ($invoice->parcels as $parcel) {
                    $parcel->update([
                        "landbank_property_status_id" => 9,
                        "hfa_property_status_id" => 23
                    ]);

                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);
                }
            }
            $lc = new LogConverter('reimbursement_invoices', 'declined by HFA');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' declined the invoice.')->save();

            $data['message'] = 'The invoice was declined!';
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    public function invoiceAddHFAApprover(ReimbursementInvoice $invoice, Request $request)
    {
        if (!Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice && $request->get('user_id') > 0) {
            if ($request->get('approval_type') == 8) {
                $approval_type = 8;
                $required_role_id = 18; //HFA primary
            } elseif ($request->get('approval_type') == 9) {
                $approval_type = 9;
                $required_role_id = 19; //HFA secondary
            } elseif ($request->get('approval_type') == 10) {
                $approval_type = 10;
                $required_role_id = 20; //HFA tertiary
            } else {
                $approval_type = 4;
                $required_role_id = 3; //HFA admin
            }
            if (!ApprovalRequest::where('approval_type_id', '=', $approval_type)
                        ->where('link_type_id', '=', $invoice->id)
                        ->where('user_id', '=', $request->get('user_id'))
                        ->whereHas('approver', function ($query) use ($required_role_id) {
                            $query->whereHas('roles', function ($query) use ($required_role_id) {
                                $query->where('role_id', '=', $required_role_id)
                                        ->orWhere('role_id', '=', 3);
                            });
                        })
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => $approval_type,
                    "link_type_id" => $invoice->id,
                    "user_id" => $request->get('user_id')
                ]);
                $newApprovalRequest->save();
                $lc = new LogConverter('reimbursement_invoices', 'add.hfa.approver');
                $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' added a HFA approver.')->save();

                $data['message'] = 'You now are an approver.';
                return $data;
            } else {
                $data['message'] = 'Something went wrong. There is already a record for this approval request.';
                return $data;
            }
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    public function approveInvoiceWithRequest(Request $request, ReimbursementInvoice $invoice, $approvers = null, $document_ids = null)
    {
        if ($request->get('approval_type') !== null) {
            $approval_type = $request->get('approval_type');
        } else {
            $approval_type = 4;
        }
        $this->approveInvoice($invoice, $approvers, $document_ids, $approval_type);

        $data['message'] = 'Your invoice was approved.';
        $data['id'] = Auth::user()->id;
        return $data;
    }

    public function approveInvoice(ReimbursementInvoice $invoice, $approvers = null, $document_ids = null, $approval_type = 4)
    {
        if ((!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) {
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
             
                        $lc = new LogConverter('reimbursement_invoices', 'approval by proxy');
                        $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' approved the invoice for '.$approver->name)->save();
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
         
                    $lc = new LogConverter('reimbursement_invoices', 'approval');
                    $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' approved the invoice.')->save();

                    $data['message'] = 'Your invoice was approved.';
                    $data['id'] = $approver_id;
                    return $data;
                } else {
                    $data['message'] = 'Something went wrong.';
                    $data['id'] = null;
                }
            }

            $invoice->load('parcels');

            if ($invoice->parcels) {
                foreach ($invoice->parcels as $parcel) {
                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);
                }
            }
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function submitInvoice(ReimbursementInvoice $invoice)
    {
        if ((!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        // check that all LB approvals are in
        $approvals = $this->getApprovalsByRoleId($invoice->id, 4, 17, 18);
        
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
                    $isApproved =0;
                    $tmp_previous_approved = 0;
                }
            }
        }

        if ($isApproved == 0) {
            $output['message'] = "Some approvals are missing. I couldn't submit this invoice.";
            $output['error'] = 1;
            return $output;
        }

        if ($invoice) {
            // update invoice status, each parcels' status
            $invoice->update([
                'status_id' => 3 // pending HFA approval
            ]);

            $invoice->load('parcels');

            if ($invoice->parcels) {
                foreach ($invoice->parcels as $parcel) {
                    $parcel->update([
                        "landbank_property_status_id" => 13,
                        "hfa_property_status_id" => 27
                    ]);

                    perform_all_parcel_checks($parcel);
                    guide_next_pending_step(2, $parcel->id);
                }
            }

            $lc = new LogConverter('reimbursement_invoices', 'submit to HFA');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . 'submitted the invoice to HFA.')->save();

            $data['message'] = 'The invoice was sent to HFA!';
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            return $data;
        }
    }

    public function invoiceAddLBApprover(ReimbursementInvoice $invoice, Request $request)
    {
        if ((!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice && $request->get('user_id') > 0) {
            if (!ApprovalRequest::where('approval_type_id', '=', 4)
                        ->where('link_type_id', '=', $invoice->id)
                        ->where('user_id', '=', $request->get('user_id'))
                        ->count()) {
                $newApprovalRequest = new  ApprovalRequest([
                    "approval_type_id" => 4,
                    "link_type_id" => $invoice->id,
                    "user_id" => $request->get('user_id')
                ]);
                $newApprovalRequest->save();
                $lc = new LogConverter('reimbursement_invoices', 'add.lb.approver');
                $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' added LB approver '.$request->get('user_id'))->save();

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

    // this is to record decline action during approval process
    public function declineInvoice(ReimbursementInvoice $invoice, Request $request)
    {
        // check user belongs to invoice entity
        if ((!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $invoice->entity_id) && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $approver_id = Auth::user()->id;
            if ($request->get('approval_type') !== null) {
                $approval_type = $request->get('approval_type');
            } else {
                $approval_type = 4;
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
 
            if ($this->HFADeclinedInvoice($invoice)) {
                $lc = new LogConverter('reimbursement_invoices', 'decline');
                $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' declined the invoice.')->save();


                $data['message'] = 'This invoice has been declined.';
                $data['id'] = $approver_id;
                return $data;
            } else {
                $data['message'] = 'Something went wrong. The invoice status could not be updated';
                $data['id'] = null;
                return $data;
            }
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    // hfa_role_id is the corresponding role for HFA users
    private function getApprovalsByRoleId($invoice_id, $approval_type_id = 0, $role_id, $hfa_role_id = null)
    {
        if ($hfa_role_id) {
            $approvals = ApprovalRequest::where('approval_type_id', '=', $approval_type_id)
                ->where('link_type_id', '=', $invoice_id)
                ->with('actions')
                ->with('actions.action_type')
                ->whereHas('approver', function ($query) use ($role_id, $hfa_role_id) {
                    $query->whereHas('roles', function ($query) use ($role_id, $hfa_role_id) {
                        $query->where('role_id', '=', $role_id);
                        $query->orWhere('role_id', '=', $hfa_role_id);
                    });
                })
                ->get();
        } else {
            $approvals = ApprovalRequest::where('approval_type_id', '=', $approval_type_id)
                ->where('link_type_id', '=', $invoice_id)
                ->with('actions')
                ->with('actions.action_type')
                ->whereHas('approver', function ($query) use ($role_id) {
                    $query->whereHas('roles', function ($query) use ($role_id) {
                        $query->where('role_id', '=', $role_id);
                    });
                })
                ->get();
        }
        
        if ($approvals) {
            return $approvals;
        } else {
            return null;
        }
    }

    public function removeApprover(ReimbursementInvoice $invoice, Request $request)
    {
        if (!Auth::user()->isLandbankInvoiceApprover() && !Auth::user()->isHFAPrimaryInvoiceApprover() && !Auth::user()->isHFASecondaryInvoiceApprover() && !Auth::user()->isHFATertiaryInvoiceApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        if ($invoice) {
            $approver_id = $request->get('id');
            $approval_type = $request->get('approval_type');
            $approver = ApprovalRequest::where('approval_type_id', '=', $approval_type)
                            ->where('link_type_id', '=', $invoice->id)
                            ->where('user_id', '=', $approver_id)
                            ->first();
            if (count($approver)) {
                $approver->delete();
            }
 
            $lc = new LogConverter('reimbursement_invoices', 'remove.approver');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' removed approver '.$approver_id)->save();


            $data['message'] = '';
            $data['id'] = $request->get('id');
            return $data;
        } else {
            $data['message'] = 'Something went wrong.';
            $data['id'] = null;
            return $data;
        }
    }

    public function newNoteEntry(ReimbursementInvoice $invoice, Request $request)
    {
        if ($invoice && $request->get('invoice-note')) {
            $user = Auth::user();

            $note = new InvoiceNote([
                'owner_id' => $user->id,
                'reimbursement_invoice_id' => $invoice->id,
                'note' => $request->get('invoice-note')
            ]);
            $note->save();
            $lc = new LogConverter('reimbursement_invoices', 'addnote');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email . ' added note to reimbursement invoice')->save();

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

    public function getParcelsFromInvoiceId(ReimbursementInvoice $invoice)
    {
        // get parcel ids associated with the request
        $parcelids_array = ParcelsToReimbursementInvoice::where('reimbursement_invoice_id', $invoice->id)->pluck('parcel_id')->toArray();
                        
        $invoice->load('status')
            ->load('parcels')
            ->load('entity');

        $total = 0;
        $invoice->legacy = 0;
        setlocale(LC_MONETARY, 'en_US');
        foreach ($invoice->parcels as $parcel) {
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
            if ($parcel->legacy == 1) {
                $invoice->legacy = 1;
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

        return ['parcels'=>$invoice->parcels,'invoice_id'=>$invoice->id];
    }

    public function editInvoice(ReimbursementInvoice $invoice)
    {
        if (Auth::user()->entity_type == "hfa") {
            $statuses = InvoiceStatus::get();

            return view('modals.invoice-edit', compact('invoice', 'statuses'));
        } else {
            return "You are not authorized to see this resource.";
        }
    }

    public function saveInvoice(ReimbursementInvoice $invoice, Request $request)
    {
        if (Auth::user()->entity_type == "hfa") {
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
            if ($forminputs['updated_at']) {
                $forminput_updated_at = $forminputs['updated_at'];
                $updated = Carbon\Carbon::createFromFormat('Y-m-d', $forminput_updated_at)->format('Y-m-d H:i:s');
            } else {
                $updated = null;
            }

            $invoice->update([
                'created_at' => $created,
                'updated_at' => $updated,
                'active' => $forminputs['active'],
                'status_id' => $forminputs['status_id']
                ]);
             
            return 1;
        } else {
            return "You are not authorized to see this resource.";
        }
    }

    public function approveInvoiceUploadSignature(ReimbursementInvoice $invoice, Request $request)
    {
        if (app('env') == 'local') {
            app('debugbar')->disable();
        }
        
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';

            if ($request->get('approvaltype') !== null) {
                if ($request->get('approvaltype')==4) {
                    $categories_json = json_encode(['33'], true);
                } elseif ($request->get('approvaltype')==8) {
                    $categories_json = json_encode(['34'], true); // 34 is HFA invoice primary
                } elseif ($request->get('approvaltype')==9) {
                    $categories_json = json_encode(['35'], true);
                } elseif ($request->get('approvaltype')==10) {
                    $categories_json = json_encode(['36'], true);
                } else {
                    $categories_json = json_encode(['33'], true);
                }
            } else {
                $categories_json = json_encode(['33'], true);
            }
           
            $approvers = explode(",", $request->get('approvers'));

            $user = Auth::user();

            $invoice->load('parcels');

            // get parcels from req $req->parcels
            foreach ($invoice->parcels as $parcel) {
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
                    $document->approve_categories([33, 34, 35, 36]);

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
                $approval_type = 4;
            }
            $approval_process = $this->approveInvoice($invoice, $approvers, $document_ids, $approval_type);

            return $document_ids;
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    public function approveInvoiceUploadSignatureComments(ReimbursementInvoice $invoice, Request $request)
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

    /*    public function approveInvoice(ReimbursementInvoice $invoice)
        {
            // check user belongs to request entity
            if(!Auth::user()->isHFAPrimaryInvoiceApprover() && !Auth::user()->isHFASecondaryInvoiceApprover() && !Auth::user()->isHFATertiaryInvoiceApprover() && (!Auth::user()->isLandbankInvoiceApprover() || Auth::user()->entity_id != $invoice->entity_id) ){
                $output['message'] = 'Something went wrong...';
                return $output;
            }
    
            $invoice->load('parcels');
    
            // create an approver with that user if not already in there
            $approver_id = Auth::user()->id;
            $approver = ApprovalRequest::where('approval_type_id','=',4)
                            ->where('link_type_id','=',$invoice->id)
                            ->where('user_id','=',$approver_id)
                            ->first();
    
            if(!$approver){
                $approver = new ApprovalRequest([
                            'approval_type_id' => 4,
                            'link_type_id' => $invoice->id,
                            'user_id' => $approver_id
                ]);
                $approver->save();
            }
    
            // create an approval action if the last one isn't already an approval
            $previous_approval = ApprovalAction::where('approval_request_id','=',$approver->id)
                                        ->orderBy('id','DESC')
                                        ->first();
            if($previous_approval){
                if($previous_approval->approval_action_type_id != 1){
                     $action = new ApprovalAction([
                                'approval_request_id' => $approver->id,
                                'approval_action_type_id' => 1
                    ]);
                    $action->save();
                }
            }else{
                $action = new ApprovalAction([
                            'approval_request_id' => $approver->id,
                            'approval_action_type_id' => 1
                ]);
                $action->save();
            }
    
            // how many HFA Primary approvers?
            $HFAInvoicePrimaryApprovers = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id','=',18)
                                    ->select('id')
                                    ->get();
    
            $is_approved = 0;
            $is_approved_primary = 0;
            $is_approved_secondary = 0;
            $is_approved_tertiary = 0;
            $tmp_previous_approved = 1;
            foreach($HFAInvoicePrimaryApprovers as $hfa_primary_approver){
    
                $approved_action = ApprovalAction::join('approval_requests','approval_actions.approval_request_id', '=', 'approval_requests.id')
                                   ->where('approval_requests.approval_type_id','=',4)
                                   ->where('approval_requests.user_id','=',$hfa_primary_approver->id)
                                   ->where('approval_requests.link_type_id','=',$invoice->id)
                                   ->orderBy('approval_actions.id','DESC')
                                   ->select('approval_actions.approval_action_type_id as action')
                                   ->first();
    
                if($approved_action){
                    if($approved_action->action == 1 && $tmp_previous_approved == 1){
                        $is_approved = 1;
                        $is_approved_primary = 1;
                    }else{
                        $is_approved = 0;
                    }
                    $tmp_previous_approved = $approved_action->action;
                }
    
            }
    
            // how many HFA Secondary approvers?
            $HFAInvoiceSecondaryApprovers = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id','=',19)
                                    ->select('id')
                                    ->get();
            $tmp_previous_approved = 1;
            foreach($HFAInvoiceSecondaryApprovers as $hfa_secondary_approver){
    
                $approved_action = ApprovalAction::join('approval_requests','approval_actions.approval_request_id', '=', 'approval_requests.id')
                                   ->where('approval_requests.approval_type_id','=',4)
                                   ->where('approval_requests.user_id','=',$hfa_secondary_approver->id)
                                   ->where('approval_requests.link_type_id','=',$invoice->id)
                                   ->orderBy('approval_actions.id','DESC')
                                   ->select('approval_actions.approval_action_type_id as action')
                                   ->first();
    
                if($approved_action){
                    if($approved_action->action == 1 && $tmp_previous_approved == 1){
                        $is_approved = 1;
                        $is_approved_secondary = 1;
                    }else{
                        $is_approved = 0;
                    }
                    $tmp_previous_approved = $approved_action->action;
                }
    
            }
    
            // how many HFA Tertiary approvers?
            $HFAInvoiceTertiaryApprovers = User::where('entity_id', '=', 1)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id','=',20)
                                    ->select('id')
                                    ->get();
            $tmp_previous_approved = 1;
            foreach($HFAInvoiceTertiaryApprovers as $hfa_tertiary_approver){
    
                $approved_action = ApprovalAction::join('approval_requests','approval_actions.approval_request_id', '=', 'approval_requests.id')
                                   ->where('approval_requests.approval_type_id','=',4)
                                   ->where('approval_requests.user_id','=',$hfa_tertiary_approver->id)
                                   ->where('approval_requests.link_type_id','=',$invoice->id)
                                   ->orderBy('approval_actions.id','DESC')
                                   ->select('approval_actions.approval_action_type_id as action')
                                   ->first();
    
                if($approved_action){
                    if($approved_action->action == 1 && $tmp_previous_approved == 1){
                        $is_approved = 1;
                        $is_approved_tertiary = 1;
                    }else{
                        $is_approved = 0;
                    }
                    $tmp_previous_approved = $approved_action->action;
                }
    
            }
    
            if(!$is_approved ){
                $output['message'] = 'Your Primary Invoice Approval has been recorded!';
                return $output;
            }
    
            // output message
            $output['message'] = 'This invoice has been approved!';
            return $output;
        }
        */

    public function createInvoice(ReimbursementPurchaseOrders $po)
    {
        if (!Auth::user()->isLandbankInvoiceApprover() && !Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';
            return $output;
        }

        // Create Invoice if not already there!
        $invoice = ReimbursementInvoice::where('po_id', '=', $po->id)->first();
        if (!$invoice) {
            $invoice = new ReimbursementInvoice([
                    'entity_id' => $po->entity_id,
                    'program_id' => $po->program_id,
                    'account_id' => $po->account_id,
                    'po_id' => $po->id,
                    'status_id' => 2, //pending LB approval
                    'active' => 1
            ]);
            $invoice->save();

            $po->load('parcels');

            // Attach parcels to Invoice & change the status of each parcel in the po
            foreach ($po->parcels as $parcel) {
                $parcel_to_invoice = new ParcelsToReimbursementInvoice([
                        'parcel_id' => $parcel->id,
                        'reimbursement_invoice_id' => $invoice->id
                ]);
                $parcel_to_invoice->save();

                $parcel->update([
                        "landbank_property_status_id" => 13,
                        "hfa_property_status_id" => 24
                ]);
                
                perform_all_parcel_checks($parcel);
                guide_next_pending_step(2, $parcel->id);

                // make sure the invoice_id is set in every invoice_item (if they exist)
                $existing_invoice_items = InvoiceItem::where('parcel_id', '=', $parcel->id);
                $existing_invoice_items->update([
                        "invoice_id" => $invoice->id
                ]);
            }

            $po->load('poItems');

            // Create invoice_items from po_items
            foreach ($po->poItems as $po_item) {
                $new_invoice_item = new InvoiceItem([
                        'invoice_id' => $invoice->id,
                        'parcel_id' => $po_item->parcel_id,
                        'account_id' => $po_item->account_id,
                        'program_id' => $po_item->program_id,
                        'entity_id' => $po_item->entity_id,
                        'expense_category_id' => $po_item->expense_category_id,
                        'amount' => $po_item->amount,
                        'vendor_id' => $po_item->vendor_id,
                        'description' => $po_item->description,
                        'notes' => $po_item->notes,
                        'ref_id' => $po_item->id
                ]);
                $new_invoice_item->save();
            }
            // output message
            $output['message'] = 'Your invoice is ready!<br /><br /><a href="/invoices/'.$invoice->id.'" class="uk-button uk-button-success">Click to view invoice</a>';
        } else {
            // output message
            $output['message'] = 'An invoice already existed!';
        }
        return $output;
    }
}

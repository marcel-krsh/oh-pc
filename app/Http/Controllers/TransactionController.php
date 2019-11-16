<?php

namespace App\Http\Controllers;

use App\LogConverter;
use App\Models\DispositionInvoice;
use App\Models\Parcel;
use App\Models\Program;
use App\Models\RecaptureInvoice;
use App\Models\ReimbursementInvoice;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\User;
use Auth;
use Carbon;
use DB;
use File;
use Gate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Storage;

class TransactionController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    public function createTransactionFromInvoice(ReimbursementInvoice $invoice)
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }

        return view('modals.new-transaction-from-invoice', compact('status_array', 'invoice'));
    }

    public function createTransactionFromDispositionInvoice(DispositionInvoice $invoice)
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }

        return view('modals.new-transaction-from-disposition-invoice', compact('status_array', 'invoice'));
    }

    public function createTransactionFromRecaptureInvoice(RecaptureInvoice $invoice)
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Something went wrong.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }

        return view('modals.new-transaction-from-recapture-invoice', compact('status_array', 'invoice'));
    }

    public function transactionBalanceCredit()
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Sorry you do not have access.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }
        $programs = Program::where('active', 1)->where('entity_id', '<>', 1)->orderBy('program_name', 'ASC')->get()->all();

        return view('modals.new-transaction-balance-credit', compact('status_array', 'programs'));
    }

    public function transactionBalanceDebit()
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Sorry you do not have access.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }
        $programs = Program::where('active', 1)->where('entity_id', '<>', 1)->orderBy('program_name', 'ASC')->get()->all();

        return view('modals.new-transaction-balance-debit', compact('status_array', 'programs'));
    }

    public function transactionFundingAward()
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Sorry you do not have access.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }
        $programs = Program::where('active', 1)->where('entity_id', '1')->orderBy('program_name', 'ASC')->get()->all();

        return view('modals.new-transaction-funding-award', compact('status_array', 'programs'));
    }

    public function transactionFundingReduction()
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Sorry you do not have access.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }
        $programs = Program::where('active', 1)->where('entity_id', '1')->orderBy('program_name', 'ASC')->get()->all();

        return view('modals.new-transaction-funding-reduction', compact('status_array', 'programs'));
    }

    public function transactionLandbankCredit()
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Sorry you do not have access.';

            return $output;
        }

        $statuses = TransactionStatus::get()->toArray();
        $status_array = [];
        foreach ($statuses as $status) {
            $status_array[$status['id']]['name'] = $status['status_name'];
            $status_array[$status['id']]['id'] = $status['id'];
        }
        $programs = Program::where('active', 1)->where('entity_id', '<>', '1')->orderBy('program_name', 'ASC')->get()->all();

        return view('modals.new-transaction-landbank-credit', compact('status_array', 'programs'));
    }

    public function landbankRecaptureInvoiceOptions(Request $request)
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Sorry you do not have access.';

            return $output;
        }
        // get options
        $option_title = 'Recapture Invoice'; // the plural is added on the template

        $options = \App\Models\RecaptureInvoice::where('program_id', $request->get('program'))->get()->all();

        // return the snippet
        return view('partials.new-transaction-landbank-credit-options', compact('option_title', 'options'));
    }

    public function saveTransaction(Request $request)
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            return 'Sorry you do not have the correct access to do this transaction.';
        }
        $transaction2 = 0; // disable second transaction by default (double entry)
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);
        if ($request->get('invoice_id') > 0) {
            $invoice_id = $request->get('invoice_id');
            if (! $invoice_id) {
                return 'I could not find which invoice this payment was for';
            }
        }

        $is_disposition_invoice = $request->get('disposition');
        $is_recapture_invoice = $request->get('recapture');
        $is_funding_award = $request->get('funding_award');
        $is_funding_reduction = $request->get('funding_reduction');
        $is_balance_credit = $request->get('balance_credit');
        $is_balance_debit = $request->get('balance_debit');
        $is_landbank_credit = $request->get('landbank_credit');

        /// update this to be dynamic:
        $hfaProgramId = 1;
        $hfaAccountId = 1;
        $hfaEntityId = 1;

        if ($is_disposition_invoice) {
            $transaction_type_id = 2; // Disposition Invoice
            $transaction_category = 6; // Disposition Recapture
            $credit_debit = 'c';
            $invoice = DispositionInvoice::where('id', '=', $invoice_id)->first();
            if (! count($invoice)) {
                return 'I could not find a valid invoice.';
            }

            $account_id = $invoice->account_id;
            $link_to_type_id = $invoice->id;
            $owner_type = 'program';
            $owner_id = $invoice->program_id;
        } elseif ($is_recapture_invoice) {
            $transaction_type_id = 6; // Recapture Invoice
            $transaction_category = 2; // Recapture Recapture
            $credit_debit = 'c';
            $invoice = DispositionInvoice::where('id', '=', $invoice_id)->first();
            if (! count($invoice)) {
                return 'I could not find a valid invoice.';
            }

            $account_id = $invoice->account_id;
            $link_to_type_id = $invoice->id;
            $owner_type = 'program';
            $owner_id = $invoice->program_id;
        } elseif ($is_balance_credit) {
            $program = Program::where('id', $forminputs['program_id'])->first();
            if (! count($program)) {
                return 'I could not find a valid program.';
            }
            // Debit from HFA
            $transaction_type_id = 5; // Transfer
            $transaction_category = 4; // Transfer
            $credit_debit = 'd';
            ////////////////////
            $account_id = $hfaAccountId; // default to hfa
            $link_to_type_id = $program->id;
            $owner_type = 'program';
            $owner_id = $hfaProgramId;

            ////////////////////////////////////////////////////
            // Credit LB
            $transaction2 = 1; // enable second transaction
            $transaction2_type_id = 3; // Deposit
            $transaction2_category = 1; // Deposit
            $credit_debit2 = 'c';
            ////////////////////
            // Get Account Id
            $account = \App\Models\Account::where('entity_id', $program->entity_id)->first();

            $account_id2 = $account->id;
            $link_to_type_id2 = $program->id;
            $owner_type2 = 'program';
            $owner_id2 = $program->id;
        } elseif ($is_balance_debit) {
            // return 'Balance Debit';
            $program = Program::where('id', $forminputs['program_id'])->first();
            if (! count($program)) {
                return 'I could not find a valid program.';
            }
            // Credit HFA
            $transaction_type_id = 3; // Transfer
            $transaction_category = 1; // Transfer
            $credit_debit = 'c';
            ////////////////////
            $account_id = $hfaAccountId; // default to hfa
            $link_to_type_id = $hfaProgramId;
            $owner_type = 'program';
            $owner_id = $program->id;

            ////////////////////////////////////////////////////
            // Debit from LB
            $transaction2 = 1; // enable second transaction
            $transaction2_type_id = 5; // Transfer
            $transaction2_category = 4; // Transfer
            $credit_debit2 = 'd';
            ////////////////////
            // Get Account Id

            $account = \App\Models\Account::where('entity_id', $program->entity_id)->first();

            $account_id2 = $account->id;
            $link_to_type_id2 = $program->id;
            $owner_type2 = 'program';
            $owner_id2 = $program->id;
        } elseif ($is_funding_award) {
            $transaction_type_id = 3; // Deposit
            $transaction_category = 1; // Deposit
            $credit_debit = 'c';
            ////////////////////
            $account_id = 1;
            $link_to_type_id = 1;
            $owner_type = 'program';
            $owner_id = 1;
        } elseif ($is_funding_reduction) {
            $transaction_type_id = 5; // Transfer
            $transaction_category = 4; // Transfer
            $credit_debit = 'd';
            ////////////////////
            $account_id = 1;
            $link_to_type_id = 1;
            $owner_type = 'program';
            $owner_id = 1;
        } elseif ($is_landbank_credit) {
            $transaction_type_id = 2; // Disposition Invoice
            $transaction_category = 6; // Disposition Recapture
            $credit_debit = 'c';
            ////////////////////
            $account_id = $invoice->account_id;
            $link_to_type_id = $invoice->id;
            $owner_type = 'program';
            $owner_id = $invoice->program_id;

        ////////////////////////////////////////////////////
        } else {
            $transaction_type_id = 1; // Reimbursement Invoice
            $transaction_category = 3; // Reimbursement
            $credit_debit = 'd';
            $invoice = ReimbursementInvoice::where('id', '=', $invoice_id)->first();
            if (! count($invoice)) {
                return 'I could not find a valid invoice.';
            }
            ////////////////////
            $account_id = $invoice->account_id;
            $link_to_type_id = $invoice->id;
            $owner_type = 'program';
            $owner_id = $invoice->program_id;
        }

        if (! isset($forminputs['amount'])) {
            $forminputs['amount'] = 0;
        } else {
            $forminputs['amount'] = str_replace('-', '', $forminputs['amount']);
            $forminputs['amount'] = (float) $forminputs['amount'];
        }

        if (! isset($forminputs['date_entered'])) {
            $forminputs['date_entered'] = null;
        }
        if (! isset($forminputs['date_cleared'])) {
            $forminputs['date_cleared'] = null;
        }
        if (! isset($forminputs['status_id'])) {
            $forminputs['status_id'] = null;
        }
        if (! isset($forminputs['transaction_note'])) {
            $forminputs['transaction_note'] = null;
        }
        if ($forminputs['date_entered']) {
            $forminput_date_entered = $forminputs['date_entered'];
            $date_entered = Carbon\Carbon::createFromFormat('Y-m-d', $forminput_date_entered)->format('Y-m-d H:i:s');
        } else {
            $date_entered = null;
        }
        if ($forminputs['date_cleared']) {
            $forminput_date_cleared = $forminputs['date_cleared'];
            $date_cleared = Carbon\Carbon::createFromFormat('Y-m-d', $forminput_date_cleared)->format('Y-m-d H:i:s');
        } else {
            $date_cleared = null;
        }

        $transaction = new Transaction([
                'account_id' => $account_id,
                'credit_debit' => $credit_debit,
                'amount' => $forminputs['amount'],
                'transaction_category_id' => $transaction_category,
                'type_id' => $transaction_type_id,
                'link_to_type_id' => $link_to_type_id,
                'status_id' => $forminputs['status_id'],
                'owner_type' => $owner_type,
                'owner_id' => $owner_id,
                'transaction_note' => $forminputs['transaction_note'],
                'date_entered' => $date_entered,
                'date_cleared' => $date_cleared,
        ]);

        $transaction->save();

        if ($transaction2 == 1) {
            // run second transaction
            $transaction = new Transaction([
                'account_id' => $account_id2,
                'credit_debit' => $credit_debit2,
                'amount' => $forminputs['amount'],
                'transaction_category_id' => $transaction2_category,
                'type_id' => $transaction2_type_id,
                'link_to_type_id' => $link_to_type_id2,
                'status_id' => $forminputs['status_id'],
                'owner_type' => $owner_type2,
                'owner_id' => $owner_id2,
                'transaction_note' => $forminputs['transaction_note'],
                'date_entered' => $date_entered,
                'date_cleared' => $date_cleared,
            ]);

            $transaction->save();
        }

        if ($is_disposition_invoice) {
            $lc = new LogConverter('transactions', 'new.disposition.invoice.payment');
            $lc->setFrom(Auth::user())->setTo($transaction)->setDesc(Auth::user()->email.'created a new payment for disposition invoice '.$invoice->id)->save();
            $lc = new LogConverter('disposition_invoices', 'new.payment');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email.'created a new payment')->save();

            if ($forminputs['amount'] >= $invoice->balance()) {
                //mark paid
                $invoice->update(['paid'=>1, 'status_id'=>6]);

                // get all the dispositions
                $dispositions = \App\Models\DispositionsToInvoice::where('disposition_invoice_id', $invoice->id)->get()->all();
                foreach ($dispositions as $disposition) {
                    //update disposition step
                        guide_set_progress($disposition->id, 22, $status = 'completed', 1); // mark dispositions paid.
                        guide_next_pending_step(1, $disposition->id);
                }
            }
        } elseif ($is_balance_credit) {
        } elseif ($is_balance_debit) {
        } elseif ($is_funding_award) {
        } elseif ($is_funding_reduction) {
        } elseif ($is_landbank_credit) {
        } else {
            $lc = new LogConverter('transactions', 'new.invoice.payment');
            $lc->setFrom(Auth::user())->setTo($transaction)->setDesc(Auth::user()->email.'created a new payment for invoice '.$invoice->id)->save();
            $lc = new LogConverter('reimbursement_invoices', 'new.payment');
            $lc->setFrom(Auth::user())->setTo($invoice)->setDesc(Auth::user()->email.'created a new payment')->save();

            if ($forminputs['amount'] >= $invoice->balance()) {
                //mark paid
                $invoice->update(['status_id'=>6]);

                // get all the parcels
                $parcelstoinvoice = \App\Models\ParcelsToReimbursementInvoice::where('reimbursement_invoice_id', $invoice->id)->get()->all();
                foreach ($parcelstoinvoice as $parceltoinvoice) {
                    $parcel = Parcel::where('id', '=', $parceltoinvoice->parcel_id)->first();

                    if ($parcel) {
                        //update parcel step
                        guide_set_progress($parcel->id, 54, $status = 'completed', 1); // mark dispositions paid.
                        guide_next_pending_step(2, $parcel->id);
                        if (isset($parcel->disposition->id)) {
                            guide_next_pending_step(1, $parcel->disposition->id);
                        }
                    }
                }
            }
        }

        return 1;
    }

    public function deleteTransaction(Transaction $transaction)
    {
        if (! Auth::user()->isHFAFiscalAgent() && ! Auth::user()->isHFAAdmin()) {
            $output['message'] = 'Are you sure you can do this?';
            $output['error'] = 1;

            return $output;
        }

        if ($transaction) {
            $lc = new LogConverter('transactions', 'delete');
            $lc->setFrom(Auth::user())->setTo($transaction)->setDesc(Auth::user()->email.'deleted a transaction.')->save();

            $transaction->delete();
        }

        $output['message'] = 'This transaction has been deleted!';

        return $output;
    }
}

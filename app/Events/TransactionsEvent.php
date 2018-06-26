<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Transaction;
use App\ReimbursementInvoice;
use App\DispositionInvoice;
use App\RecaptureInvoice;

/**
 * Transactions Event
 *
 * @category Events
 * @license  Proprietary and confidential
 */
class TransactionsEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function transactionCreated(Transaction $transaction)
    {
        //for reimbursement invoices - update the payment data cache
        if ($transaction->type_id == 1) {
            $invoice = ReimbursementInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }

        //for disposition invoices - update the payment data cache
        if ($transaction->type_id == 2) {
            $invoice = DispositionInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }

        //for recapture invoices - update the payment data cache
        if ($transaction->type_id == 6) {
            $invoice = RecaptureInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }
    }
    public function transactionUpdated(Transaction $transaction)
    {
        //for reimbursement invoices - update the payment data cache
        if ($transaction->type_id == 1) {
            $invoice = ReimbursementInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }

        //for disposition invoices - update the payment data cache
        if ($transaction->type_id == 2) {
            $invoice = DispositionInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }

        //for recapture invoices - update the payment data cache
        if ($transaction->type_id == 6) {
            $invoice = RecaptureInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }
    }
    public function transactionDeleted(Transaction $transaction)
    {
        //for reimbursement invoices - update the payment data cache
        if ($transaction->type_id == 1) {
            $invoice = ReimbursementInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }

        //for disposition invoices - update the payment data cache
        if ($transaction->type_id == 2) {
            $invoice = DispositionInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }

        //for recapture invoices - update the payment data cache
        if ($transaction->type_id == 6) {
            $invoice = RecaptureInvoice::find($transaction->link_to_type_id);
            $invoice->updatePaymentDetails();
        }
    }
}

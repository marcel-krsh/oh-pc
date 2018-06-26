<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\RecaptureInvoice;
use App\RecaptureItem;
use App\DispositionInvoice;
use App\DispositionItems;
use App\ReimbursementInvoice;
use App\InvoiceItem;
use Log;

/**
 *InvoiceItem Event
 *
 * @category Events
 * @license  Proprietary and confidential
 */
class InvoiceItemsEvent
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

    // Reimbursement Invoice Items
    public function reimbursementItemCreated(InvoiceItem $invoice_item)
    {
        //for reimbursement invoices - update the payment data cache
    
        $invoice = ReimbursementInvoice::find($invoice_item->invoice_id);
        $invoice->updatePaymentDetails();
        \Log::debug("Invoice Item Created Event Fired on Invoice ID: ".$invoice_item->invoice_id);
    }
    public function reimbursementItemUpdated(InvoiceItem $invoice_item)
    {
        //for reimbursement invoices - update the payment data cache
    
        $invoice = ReimbursementInvoice::find($invoice_item->invoice_id);
        $invoice->updatePaymentDetails();
        \Log::debug("Invoice Item Updated Event Fired on Invoice ID: ".$invoice_item->invoice_id);
    }
    public function reimbursementItemDeleted(InvoiceItem $invoice_item)
    {
        //for reimbursement invoices - update the payment data cache
        if (!is_null($invoice_item->invoice_id)) {
            $invoice = ReimbursementInvoice::find($invoice_item->invoice_id);
            $invoice->updatePaymentDetails();
            \Log::debug("Invoice Item Deleted Event Fired on Invoice ID: ".$invoice_item->invoice_id);
        } else {
            \Log::debug("Invoice Item Deleted Event Fired on BUT COULD NOT FIND Invoice ID: ");
        }
    }

    // Disposition Invoice Items
    public function dispositionItemCreated(DispositionItems $invoice_item)
    {
        //for disposition invoices - update the payment data cache
    
        $invoice = DispositionInvoice::find($invoice_item->disposition_invoice_id);
        $invoice->updatePaymentDetails();
        \Log::debug("Disposition Item Created Event Fired:". $invoice_item->disposition_invoice_id);
    }
    public function dispositionItemUpdated(DispositionItems $invoice_item)
    {
        //for disposition invoices - update the payment data cache
    
        $invoice = DispositionInvoice::find($invoice_item->disposition_invoice_id);
        $invoice->updatePaymentDetails();
        \Log::debug("Disposition Item Updated Event Fired:". $invoice_item->disposition_invoice_id);
    }
    public function dispositionItemDeleted(DispositionItems $invoice_item)
    {
        //for disposition invoices - update the payment data cache
    
        $invoice = DispositionInvoice::find($invoice_item->disposition_invoice_id);
        $invoice->updatePaymentDetails();
        \Log::debug("Disposition Item Deleted Event Fired:". $invoice_item->disposition_invoice_id);
    }

    // Recapture Invoice Items
    public function recaptureItemCreated(recapture_items $invoice_item)
    {
        //for recapture invoices - update the payment data cache
    
        $invoice = RecaptureInvoice::find($invoice_item->recapture_invoice_id);
        $invoice->updatePaymentDetails();
    }
    public function recaptureItemUpdated(recapture_items $invoice_item)
    {
        //for recapture invoices - update the payment data cache
    
        $invoice = RecaptureInvoice::find($invoice_item->recapture_invoice_id);
        $invoice->updatePaymentDetails();
    }
    public function recaptureItemDeleted(recapture_items $invoice_item)
    {
        //for recapture invoices - update the payment data cache
    
        $invoice = RecaptureInvoice::find($invoice_item->recapture_invoice_id);
        $invoice->updatePaymentDetails();
    }
}

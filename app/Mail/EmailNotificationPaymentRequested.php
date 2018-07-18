<?php

namespace App\Mail;

use App\User;
use App\ReimbursementInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\HistoricEmail;

/**
 * EmailNotificationPaymentRequested
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailNotificationPaymentRequested extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Owner
     */
    public $owner;

    /**
     * @var mixed Invoice
     */
    public $invoice;

    /**
     * EmailNotificationPaymentRequested constructor.
     *
     * @param int  $recipient_id
     * @param null $invoice_id
     */
    public function __construct($recipient_id = 1, $invoice_id = null)
    {
        $this->invoice_id = $invoice_id;
        $this->invoice = ReimbursementInvoice::where('id', '=', $invoice_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = "[OHFA Allita] You received a payment request";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $owner = $this->owner;
        $invoice = $this->invoice;
        $greeting = "INVOICE ".$this->invoice_id." was approved and submitted for payment by HFA.";
       
        $introLines[] = "You can now process transaction.";
        $outroLines[] = [];

        $actionText = "View INVOICE";

        $actionUrl = secure_url('/invoices/'.$this->invoice_id);

        $level = "success";
        $level2 = "error";
        $outroLines = [];

        //clear session vars.
        session(['ownerId'=>"",'newUserId' => ""]);

        // save in database
        if ($owner) {
            $body = \view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                "user_id" => $owner->id,
                "type" => 'reimbursement_invoices',
                "type_id" => $this->invoice_id,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

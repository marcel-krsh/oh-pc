<?php

namespace App\Mail;

use App\User;
use App\RecaptureInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\HistoricEmail;

/**
 * ApproverNotification
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class RecaptureApproverNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Owner
     */
    public $owner;

    /**
     * @var mixed Disposition
     */
    public $invoice;

    /**
     * @var int Recipient ID
     */
    public $recipient_id;

    /**
     * ApproverNotification constructor.
     *
     * @param int  $recipient_id
     * @param null $disposition_id
     */
    public function __construct($recipient_id = 1, $invoice_id = null)
    {
        $this->invoice = RecaptureInvoice::where('id', '=', $invoice_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = "[OHFA Allita] Recapture Invoice Approval Request";
        $this->recipient_id = $recipient_id;
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
        $greeting = "You have been listed as an approver on a recapture invoice.";
       
        $introLines[] = "You are receiving this notification because you have a recapture invoice to approve. Please login to read more.";
        
        $actionText = "View recapture invoice";
        $actionUrl = secure_url('/recapture_invoice/'.$invoice->id);
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
                "type" => 'recapture',
                "type_id" => $invoice->id,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

<?php

namespace App\Mail;

use App\User;
use App\ReimbursementInvoice;
use App\Disposition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\HistoricEmail;

/**
 * EmailNotificationDispositionReleaseRequested
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailNotificationDispositionReleaseRequested extends Mailable
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
     * EmailNotificationDispositionReleaseRequested constructor.
     *
     * @param int  $recipient_id
     * @param null $invoice_id
     * @param int  $disposition_id
     */
    public function __construct($recipient_id = 1, $invoice_id = null, $disposition_id = 0)
    {
        $this->invoice_id = $invoice_id;
        $this->disposition_id = $disposition_id;
        if ($disposition_id != 0) {
            $this->disposition = Disposition::where('id', '=', $disposition_id)->first();
        } else {
            $this->disposition = null;
        }
        $this->invoice = ReimbursementInvoice::where('id', '=', $invoice_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = "[OHFA Allita] You received a release request";
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
        
        $introLines[] = "";
        $outroLines[] = [];

        if ($this->disposition) {
            $greeting = "DISPOSITION ".$this->disposition->id." has a release request.";
            $type = 'dispositions';
            $type_id = $this->disposition->id;
            $actionText = "View DISPOSITION";
            $actionUrl = secure_url('/dispositions/'.$this->disposition->parcel_id.'/'.$this->disposition->id);
        } else {
            $greeting = "INVOICE ".$this->invoice_id." has release requests.";
            $type = 'reimbursement_invoices';
            $type_id = $this->invoice_id;
            $actionText = "View DISPOSITION INVOICE";
            $actionUrl = secure_url('/disposition_invoice/'.$this->invoice_id);
        }

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
                "type" => $type,
                "type_id" => $type_id,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

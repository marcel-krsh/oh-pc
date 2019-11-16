<?php

namespace App\Mail;

use App\HistoricEmail;
use App\ReimbursementPurchaseOrders;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailNotificationPOApproved.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailNotificationPOApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Owner
     */
    public $owner;

    /**
     * @var mixed PO
     */
    public $po;

    /**
     * EmailNotificationPOApproved constructor.
     *
     * @param int  $recipient_id
     * @param null $po_id
     */
    public function __construct($recipient_id = 1, $po_id = null)
    {
        $this->po_id = $po_id;
        $this->po = ReimbursementPurchaseOrders::where('id', '=', $po_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = '[OHFA Allita] You received a new PO';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $owner = $this->owner;
        $po = $this->po;
        $greeting = 'PO '.$this->po_id.' was approved by HFA.';

        $introLines[] = 'You can now create an invoice.';
        $outroLines[] = [];

        $actionText = 'View PO';

        $actionUrl = secure_url('/po/'.$this->po_id);

        $level = 'success';
        $level2 = 'error';
        $outroLines = [];

        //clear session vars.
        session(['ownerId'=>'', 'newUserId' => '']);

        // save in database
        if ($owner) {
            $body = \view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                'user_id' => $owner->id,
                'type' => 'reimbursement_purchase_orders',
                'type_id' => $this->po_id,
                'subject' => $this->subject,
                'body' => $body,
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

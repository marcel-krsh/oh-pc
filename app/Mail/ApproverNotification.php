<?php

namespace App\Mail;

use App\Disposition;
use App\HistoricEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ApproverNotification.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class ApproverNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Owner
     */
    public $owner;

    /**
     * @var mixed Disposition
     */
    public $disposition;

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
    public function __construct($recipient_id = 1, $disposition_id = null)
    {
        $this->disposition = Disposition::where('id', '=', $disposition_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = '[OHFA Allita] Disposition Approval Request';
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
        $disposition = $this->disposition;
        $greeting = 'You have been listed as an approver on a disposition.';

        $introLines[] = 'You are receiving this notification because you have a disposition to approve. Please login to read more.';

        $actionText = 'View disposition';
        $actionUrl = secure_url('/dispositions/'.$disposition->parcel_id.'/'.$disposition->id);
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
                'type' => 'dispositions',
                'type_id' => $disposition->id,
                'subject' => $this->subject,
                'body' => $body,
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

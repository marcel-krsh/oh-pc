<?php

namespace App\Mail;

use App\Disposition;
use App\HistoricEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * DispositionApprovedNotification.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class DispositionApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;
    public $disposition;

    public function __construct($recipient_id = 1, $disposition_id = null)
    {
        $this->disposition = Disposition::where('id', '=', $disposition_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = '[OHFA Allita] Disposition Approval';
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
        $greeting = 'A disposition has been approved!';

        $introLines[] = 'You are receiving this notification because a disposition that was submitted to HFA has been approved. Please login to read more.';

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

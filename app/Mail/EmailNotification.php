<?php

namespace App\Mail;

use App\Models\Communication;
use App\Models\HistoricEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailNotification.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Owner
     */
    public $owner;

    /**
     * @var mixed Message
     */
    public $message;

    /**
     * EmailNotification constructor.
     *
     * @param int  $recipient_id
     * @param null $message_id
     */
    public function __construct($recipient_id = 1, $message_id = null)
    {
        $this->message = Communication::where('id', '=', $message_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = $this->owner;
        $this->subject = '[OHFA PC] '.$this->message->subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $owner = $this->owner;
        $message = $this->message;
        $greeting = 'A new message has been posted.';

        $introLines[] = 'You are receiving this notification because you have an unread message. Please login to read more.';

        $actionText = 'View message';
        $actionUrl = secure_url('/view_message/'.$message->id);
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
                'type' => 'communications',
                'type_id' => $message->id,
                'subject' => $this->subject,
                'body' => $body,
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

<?php

namespace App\Mail;

use App\User;
use App\Notice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\HistoricEmail;

/**
 * EmailNoticeNotification
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailNoticeNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed $owner
     */
    public $owner;

    /**
     * @var mixed $message
     */
    public $message;

    /**
     * @var mixed $user
     */
    public $user;

    /**
     * EmailNoticeNotification constructor.
     *
     * @param null $recipient_id
     * @param null $message_id
     */
    public function __construct($recipient_id = null, $message_id = null)
    {
        $this->message = Notice::where('id', '=', $message_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->user = Auth::user();
        $this->subject = $this->message->subject;
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
        $greeting = $this->message->subject;
       
        $introLines[] = $message->body;
        
        $tracker = $message->id;
        
        // save in database
        if ($owner) {
            $body = \view('emails.send_notice', compact('greeting', 'introLines', 'tracker'));
            $email_saved_in_db = new  HistoricEmail([
                "user_id" => $owner->id,
                "type" => 'Notice',
                "type_id" => $message->id,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_notice', compact('greeting', 'introLines', 'tracker'));
    }
}

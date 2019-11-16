<?php

namespace App\Mail;

use App\Communication;
use App\HistoricEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailNotification.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class SystemMessage extends Mailable
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
     * @var mixed action link
     */
    public $action_link;

    /**
     * @var mixed action text
     */
    public $action_text;

    /**
     * EmailNotification constructor.
     *
     * @param int  $recipient_id
     * @param null $message_id
     */
    public function __construct($message_id, $action_link, $action_text, $recipient_id)
    {
        $this->message = Communication::where('id', '=', $message_id)->get()->first();
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->action_link = $action_link;
        $this->action_text = $action_text;
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
        $greeting = '[Allita] System Message';

        $actionText = $this->action_text;
        $actionUrl = $this->action_link;
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

        return $this->view('emails.send_system_messages', compact('greeting', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

<?php

namespace App\Mail;

use App\HistoricEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailSystemAdmin.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailSystemAdmin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed Message
     */
    public $message;

    /**
     * @var mixed Path
     */
    public $path;

    /**
     * @var mixed Recipient ID
     */
    public $recipient_id;

    /**
     * EmailSystemAdmin constructor.
     *
     * @param null $message
     * @param null $path
     * @param null $recipient_id
     */
    public function __construct($message = null, $path = null, $recipient_id = null)
    {
        $this->message = $message;
        $this->path = $path;
        $this->recipient_id = $recipient_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $greeting = 'Notice';

        $introLines[] = $this->message;

        $actionText = 'Go';
        $actionUrl = secure_url($this->path);
        $level = 'error';
        $level2 = 'error';
        $outroLines = [];

        // save in database
        if ($this->recipient_id != null) {
            $body = \view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                'user_id' => $this->recipient_id,
                'type' => 'admin',
                'type_id' => null,
                'subject' => '[Allita] System Admin Notification',
                'body' => $body,
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'))
                        ->subject('[Allita] System Admin Notification');
    }
}

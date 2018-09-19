<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * DownloadReady
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailFailedLogin extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;
    public $subject;
    public $reset_link;

    /**
     * Create a new message instance.
     *
     * @param null $recipient_id
     */
    public function __construct($recipient_id = null)
    {
        $this->subject = "[Allita PC] System Message";
        $this->owner = User::where('id', '=', $recipient_id)->get()->first();
        $this->reset_link = env('DEVCO_RESET_URL');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $greeting = "Hello,";
       
        $introLines[] = 'It appears someone is trying to login to Allita PC using your username and has yet to put in the correct password. If this is not you, please notify your admin right away at admin@allita.org. If it is you, please try resetting your password here '.$this->reset_link.'.';
        
        $actionText = "";

        $actionUrl = '';

        $actionText2 = "";

        $actionUrl2 = '';

        $level = "success";
        $level2 = "success";
        $outroLines = [];

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

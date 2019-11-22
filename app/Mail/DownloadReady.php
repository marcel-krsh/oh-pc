<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\HistoricEmail;

/**
 * DownloadReady
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class DownloadReady extends Mailable
{
    use Queueable, SerializesModels;

    public $folder;
    public $filename;
    public $recipient_id;

    /**
     * Create a new message instance.
     *
     * @param null $folder
     * @param null $filename
     * @param null $recipient_id
     */
    public function __construct($folder = null, $filename = null, $recipient_id = null)
    {
        $this->folder = $folder;
        $this->filename = $filename;
        $this->subject = "[OHFA Allita] Export report ready";
        $this->recipient_id = $recipient_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $greeting = "";
       
        $introLines[] = "Your report is ready to download.";
        
        $actionText = "View all files";

        $actionUrl = secure_url($this->folder);

        $actionText2 = "Download file";

        $actionUrl2 = secure_url($this->folder.'/'.$this->filename.'/download');

        $level = "success";
        $level2 = "success";
        $outroLines = [];

        // save in database
        if ($this->recipient_id != null) {
            $body = \view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                "user_id" => $this->recipient_id,
                "type" => 'file',
                "type_id" => null,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.send_communication', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

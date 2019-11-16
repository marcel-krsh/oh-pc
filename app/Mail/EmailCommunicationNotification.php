<?php

namespace App\Mail;

use App\Models\HistoricEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailCommunicationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $nt;
    public $user;
    public $subject;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nt)
    {
        $this->nt = $nt;
        $this->data = $nt->data;
        $this->subject = '[OHFA PC] Notification: '.$this->data['heading'];
        Log::info($nt);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $owner = $this->nt->from_user;
        $user = $this->nt->to_user;
        $data = $this->nt->data;
        $greeting = 'Hello '.$user->person->first_name.',';
        $action_text = 'VIEW COMMUNICATION';
        $action_url = $data['base_url'].$this->nt->token;
        $level = 'success';
        $level2 = 'error';
        $introLines[] = 'NEW COMMUNICATION:';
        // $introLines[]  = 'Use below link to view the message.';
        $introLines[] = date('M d, Y h:i', strtotime($this->nt->created_at));
        $introLines[] = 'FROM: '.$owner->name;
        $introLines[] = $data['heading'];

        $outroLines = [];
        // save in database

        if ($owner) {
            $body = \view('emails.new_communication', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2'));
            $email_saved_in_db = new HistoricEmail([
        'user_id' => $user->id,
        'type'    => 'communication',
        'type_id' => $owner->id,
        'subject' => $this->subject,
        'body'    => $body,
      ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.new_communication', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2', 'email_saved_in_db'));
    }
}

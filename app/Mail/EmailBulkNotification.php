<?php

namespace App\Mail;

use App\Models\HistoricEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailBulkNotification extends Mailable
{
  use Queueable, SerializesModels;

  public $data;
  public $user;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($data, $user)
  {
    $this->data    = $data;
    $this->user    = $user;
    $this->subject = "[OHFA Allita PC] Notifications ";
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $owner         = '';
    $user          = $this->user;
    $data          = $this->data;
    $greeting      = "Hello " . $user->person->first_name . ',';
    $action_text   = "";
    $action_url    = '';
    $level         = "success";
    $level2        = "error";
    $introLines[]  = "Here are your recent notifications:";
    $notifications = $data;
    $outroLines    = [];
    // save in database

    if ($owner) {
      $body              = \view('emails.bulk_notifications', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2', 'notifications'));
      $email_saved_in_db = new HistoricEmail([
        "user_id" => $user->id,
        "type"    => 'communication',
        "type_id" => null,
        "subject" => $this->subject,
        "body"    => $body,
      ]);
      $email_saved_in_db->save();
    }
    return $this->view('emails.bulk_notifications', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2', 'notifications'));
  }
}

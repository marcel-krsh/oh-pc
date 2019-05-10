<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HistoricEmail;
use App\Models\User;

class EmailCommunicationNotification extends Mailable
{
  use Queueable, SerializesModels;

  public $cr;
  public $token;
  public $user;
  public $subject;
  public $communication;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($cr, $token)
  {
    $this->cr            = $cr;
    $this->token         = $token;
    $this->communication = $this->cr->communication;
    $this->subject       = "[OHFA Allita PC] New Message: " . $this->communication->subject;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $owner         = $this->cr->communication->owner;
    $user          = $this->cr->user;
    $communication = $this->communication;
    $greeting      = "Hello " . $user->person->first_name . ',';
    $action_text   = "VIEW MESSAGE";
    $action_url    = secure_url('/communication/view-message', $communication->id) . "/" . $user->id . "?t=" . $this->token;
    $level         = "success";
    $level2        = "error";
    $introLines[]  = "NEW MESSAGE:";
    // $introLines[]  = 'Use below link to view the message.';
    $introLines[]  =  date('M d, Y h:i', strtotime($communication->created_at));
    $introLines[]  = 'FROM: ' . $owner->name;
    $introLines[]  = $communication->subject;

    $outroLines    = [];
    // save in database

    if ($owner) {
      $body              = \view('emails.new_communication', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2'));
      $email_saved_in_db = new HistoricEmail([
        "user_id" => $user->id,
        "type"    => 'communication',
        "type_id" => $owner->id,
        "subject" => $this->subject,
        "body"    => $body,
      ]);
      $email_saved_in_db->save();
    }
    return $this->view('emails.new_communication', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2', 'email_saved_in_db'));
  }
}

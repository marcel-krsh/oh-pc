<?php

namespace App\Mail;

use App\Models\HistoricEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class EmailBulkNotification extends Mailable
{
  use Queueable, SerializesModels;

  public $data;
  public $user;
  public $owner;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($data, $user)
  {
    $this->data    = $data;
    $this->user    = $user;
    $this->subject = "[OHFA PC] Notifications ";
    $this->owner   = Cache::remember('allita-notifier', 1440, function () {
      return User::whereEmail('noreply@ohiohome.org')->first();
    });
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $owner         = $this->owner;
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

    $body              = \view('emails.bulk_notifications', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2', 'notifications'));
    $email_saved_in_db = new HistoricEmail([
      "user_id" => $user->id,
      "type"    => 'bulk-notifications',
      "type_id" => $owner ? $owner->id : null,
      "subject" => $this->subject,
      "body"    => $body,
    ]);
    $email_saved_in_db->save();
    return $this->view('emails.bulk_notifications', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2', 'notifications'));
  }
}

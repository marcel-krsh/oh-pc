<?php

namespace App\Mail;

use App\Models\HistoricEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailCreateNewUser
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailCreateNewUser extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * @var mixed $owner
   */
  public $owner;

  /**
   * @var [type]
   */
  public $new_user;

  /*
   * EmailCreateNewUser constructor.
   *
   * @param \App\User $owner
   */
  public function __construct(User $owner, User $new_user)
  {
    $this->owner    = $owner;
    $this->new_user = $new_user;
    $this->subject  = "New user registration";
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $owner       = $this->owner;
    $new_user    = $this->new_user;
    $greeting    = "Hello " . $new_user->name .',';
    $action_text = "COMPLETE REGISTRATION";
    $action_url  = secure_url('/user/complete-registration') . "/" . $new_user->id . "?t=" . $new_user->email_token;
    $level       = "success";
    $level2      = "error";
    $introLines[] = "You have been added to Allita system. Use below link to complete your registration process";
    $introLines[] = 'Login with Email: ' . $new_user->email;
    $outroLines = [];
    // save in database
    if ($owner) {
      $body              = \view('emails.create_new_user', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
      $email_saved_in_db = new HistoricEmail([
        "user_id" => $new_user->id,
        "type"    => 'users',
        "type_id" => $owner->id,
        "subject" => $this->subject,
        "body"    => $body,
      ]);
      $email_saved_in_db->save();
    }
    return $this->view('emails.create_new_user', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2', 'email_saved_in_db'));
  }
}

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
class EmailVerificationCode extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * @var mixed $owner
   */
  public $owner;

  protected $code;


  /*
   * EmailCreateNewUser constructor.
   *
   * @param \App\User $owner
   */
  public function __construct(User $owner, $code)
  {
    $this->owner    = $owner;
    $this->subject  = "DEVCO Inspection Verification Code";
    $this->code = $code;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $owner       = $this->owner;
    $new_user    = $this->owner;
    $greeting    = "Hello,";
    $action_text = "";
    $action_url  = '';
    $level       = "success";
    $level2      = "error";
    $introLines[] = "Your verification code: " . $this->code;
    $introLines[] = 'This code expires in 15 minutes.';
    $outroLines = [];
    // save in database
    if ($owner) {
      $body              = \view('emails.send_verification_code', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2'));
      $email_saved_in_db = new HistoricEmail([
        "user_id" => $new_user->id,
        "type"    => 'users',
        "type_id" => $owner->id,
        "subject" => $this->subject,
        "body"    => $body,
      ]);
      //$email_saved_in_db->save();
    }

    return $this->view('emails.send_verification_code', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'level2'));
  }
}

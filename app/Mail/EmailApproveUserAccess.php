<?php

namespace App\Mail;

use App\Models\HistoricEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailCreateNewUser.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailApproveUserAccess extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed
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
        $this->owner = $owner;
        $this->new_user = $new_user;
        $this->subject = 'Request User Access';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $owner = $this->owner;
        $new_user = $this->new_user;
        $greeting = 'Hello '.$owner->name.',';
        $action_text = 'PROVIDE USER ACCESS';
        $action_url = secure_url('/user/approve-access').'/'.$new_user->id;
        $level = 'success';
        $level2 = 'error';
        $introLines[] = 'A user has been added to the Allita Program Compliance system, and they are requesting access. Please use the link below to assign a role to them and provide login access.';
        $introLines[] = 'User Name: <span class="mail-masthead_name">'.$new_user->name.'</span>';
        $introLines[] = 'User Email: <span class="mail-masthead_name">'.$new_user->email.'</span>';
        $introLines[] = 'User Created On: <span class="mail-masthead_name">'.$new_user->created_at.'</span>';
        $outroLines = [];
        // save in database
        if ($owner) {
            $body = \view('emails.request-request-access', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new HistoricEmail([
        'user_id' => $owner->id,
        'type'    => 'users',
        'type_id' => $new_user->id,
        'subject' => $this->subject,
        'body'    => $body,
      ]);
            $email_saved_in_db->save();
        }

        return $this->view('emails.request-request-access', compact('greeting', 'introLines', 'action_url', 'action_text', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2', 'email_saved_in_db'));
    }
}

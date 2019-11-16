<?php

namespace App\Mail;

use App\HistoricEmail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailActivation.
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailActivation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed
     */
    public $owner;

    /*
     * EmailActivation constructor.
     *
     * @param \App\User $owner
     */
    public function __construct(User $owner)
    {
        $this->user = $owner;
        $this->subject = 'Activate New Program Member';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $owner = User::find(session('ownerId'));
        $activateUser = User::find(session('newUserId'));
        $greeting = 'A New User Has Registered';

        $ownerFirstNameEnd = strpos($owner->name, ' ');
        $ownerFirstName = substr($owner->name, 0, $ownerFirstNameEnd);
        $introLines[] = 'Congratulations '.ucwords($ownerFirstName).',';
        $introLines[] = ucwords($activateUser->name)." has registered to be a member of your organization using email address $activateUser->email. Please use the button below to activate their membership.";
        $introLines[] = 'If this indvidual is NOT a member, click on the second button to delete the user.';

        $actionText = 'ACTIVATE '.strtoupper($activateUser->name);
        $actionUrl = secure_url('/user/quick_activate').'/'.$activateUser->id.'?t='.$activateUser->email_token;
        $actionText2 = 'DELETE '.strtoupper($activateUser->name);
        $actionUrl2 = secure_url('/user/quick_delete').'/'.$activateUser->id.'?t='.$activateUser->email_token;
        $level = 'success';
        $level2 = 'error';
        $outroLines = [];

        //clear session vars.
        session(['ownerId'=>'', 'newUserId' => '']);

        // save in database
        if ($owner) {
            $body = \view('vendor.notifications.email', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                'user_id' => $owner->id,
                'type' => 'users',
                'type_id' => $owner->id,
                'subject' => $this->subject,
                'body' => $body,
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('vendor.notifications.email', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

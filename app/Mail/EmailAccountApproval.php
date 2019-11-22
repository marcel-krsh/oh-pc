<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \DB;
use App\HistoricEmail;

/**
 * EmailAccountApproval
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailAccountApproval extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \App\User User
     */
    public $user;

    /**
     * EmailAccountApproval constructor.
     *
     * @param \App\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->subject = "Your Account Request Has Been Approved";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = User::find(session('userId'));
        $organization = DB::table('entities')->select('*')->where('id', $user->entity_id)->first();
        $orgOwner = User::find($organization->owner_id);
        if ($user->validate_all == 1) {
            $greeting = "Your request to create ".$organization->entity_name." and its respective program has been approved!";
            $userFirstNameEnd = strpos($user->name, " ");
            $userFirstName = substr($user->name, 0, $userFirstNameEnd);
            $introLines[] = "Congratulations ".ucwords($userFirstName).",";
            $introLines[] = "You now have access to me, Allita, using your email address $user->email and the pasword you entered previously.";
            $introLines[] = "You can now ask members of your team to register as well! Send them to the registration page and have them select you from the list of available programs. As the owner of the account, you will receive email notifications with activation links at $user->email. Upon activating them, you will then need to assign them a role, or roles, within the system based on their responsibilities.";
        } else {
            $greeting = "Your request to join ".$organization->entity_name." has been approved!";
            $userFirstNameEnd = strpos($user->name, " ");
            $userFirstName = substr($user->name, 0, $userFirstNameEnd);
            $introLines[] = "Congratulations ".ucwords($userFirstName).",";
            $introLines[] = "You now have access to me, Allita, using your email address $user->email and the pasword you entered previously.";
            $introLines[] = "If for some reason you still do not have access to the site - contact your adminstrator ".ucwords($orgOwner->name)." via email at $orgOwner->email and ask to be assigned a role within the system.";
        }

        $actionText = "LOG IN!";
        $actionUrl = secure_url('/');
        $level = "";
        $outroLines = ["I'm looking forward to working with you!"];

        //clear session vars.
        session(['userId'=>""]);

        // save in database
        if ($user) {
            $body = \view('vendor.notifications.email', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines'));
            $email_saved_in_db = new  HistoricEmail([
                "user_id" => $user->id,
                "type" => 'users',
                "type_id" => $user->id,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('vendor.notifications.email', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines'));
    }
}

<?php

namespace App\Mail;

use App\User;
use App\Entity;
use App\Program;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use \DB;
use App\HistoricEmail;

/**
 * EmailEntityActivation
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailEntityActivation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed $user
     */
    public $user;

    /**
     * EmailEntityActivation constructor.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $user = User::find($user);
        $this->user = $user;
        $this->subject = "A New Entity, Program, And User Request";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $activateUser = User::find(session('newUserId'));
        $activateEntity = Entity::find(session('newEntityId'));
        $activateProgram = Program::find(session('newProgramId'));
        $approver = User::find(session('approverId'));

        $greeting = "There's a new landbank in town!";
       
        $approverFirstNameEnd = strpos($approver->name, " ");
        $approverFirstName = substr($approver->name, 0, $approverFirstNameEnd);
        $introLines[] = "Congratulations ".ucwords($approverFirstName).",";
        $introLines[] = ucwords($activateUser->name)." has registered to be the manager of a new entity called ".ucwords($activateEntity->entity_name)." with a program named ".ucwords($activateProgram->program_name)." using email address $activateUser->email. Please use the button below to activate their membership.";
        $introLines[] = "If this new group SHOULD NOT be a member, click on the second button to delete the user, their entity, and their program.";
        
        $actionText = "ACTIVATE ".strtoupper($activateUser->name);
        $actionUrl = secure_url('/user/quick_activate')."/".$activateUser->id."?t=".$activateUser->email_token;
        $actionText2 = "DELETE ".strtoupper($activateUser->name);
        $actionUrl2 = secure_url('/user/quick_delete')."/".$activateUser->id."?t=".$activateUser->email_token;
        $level = "success";
        $level2 = "error";
        $outroLines = [];

        //clear session vars.
        session(['approverId'=>""]);

        // save in database
        if ($user) {
            $body = \view('vendor.notifications.email', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
            $email_saved_in_db = new  HistoricEmail([
                "user_id" => $user->id,
                "type" => 'users',
                "type_id" => $user->id,
                "subject" => $this->subject,
                "body" => $body
            ]);
            $email_saved_in_db->save();
        }

        return $this->view('vendor.notifications.email', compact('greeting', 'introLines', 'actionUrl', 'actionText', 'level', 'outroLines', 'actionText2', 'actionUrl2', 'level2'));
    }
}

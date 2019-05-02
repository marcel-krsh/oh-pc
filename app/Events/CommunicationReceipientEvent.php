<?php

namespace App\Events;

use App\Jobs\SendNotificationEmail;
use App\Mail\EmailCommunicationNotification;
use App\Models\CommunicationRecipient;
use App\Models\NotificationsTriggered;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommunicationReceipientEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct(Mailer $mailer)
  {
    $this->mailer = $mailer;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function communicationCreated(CommunicationRecipient $cr)
  {
    try {
      $cr_details = CommunicationRecipient::with('communication.owner', 'user.notification_preference')->find($cr->id);
      //insert data into notifications_triggered table
      if ($cr_details->communication) {
        $np                 = $cr_details->user->notification_preference;
        $communication      = $cr_details->communication;
        $owner              = $cr_details->communication->owner;
        $user               = $cr_details->user;
        $token              = generateToken();
        $nt                 = new NotificationsTriggered;
        $nt->from_id        = $owner->id;
        $nt->to_id          = $cr_details->user_id;
        $nt->type_id        = 1;
        $nt->token          = $token;
        $email_notification = new EmailCommunicationNotification($cr_details, $token);
        $nt->deliver_time   = '2019-05-02 18:00:00'; //notificationDeliverTime();
        $nt->data           = $this->buildData($communication, $owner, $user);
        $nt->save();
        if ($np) {
          //1 -> Immediately, 2 -> Hourley, 3->Daily
          if (1 == $np->frequency) {
            $email_notification = new EmailCommunicationNotification($cr_details, $token);
            $nt->deliver_time   = Carbon::now();
            $nt->data           = $this->buildData($communication, $owner, $user);
            $nt->active         = 0; //since this is immediate notification
            $nt->save();
            $queued_job = dispatch(new SendNotificationEmail($user, $email_notification));
          } elseif (2 == $np->frequency) {
            $nt->deliver_time = closestNextHour();
            $nt->data         = $this->buildData($communication, $owner, $user);
            $nt->save();
          } elseif (3 == $np->frequency) {
            $nt->deliver_time = notificationDeliverTime();
            $nt->data         = $this->buildData($communication, $owner, $user);
            $nt->save();
          }
        } else {
          $nt->deliver_time = closestNextHour();
          $nt->data         = $this->buildData($communication, $owner, $user);
          $nt->save();
        }
      }
    } catch (\Exception $e) {
      $data_insert_error = $e->getMessage();
    }
  }

  protected function buildData($communication, $owner, $user)
  {
    $data             = [];
    $data['type_id']  = 1;
    $data['heading']  = 'New Message: ' . $communication->subject;
    $data['base_url'] = secure_url('/communication/view-message', $communication->id) . "/" . $user->id . "?t=";
    $data['message']  = $communication->message;
    return $data;
  }
}

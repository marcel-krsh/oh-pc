<?php

namespace App\Events;

use App\Jobs\SendNotificationEmail;
use App\Mail\EmailCommunicationNotification;
use App\Models\CommunicationRecipient;
use App\Models\NotificationsTriggered;
use Config;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CommunicationReceipientEvent
{

  use Dispatchable, InteractsWithSockets, SerializesModels;

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
      $cr_details = CommunicationRecipient::with('communication.owner', 'user.notification_preference', 'user.person')->find($cr->id);
      //insert data into notifications_triggered table
      //based on type ID set the model and model_ID, should be set in session
      if ($cr_details->communication && ($cr_details->communication->owner_id != $cr_details->user_id)) {
        $config               = config('allita.notification');
        $np                   = $cr_details->user->notification_preference;
        $communication        = $cr_details->communication;
        $owner                = $cr_details->communication->owner;
        $user                 = $cr_details->user;
        $token                = generateToken();
        $nt                   = new NotificationsTriggered;
        $nt->from_id          = $owner->id;
        $nt->to_id            = $cr_details->user_id;
        $nt->communication_id = $communication->id;
        $model_id             = $cr_details->id;
        $type_id              = 1;
        if (session()->has('notification_triggered_type')) {
          $type_id  = session()->get('notification_triggered_type');
          $model_id = session()->get('notification_model_id');
        }
        Log::info($model_id);
        // Log::info(session()->get('notification_model_id'));
        $nt->type_id = $type_id;
        $nt->token   = $token;
        if (2 == $type_id) {
          $nt->model    = $config['models'][$type_id];
          $nt->model_id = $model_id;
        } else {
          $nt->model    = get_class($cr_details);
          $nt->model_id = $model_id;
        }
        //Build email
        //$email_notification = new EmailCommunicationNotification($cr_details, $token);
        //Check if user has notification preference
        if ($np) {
          //1 -> Immediately, 2 -> Hourley, 3->Daily
          if (1 == $np->frequency) {
            $nt->deliver_time = date("Y-m-d H:i:s"); //Carbon::now();
            $nt->data         = $this->buildData($communication, $owner, $user, $type_id, $model_id);
            $nt->active       = 0; //since this is immediate notification
            $nt->sent_at      = $nt->deliver_time;
            $nt->sent_count   = 1;
            $nt->save();

            $email_notification = new EmailCommunicationNotification($nt);
            $queued_job         = dispatch(new SendNotificationEmail($user, $email_notification));
          } elseif (3 == $np->frequency) {
            $nt->sent_at      = $nt->deliver_time;
            $nt->sent_count   = 1;
            $nt->deliver_time = notificationDeliverTime($np->deliver_time);
            $nt->data         = $this->buildData($communication, $owner, $user, $type_id, $model_id);
            $nt->save();
          }
        } else {
          // If user has hourly or no notification preference, notifications are sent every hour
          $nt->deliver_time = closestNextHour();
          $nt->sent_at      = $nt->deliver_time;
          $nt->sent_count   = 1;
          $nt->data         = $this->buildData($communication, $owner, $user, $type_id, $model_id);
          $nt->save();
        }
      }
    } catch (\Exception $e) {
      $data_insert_error = $e->getMessage();
    }
  }

  protected function buildData($communication, $owner, $user, $type_id, $model_id = null)
  {
    $data            = [];
    $data['type_id'] = $type_id;
    $data['heading'] = $communication->subject;
    if (2 == $type_id) {
      $data['base_url'] = url('notifications/report/' . $user->id . '/' . $model_id) . "?t=";
    } else {
      $data['base_url'] = secure_url('notifications/view-message', $user->id) . "/" . $model_id . "?t=";
    }
    // Log::info($data['base_url']);
    $data['message'] = $communication->message;
    return $data;
  }
}

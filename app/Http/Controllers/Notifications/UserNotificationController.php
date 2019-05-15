<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationEmail;
use App\Mail\EmailBulkNotification;
use App\Mail\EmailCommunicationNotification;
use App\Models\CommunicationRecipient;
use App\Models\NotificationsTriggered;
use App\Models\User;
use App\Models\UserNotificationPreferences;
use Auth;
use Config;
use Illuminate\Http\Request;
use Log;

class UserNotificationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth')->except('postResendNotificationsLink');
    if (env('APP_DEBUG_NO_DEVCO') == 'true') {
      //Auth::onceUsingId(286); // TEST BRIAN
      //Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
    }
  }

  public function postNotificationPreference(Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'notification_setting' => 'required',
    ]);
    $user = Auth::user();
    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()->all()]);
    } else {
      $uns = UserNotificationPreferences::where('user_id', $user->id)->first();
      if (!$uns) {
        $uns = new UserNotificationPreferences;
      }
      $uns->deliver_time = null;
      if (3 == $request->notification_setting) {
        if ($request->delivery_time) {
          $uns->deliver_time = $request->delivery_time;
        } else {
          $uns->deliver_time = '17:00:00';
        }
      }
      $uns->user_id   = $user->id;
      $uns->frequency = $request->notification_setting;
      $uns->save();
      return 1;
    }
    return $this->extraCheckErrors($validator);
  }

  public function communicationNotifications(Request $request)
  {
    /**
     * For new communication or a reply, the following information is created
     * New enrty in Communication
     * New entry in CommunicationRecepient
     * If Documents are attached, new entry into CommunicationDocument
     *
     */
    /**
     * CREATE A QUEUE TO SEND EMAIL/SMS/CALL
     * This queue table should work for all types of notifications
     *   So store message data as Json
     *   Have a subject
     *   Time when to deliver(Listening to Queues)
     *   How to tackle immediate emails (Recommends still should go into queue, but dispatch immediately)
     *     Immediate tasks can be handled through queues -
     *     like each email would have only one type of communication and a link that works for 24 hrs?
     *   Maybe have notification type
     *   Delayed Notification Handling!
     *     Still through Queues? Fetch the current queue and append data? ::::XXXX
     * Figureout what and how to do with documents!
     * Should we include all these in historic_emails? AB
     *
     */
    /**
     * Implementation Plan
     * -Store notifications to a table, notifications_triggered
     *   Do a scheduled task every minute?/1 hour? like 56th minute
     *     Fetch the notifications from notifications_triggered table grouped by user_id
     *     then combine those into single email, in blade
     *
     *BETTER DO IN SEPERATE PROJECT anc integrate in allita here!
     */
    Log::info("Request Cycle with Queues Begins");
    // $this->dispatch(new SendNotificationEmail());
    // $this->dispatch((new SendNotificationEmail())->delay(60 * 5));
    Log::info("Request Cycle with Queues Ends");
  }

  protected function extraCheckErrors($validator)
  {
    $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Technical Team');
    return response()->json(['errors' => $validator->errors()->all()]);
  }

  public function postResendNotificationsLink(Request $request)
  {
    /**
     * check if the requested link belongs to single notification or a batch notification
     * Single notification
     *   Send single link again, update that in notifications_triggered
     * Bulk notifications
     *   Get all the notifications linked to that token,
     *     Send bulk emails and update in notifications_triggered
     */
    $token         = session()->get('notification_token');
    $notifications = NotificationsTriggered::where('token', $token)->with('to_user')->inactive()->get();
    if ($notifications) {
      $first_of_list = $notifications->first();
      $request_count = NotificationsTriggered::where('to_id', $first_of_list->to_id)
        ->where('model_id', $first_of_list->model_id)
        ->get()
        ->count();
    }
    //return get_class($notifications->first());
    //CommunicationReceipientEvent
    $token = generateToken();
    if (count($notifications) == 1) {
      //single notifiation, consider type_id
      //EmailCommunicationNotification
      $notification = $notifications->first();
      $cr_details   = CommunicationRecipient::with('communication.owner', 'user.notification_preference', 'user.person')->find($notification->model_id);
      $nt           = $this->saveNotificationTrigger($notification, $token, $request_count);
      if (1 == $nt->type_id || 2 == $nt->type_id) {
        $email_notification = new EmailCommunicationNotification($nt);
        $user               = $nt->to_user;
        $queued_job         = dispatch(new SendNotificationEmail($user, $email_notification));
      }
      return 1;
    } elseif (count($notifications) > 1) {
      // bulk notification
      // SendNotificationsHourly
      // EmailBulkNotification
      // $hourley_notifications = NotificationsTriggered::whereBetween('deliver_time', [$from, $to])
      //   ->with('to_user.person', 'from_user')
      //   ->active()
      //   ->get()
      //   ->groupBy('to_id');
      $config             = config('allita.notification');
      $user               = $notifications->first()->to_user;
      $user_notifications = $notifications->sortBy('updated_at')->groupBy('type_id');
      $data               = [];
      foreach ($user_notifications as $key => $notification_types) {
        // if (1 == $notification_types->first()->type_id) {
        $data[$key]['notification_type'] = $config['type'][$notification_types->first()->type_id];
        foreach ($notification_types as $noti_key => $notification) {
          $data[$key]['type'][$noti_key]['heading'] = $notification->data['heading'];
          $data[$key]['type'][$noti_key]['link']    = $notification->data['base_url'] . $token;
          $data[$key]['type'][$noti_key]['message'] = $notification->data['message'];
          $data[$key]['type'][$noti_key]['from']    = $notification->from_user->name;
          $data[$key]['type'][$noti_key]['time']    = $notification->created_at;
          $nt                                       = $this->saveNotificationTrigger($notification, $token, $request_count);
        }
        //}
      }
      $email_notification = new EmailBulkNotification($data, $user);
      $queued_job         = dispatch(new SendNotificationEmail($user, $email_notification));
      return 1;
    } else {
      return 'Notification not found';
    }
    return 1;
  }

  protected function saveNotificationTrigger($notification, $token, $request_count)
  {
    $nt               = $notification->replicate();
    $nt->deliver_time = date("Y-m-d H:i:s"); //Carbon::now();
    $nt->active       = 0; //since this is immediate notification
    $nt->sent_at      = $nt->deliver_time;
    $nt->token        = $token;
    $nt->sent_count   = $request_count + 1;
    $nt->save();
    return $nt;
  }
}

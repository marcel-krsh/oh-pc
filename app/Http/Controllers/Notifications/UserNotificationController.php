<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotificationPreferences;
use Auth;
use Illuminate\Http\Request;
use Log;

class UserNotificationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
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
}

<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotificationPreferences;
use Auth;
use Illuminate\Http\Request;

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

  protected function extraCheckErrors($validator)
  {
    $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Technical Team');
    return response()->json(['errors' => $validator->errors()->all()]);
  }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommunicationRecipient;
use Carbon\Carbon;
use Event;

class AdminfixController extends Controller
{
  /**
   * Created to fix the non-triggered notifications from 12-01-2019 to NOW. Recommended to run once
   * @return [type] [description]
   */
  public function communicationNotifications()
  {
    return 'Already ran';

    if (!session()->has('ran-notification')) {
      //get all the messages sent to receipients with communication and user, which are not seen
      $receipients = CommunicationRecipient::with('communication', 'user')->whereBetween('created_at', ['2019-12-01 00:00:00', Carbon::now()])->where('seen', '<>', 1)->get();
      foreach ($receipients as $key => $receipient) {
        //Check if the notification was already triggered for this message - HOW!!!
        Event::dispatch('communication.created', $receipient);
        // event(new CommunicationReceipientEvent($receipient->id));
      }
      session(['ran-notification' => 1]);
      return 'Processed, DONOT RUN AGAIN';
    } else {
      return 'Already ran';
    }
  }
}

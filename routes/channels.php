<?php

use App\Models\Communication;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return true; // return (int) $user->id === (int) $id;
// });

// Broadcast::channel('message.{messageId}', function ($user, $messageId) {
//     return true; // return $user->id === Communication::findOrNew($messageId)->owner_id;
// });

Broadcast::channel('communications.{uid}.{sid}', function ($user, $uid, $sid) {
	//dd($user,$uid,$sid);
  return true; // return Auth::check();
});

// Broadcast::channel('chat.{uid}.{sid}', function ($user, $uid, $sid) {
//   return true; // return Auth::check();
// });

// Broadcast::channel('chat', function () {
//   return true; // return Auth::check();
// });

<?php
use App\Models\User;
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

Broadcast::channel('communications.{id}', function ($user, $id) {
	//dd($user,$uid,$sid);
  	//return ['id'=>$user->id];
  	//return (int) $user->id === (int) $id;
	return true;
});


Broadcast::channel('audits', function ($user) {
	//dd($user,$uid,$sid);
  	return ['name'=>$user->name];
});

Broadcast::channel('chat',function($user){
	return ['name'=>$user->name];
});

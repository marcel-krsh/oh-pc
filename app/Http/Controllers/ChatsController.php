<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class ChatsController extends Controller
{
    //
    public function __construct()
	{
	  if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            Auth::onceUsingId(286); // TEST BRIAN
      }else{
      	$this->middleware('web');
      }
	}

	/**
	 * Show chats
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
	  return view('layouts.chat');
	}

	/**
	 * Fetch all messages
	 *
	 * @return Message
	 */
	public function fetchMessages()
	{
	  return Message::with('user')->get();
	}

	/**
	 * Persist message to database
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function sendMessage(Request $request)
	{
	  $user = Auth::user();

	  $message = $user->messages()->create([
	    'message' => $request->input('message')
	  ]);

	  // broadcast(new MessageSent($user, $message))->toOthers();
	  broadcast(new MessageSent($user, $message))->toOthers();


	  return ['status' => 'Message Sent!'];
	}
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;
use Carbon;

use Illuminate\Support\Facades\Redis;

class DataController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        if(env('APP_DEBUG_NO_DEVCO') == 'true'){
    	   Auth::onceUsingId(286); // TEST BRIAN
        }
    }

    public function testSockets(){

        // 1. publish event using redis
        // 
        // 2. Node.js + redis subscribe to the event
        // 
        // 3. Use Socket.io to emit to all clients
        // 
        
        $data = [
            'event' => 'UserSignedUp',
            'data' => [
                'username' => 'JohnDoe2'
            ]
        ];

        Redis::publish('test-channel', json_encode($data));

        // $user = Redis::get('user:bob');

    	return view('welcome');
    }

}
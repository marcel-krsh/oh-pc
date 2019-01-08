<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Session;
use App\LogConverter;
use Carbon;

class DataController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            Auth::onceUsingId(286); // TEST BRIAN
        }
    }

    public function autosave(Request $request) {

        // we get two things: a reference to know what to update and its new value
        $ref = $request->get('ref');
        $data = $request->get('data');
        $user = Auth::user();

        switch ($ref) {
            case "auditor.availability_max_hours":
                $user->availability_max_hours = $data;
                $user->save();             
                return 1; break;
            case "auditor.availability_lunch":
                $user->availability_lunch = $data;
                $user->save();             
                return 1; break;
            case "auditor.availability_max_driving":
                $user->availability_max_driving = $data;
                $user->save();             
                return 1; break;

            default:
               return "There was a problem with your request.";
        }
    }

    // public function testSockets()
    // {

    //     // 1. publish event using redis
    //     //
    //     // 2. Node.js + redis subscribe to the event
    //     //
    //     // 3. Use Socket.io to emit to all clients
    //     //
        
    //     $data = [
    //         'event' => 'UserSignedUp',
    //         'data' => [
    //             'username' => 'JohnDoe2'
    //         ]
    //     ];

    //     Redis::publish('test-channel', json_encode($data));

    //     // $user = Redis::get('user:bob');

    //     return view('welcome');
    // }
}

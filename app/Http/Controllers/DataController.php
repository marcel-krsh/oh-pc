<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CachedAudit;
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
            //Auth::onceUsingId(286); // TEST BRIAN
            Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
        }
    }

    public function autosave(Request $request) {

        // we get two things: a reference to know what to update and its new value
        $ref = $request->get('ref');
        $data = $request->get('data');
        if(Auth::user()->admin_access() && !is_null($request->get('user_id'))) {
            $user = \App\User::find($request->get('user_id'));
        } else {
            $user = Auth::user();
        }

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

            case "auditor.allowed_tablet":
                if(Auth::user()->admin_access()){
                 $user->allowed_tablet = $data;
                 $user->save();
                 return 'I stored this '.$data.' on user '.$user->id; break;
                } else {
                    return 'Sorry, you must be an admin to adjust tablet access for users.';
                }


            default:
               return "There was a problem with your request.";
        }
    }

    public function removeSession($type, $value = null)
    {
    	if (null !== $value) {
        session([$type => $value]);
        $new_filter = session($type);
        return $new_filter;
      } else {
        if (!session()->has($type)) {
          if ('' != $value) {
            session([$type => 1]);
          } else {
            session([$type => '']);
          }
        } else {
          if (session($type) == 0 || session($type) === null) {
            if ('' != $value) {
              session([$type => 1]);
            } else {
              session([$type => '']);
            }
          } else {
            session()->forget($type);
          }
        }
        return 1;
      }
    }

    public function setSessionNew(Request $request, $name=null, $value=null){

        // we can pass an array if needed [ [name,val],[name,val] ]
      if($request->has('data')){
          $names = $request->get('data');
          $selected = $request->get('selected');

          if(is_array($names)){
              foreach($names as $n){
                  Session::put($n[0], $n[1]);
              }
          }
          if(is_array($selected)) {
          	foreach($selected as $n){
                Session::put($n[0], $n[1]);
            }
            return 1;
          }
      }
    }

    public function setSession(Request $request, $name=null, $value=null){

        // we can pass an array if needed [ [name,val],[name,val] ]
        if($request->has('data')){
            $names = $request->get('data');

            if(is_array($names)){
                foreach($names as $n){
                    Session::put($n[0], $n[1]);
                }
                return 1;
            }else{
                return 0;
            }
        }else{
            // check if it is a project audit selection project.id.selectedaudit
            $name_check = explode('.', $name);
            if($name_check[0] == "project" && $name_check[2] == "selectedaudit"){
                $project_id = $name_check[1];
                $audit = CachedAudit::where('id', '=', $value)->where('project_id','=',$project_id)->first();
                Session::put($name, $value);
                return Session::get($name);
            }else{
                Session::put($name, $value);
                return Session::get($name);
            }
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

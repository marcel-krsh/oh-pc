<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Auth;
use Session;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Mail\EmailFailedLogin;
use App\Models\SystemSetting;
use App\Models\User;

class AllitaAuth
{
    /**
     * AuthService
     * @var
     */
    private $_auth_service;

    /**
     * DevcoService
     * @var
     */
    private $_devco_service;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->_auth_service = new AuthService;
        $this->_devco_service = new DevcoService;

        // make sure we have access and refresh tokens
        // $pcapi_refresh_token = SystemSetting::get('devco_refresh_token'); 
        // $pcapi_access_token = SystemSetting::get('devco_access_token'); 

        // if($pcapi_refresh_token === null || $pcapi_access_token === null){
        //     $this->_auth_service->rootAuthenticate();
        //     $this->_auth_service->reloadTokens();
        // }

        // how do we know if the access_token needs to be replaced?

         $this->authenticate($request);
        // $this->checkDevcoSession($request);

        // temporary solution
        if($request->has('user_id')){
            Auth::loginUsingId($request->get('user_id'));
        } 

        return $next($request);
    }

    /**
     * Checks and refreshes Devco session
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return 
     */
    public function checkDevcoSession($request)
    {
        // how long has it been? 
        // over 15 min -> refresh session in Devco, save new timestamp in our db

        // $service = new AuthService;
        // $service->rootRefreshToken();
    }

    /**
     * Authenticate user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return 
     */
    public function authenticate($request)
    {

        if(!Auth::check()){
  
            $credentials = $request->only('user_id', 'token');
            $ip = $request->ip();
            $user_agent = $request->header('User-Agent');
dd($credentials);
            // keep track of tries
            // $auth_tracker = AuthTracker::where('ip','=',$ip)->where('user_id','=',$request->get('user_id'))->first();
            // if(!$auth_tracker){
            //     // maybe same IP, different user_id
            //     $auth_tracker = AuthTracker::is_blocked_by_ip($ip);

            //     if($auth_tracker) {
            //         $auth_tracker->incrementTries();
            //     }else{
            //         $auth_tracker = new AuthTracker([
            //                         'token' => $request->get('token'),
            //                         'ip' => $ip,
            //                         'user_agent' => $user_agent,
            //                         'user_id' => $request->get('user_id'),
            //                         'tries' => 1,
            //                         'blocked_until' => null
            //                     ]);
            //     }
            // }

            if(!$request->has('user_id')){
                // if($auth_tracker){
                //     $auth_tracker->incrementTries();
                // }
                throw new AuthenticationException('Unauthenticated 119. Missing user id.');
            } 

            if(!$request->has('token')){
                // if($auth_tracker){
                //     $auth_tracker->incrementTries();
                // }
                throw new AuthenticationException('Unauthenticated 119. Missing token.');
            } 

            // we have user_id and token, check credentials with Devco
             $check_credentials = $this->_auth_service->userAuthenticateToken($request->get('user_id'), $request->get('token'), $ip, $user_agent);
            
            //dd($check_credentials);
            if(!$check_credentials->data->attributes->{'authenticated'} || !$check_credentials->data->attributes->{'user-activated'} || !$check_credentials->data->attributes->{'user-exists'}){

                // if($auth_tracker){
                //     $auth_tracker->incrementTries();
                // }
                throw new AuthenticationException('Unauthentic  ated 130.');

            }


            // if($auth_tracker){
            //     $auth_tracker->resetTries();
            // }

            // we got a real user, check if that user is in our system
            $user_key = $check_credentials->included[0]->attributes->{'user-key'};
            $email = $check_credentials->included[0]->attributes->{'email'};
            $first_name = $check_credentials->included[0]->attributes->{'first-name'};
            $last_name = $check_credentials->included[0]->attributes->{'last-name'};

            $user = User::where('devco_key', '=', $user_key)->first();

            if(!$user){
                $user = new User([
                    'devco_key' => $user_key,
                    'name' => $first_name." ".$last_name,
                    'email' => $email,
                    'active' => 1
                ]);
            }          
dd($user);
            Auth::loginUsingId($user->id);  

        }else{

            // user is already logged in
            $user = Auth::user();

            // make sure the user corresponds to the Devco user


            // 
        }
        
        // login user by user id
        //Auth::loginUsingId(1);
    }

//throw new HttpException(503);
}

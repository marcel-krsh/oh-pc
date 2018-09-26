<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;
use Auth;
use Session;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Mail\EmailFailedLogin;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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


    protected $auth;

    public function __construct(Guard $auth){

        $this->auth = $auth;

    }
    public function handle($request, Closure $next)
    {
        // Do they have an active session?


        //if(!$request->user()){
            $this->_auth_service = new AuthService;
            $this->_devco_service = new DevcoService;

            

             $this->authenticate($request);

            // temporary solution
            // if($request->has('user_id')){
            //     Auth::loginUsingId($request->get('user_id'));
            // } 
       // }

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
        
    }

    /**
     * Authenticate user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return 
     */
    public function authenticate($request)
    {

        if(!$request->user()){
            
            $name = $this->auth->getRecallerName();
            if($name){
                // make sure life span of cookie to 20 minutes...

                $rememberMeCookieValue = $request->cookie($name);
                $encryptor = app(\Illuminate\Contracts\Encryption\Encrypter::class);

                $rememberMeCookieValue = $encryptor->decrypt($rememberMeCookieValue,false);
                $credentials = explode('|', $rememberMeCookieValue);
                if(is_array($credentials) && count($credentials)>2){
                    $rememberedUser = User::where('id',$credentials[0])->where('remember_token',$credentials[1])->where('password', $credentials[2])->first();
                    if(!is_null($rememberedUser)){
                        $this->auth->loginUsingId($rememberedUser->id,true);
                        $key = auth()->getRecallerName();
                        cookie()->queue($key, $request->cookie($key), 20);
                    } else {
                        // redirect to devco
                        dd('redirect to devco - could not find the user.');
                    }
                } else {
                    $rememberMeCookieValue = $encryptor->decrypt($rememberMeCookieValue,false);
                    dd('cannot find the user info in the explode: '.$rememberMeCookieValue);
                }

            } else {

                $credentials = $request->only('user_id', 'token');
                $ip = $request->ip();
                $user_agent = $request->header('User-Agent');

                //dd($credentials,$ip,$user_agent);

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

                if(!isset($credentials['user_id'])){
                    // if($auth_tracker){
                    //     $auth_tracker->incrementTries();
                    // }
                    //Auth::logout();
                    throw new AuthenticationException('Unauthenticated 111. Missing user id.'.Auth::user());
                } 

                if(!isset($credentials['token'])){
                    // if($auth_tracker){
                    //     $auth_tracker->incrementTries();
                    // }
                    //Auth::logout();
                    throw new AuthenticationException('Unauthenticated 118. Missing token.');
                } 

                // we have user_id and token, check credentials with Devco
                 $check_credentials = $this->_auth_service->userAuthenticateToken($credentials['user_id'], $credentials['token'], $ip, $user_agent);
                
                //dd($check_credentials);
                if(!$check_credentials->data->attributes->{'authenticated'} || !$check_credentials->data->attributes->{'user-activated'} || !$check_credentials->data->attributes->{'user-exists'}){

                    // if($auth_tracker){
                    //     $auth_tracker->incrementTries();
                    // }
                    //Auth::logout();
                    throw new AuthenticationException('Unauthenticated 130.');

                } 

            


                // if($auth_tracker){
                //     $auth_tracker->resetTries();
                // }

                // we got a real user, check if that user is in our system
                $user_key = $devcoCredentials->{'user-key'};
                $email = $devcoCredentials->{'email'};
                $first_name = $devcoCredentials->{'first-name'};
                $last_name = $devcoCredentials->{'last-name'};

                $user = User::where('devco_key', '=', $user_key)->first();

                if(!$user){
                    // no user found - add them to the database
                    $user = new User;
                    $user->devco_key = $user_key;
                    $user->name = $first_name." ".$last_name;
                    $user->email = $email;
                    $user->password = Hash::make(str_random(50));
                    $user->active = 1;
                    $user->save();
                }          

                //Auth::loginUsingId($user->id);  
                Auth::loginUsingId($user->id,true);
                $key = auth()->getRecallerName();
                cookie()->queue($key, cookie()->get($key), 20);
                //dd($user->id,Auth::user(),Auth::check());
            }

        }else{

            // user is already logged in
            // $user = Auth::user();
            //dd('User is logged in already'.$user);
            // make sure the user corresponds to the Devco user
            ///login user by user id
        
            // 
        }
        
        
    }

//throw new HttpException(503);
}

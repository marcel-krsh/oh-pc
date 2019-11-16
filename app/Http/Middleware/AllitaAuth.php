<?php

namespace App\Http\Middleware;

use App\Mail\EmailFailedLogin;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
//use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Services\AuthService;
use App\Services\DevcoService;
use Auth;
use Closure;
use Cookie;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AllitaAuth
{
    /**
     * AuthService.
     * @var
     */
    private $_auth_service;

    /**
     * DevcoService.
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

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        // Do they have an active session?

        //if(!$request->user()){
        if (env('APP_DEBUG_NO_DEVCO') != 'true') { // allows for local testing
                if (Auth::check()) {
                    return $next($request);
                }
            if (! local()) {
                $this->_auth_service = new AuthService;
                $this->_devco_service = new DevcoService;
            }
            $this->authenticate($request);
        } else {
            $this->auth->loginUsingId(env('USER_ID_IMPERSONATION'));
        }

        // temporary solution
        // if($request->has('user_id')){
        //     Auth::loginUsingId($request->get('user_id'));
        // }
        // }

        return $next($request);
    }

    /**
     * Checks and refreshes Devco session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return
     */
    public function checkDevcoSession($request)
    {
    }

    /**
     * Authenticate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return
     */
    public function authenticate($request)
    {

        /// Set Auth Check Variables:
        $maxLoginTries = config('allita.api.max_login_tries');
        $blockOutTimeFactor = config('allita.api.block_out_time_factor');
        $failedLoginAttempt = false;
        $blockAccess = false;
        $maxUnlockTries = config('allita.api.max_unlock_tries'); // update to be a system setting.
        $rememberMeSessionLength = config('allita.api.remember_me_session_length');
        $rememberMeCookieValueDecrypted = '';
        $blockUnlock = false;
        $thisIp = $request->ip();
        $thisAgent = $request->header('User-Agent');
        $explodedCredentials = false;
        $invalidCookie = false;
        $user = false;
        $userActive = false;
        $addUser = false;
        $checkUser = false;
        $device = false;
        $deviceCheck = true;
        $twoFactorConfirmed = true; // set to true if you want to bypass twofactor
        $failedLoginReason = 'No Credentials Provided';
        $devcoLoginUrl = config('allita.api.devco_login_url');

        ////////////////////////////////////////////////////////
        ///// Check if this ip is currently blocked
        ////
        $currentlyBlocked = AuthTracker::where('ip', '=', $thisIp)->first(); // we track each ip individually

        if (! is_null($currentlyBlocked) && $currentlyBlocked->blocked_until > time()) {
            // this ip is currently being blocked
            $blockAccess = true;
        }

        ////////////////////////////////////////////////////////
        /// check if there is a remember me token
        ///

        $name = $this->auth->getRecallerName();
        if ($name) {
            $rememberMeCookieValue = Cookie::get($name);
            /// check if token is for remembering user:
            if (! is_null($rememberMeCookieValue) && strlen($rememberMeCookieValue) > 10) {
                //dd($rememberMeCookieValue);
                $encryptor = app(\Illuminate\Contracts\Encryption\Encrypter::class);
                try {
                    $rememberMeCookieValueDecrypted = $encryptor->decrypt($rememberMeCookieValue, false);
                } catch (DecryptException $e) {
                    $invalidCookie = true;
                    $failedLoginAttempt = true;
                    $failedLoginReason = 'Invalid Cookie Used:'.$e;
                }

                $credentials = explode('|', $rememberMeCookieValueDecrypted);

                // make sure this is not double encrypted:
                if (count($credentials) > 2) {
                    $explodedCredentials = true;
                } elseif (is_string($rememberMeCookieValueDecrypted) && strlen($rememberMeCookieValueDecrypted) > 10 && $invalidCookie == false) {
                    /// cookie may be double encrypted - decrypt again.
                    //dd('oops');
                    try {
                        $rememberMeCookieValueDecrypted = $encryptor->decrypt($rememberMeCookieValueDecrypted, false);
                    } catch (DecryptException $e) {
                        $invalidCookie = true;
                        $failedLoginAttempt = true;
                        $failedLoginReason = 'Invalid Cookie Used:'.$e;
                    }
                    $credentials = explode('|', $rememberMeCookieValueDecrypted);
                    if (count($credentials) > 2) {
                        $explodedCredentials = true;
                    }
                }

                if ($explodedCredentials) {
                    /// we have credentials - log them in
                    $rememberedUser = User::where('id', $credentials[0])->where('remember_token', $credentials[1])->where('password', $credentials[2])->first();
                    // make sure we found a user:
                    if (! is_null($rememberedUser)) {
                        // make sure user is active and it hasn't been longer than their maximum time since last load.
                        if ($rememberedUser->active == 1 && ($rememberedUser->last_accessed > (time() - env('SESSION_IDLE_TIME', 28800)))) {
                            // user is active - log them in
                            $this->auth->loginUsingId($rememberedUser->id);
                            $rememberedUser->update(['last_accessed'=>time()]);
                            //dd($this->auth);
                            // set userActive and user to be true for final test.
                            $userActive = true;
                            $user = true;
                        } else {
                            // incorrect attempt with a remember me token
                            // record as an attempt to login (albeit via a hijacked cookie)
                            $failedLoginAttempt = true;
                            $failedLoginReason = 'Remember me token could not be validated.';
                        }
                    }
                }
            }
        }
        ////////////////////////////////////////////////////////
        /// check if credentials were passed via the get string
        ///

        $passedCredentials = $request->only('user_id', 'token');
        $passedIp = $request->ip();
        $passedUserAgent = $request->header('User-Agent');

        if (isset($passedCredentials['user_id']) &&
            isset($passedCredentials['token']) &&
            isset($passedIp) && isset($passedUserAgent)
        ) {
            // credentials passed through the get string - let us validate with DevCo
            $checkCredentials = $this->_auth_service->userAuthenticateToken($passedCredentials['user_id'], $passedCredentials['token'], $passedIp, $passedUserAgent);
            //dd($checkCredentials);
            if (is_string($checkCredentials) || ! $checkCredentials->data->attributes->{'authenticated'} || ! $checkCredentials->data->attributes->{'user-activated'} || ! $checkCredentials->data->attributes->{'user-exists'}) {
                // this is a failed login attempt
                $failedLoginAttempt = true;
                $failedLoginReason = $checkCredentials;
            //throw new AuthenticationException('Unauthenticated 130.');
            } else {
                // this user is authenticated!
                $user = true;
                $checkUser = true; // they are authenticated but we need to check them out.
            }
        }

        if ($checkUser) {
            //dd($checkCredentials->included); /// check the data structure
            $devcoUserKey = $checkCredentials->included[0]->attributes->{'user-key'};
            $devcoEmail = $checkCredentials->included[0]->attributes->{'email'};
            $devcoFirstName = $checkCredentials->included[0]->attributes->{'first-name'};
            $devcoLastName = $checkCredentials->included[0]->attributes->{'last-name'};

            $allitaUser = User::where('devco_key', '=', $devcoUserKey)->first();

            if (! $allitaUser) {
                // no user found - add them to the database
                $allitaUser = new User;
                $allitaUser->devco_key = $user_key;
                $allitaUser->name = $devcoFirstName.' '.$devcoLastName;
                $allitaUser->email = $devcoEmail;
                $allitaUser->password = Hash::make(Str::random(50));
                $allitaUser->active = 1;
                $allitaUser->last_accessed = time();
                $allitaUser->save();

                // get user organizations

                // add organizations that don't exist

                // associate user to organizations

                // associate user to default role
            }
            if ($allitaUser->active == 1) {
                $this->auth->loginUsingId($allitaUser->id, true);
                $allitaUser->update(['last_accessed'=> time()]);
                //$name = $this->auth->getRecallerName();
                // set userActive and user to be true for final test.
                $userActive = true;
                $user = true;
            }
        }

        /// check if device cookie exists: yes: deviceCheck true / no: device false

        /// if deviceCheck: validate to user, twoFactorConfirmed, non expired - yes: device true / no: device false

        /// if user true / device false and !isset($request->twoFactorConfirmationCode)- redirect to register device page with 2fa (show options to enter code, send new code - via methods available on user contact info)

        /// if user false - redirect to devco login

        ////////////////////////////////////////////////////////
        /// check if this is a failed login attempt
        ///

        if ($failedLoginAttempt && $blockAccess == false) { // we don't record additional attempts on blocked access.

            if (is_null($currentlyBlocked)) {
                // there is not a tracker for this yet - insert one:
                $newTracker = new AuthTracker([
                                 'token' => $request->get('token'),
                                 'ip' => $thisIp,
                                 'user_agent' => $thisAgent,
                                 'user_id' => $request->get('user_id'),
                                 'tries' => 1,
                                 'total_failed_tries' => 1,
                                 'last_failed_time' => time(),
                                 'last_failed_reason' => $failedLoginReason,
                                'blocked_until' => null,
                            ]);
                $newTracker->save();
            } else {
                //update the current tracking ip:

                //check that the last login fail for this ip was within the last 5 minutes
                if ($currentlyBlocked->last_failed_time > (time() - 300)) {
                    $loginTries = $currentlyBlocked->tries + 1;
                } else {
                    // it has been more than 5 minutes since the last login fail - we will reset the try count.
                    $loginTries = 1;
                }
                // total tries helps us understand how many failed login attempts have happened.
                $totalTries = $currentlyBlocked->total_failed_tries + 1;
                $blockedUntil = null;
                $failedAttemptUser = null;
                $unlockToken = null;
                $timesLocked = $currentlyBlocked->times_locked;
                $lastFailedTime = time();
                $lastLockedTime = $currentlyBlocked->last_locked_time;

                if (! is_null($request->get('user_id'))) {
                    $failedAttemptUser = $request->get('user_id');
                }

                if ($loginTries > $maxLoginTries) {
                    // this ip has exceeded the max number of tries - block it
                    // the total amount of time to block is the total number of failed tries x the factor.
                    $blockedUntil = time() + ($totalTries * $blockOutTimeFactor);
                    $loginTries = 0; // we reset the number of tries - this is a current tracking number.
                    $unlockToken = Hash::make(Str::random(5000)); // add in an unlock token.
                    $timesLocked = $timesLocked + 1;
                    $lastLockedTime = time();
                }
                $currentlyBlocked->token = $request->get('token');
                $currentlyBlocked->ip = $thisIp;
                $currentlyBlocked->user_agent = $thisAgent;
                $currentlyBlocked->user_id = $failedAttemptUser;
                $currentlyBlocked->tries = $loginTries;
                $currentlyBlocked->total_failed_tries = $totalTries;
                $currentlyBlocked->times_locked = $timesLocked;
                $currentlyBlocked->blocked_until = $blockedUntil;
                $currentlyBlocked->unlock_token = $unlockToken;
                $currentlyBlocked->last_failed_time = $lastFailedTime;
                $currentlyBlocked->last_locked_time = $lastLockedTime;
                $currentlyBlocked->last_failed_reason = $failedLoginReason;
                $currentlyBlocked->save();
            }
        }

        ////////////////////////////////////////////////////////
        //// check if the authorized person is trying to unlock the ip
        ////

        $unblockIp = $request->only('unlock_ip', 'unlock_token');
        if ($user &&
            //$device &&
            //$twoFactorConfirmed &&
            isset($unblockIp['unlock_ip']) &&
            isset($unblockIp['unlock_token'])
        ) {
            // find the currently blocked ip to unlock
            $currentlyBlocked = AuthTracker::
                                    where('ip', $unblockIp['unlock_ip'])
                                    ->where('blocked_until', '>', time())
                                    ->first();

            // make sure this block can be unlocked
            if (! is_null($currentlyBlocked)) {
                // there is an auth_tracking record...
                if ($currentlyBlocked->unlock_attempts < $maxUnlockTries) {
                    $unlockAttempts = $currentlyBlocked->unlock_attempts + 1;
                    $totalUnlockAttempts = $currentlyBlocked->unlock_attempts + 1;
                    // check submitted ip and token against the blocked set
                    if ($currentlyBlocked->ip == $unblockIp['unlock_ip'] &&
                        $currentlyBlocked->unlock_token == $unblockIp['unlock_token']) {
                        // they have passed in a matching unlock token - make a time stamp for the past:
                        $unblockedTime = time() - 1;
                        $currentlyBlocked->update([
                                            'blocked_until' => $unblockedTime,
                                            'unlock_attempts' => 0,
                                            'total_unlock_attempts' => $totalUnlockAttempts,
                                            'unlock_token' => null,
                                            ]);
                        // reset blocked access to false.
                        if ($currentlyBlocked->ip == $thisIp) {
                            $blockAccess = false;
                        }
                    } else {
                        // this was a failed attempt to unlock a ip - update the attempts
                        $currentlyBlocked->update(['unlock_attempts' => $unlockAttempts, 'total_unlock_attempts' => $total_unlock_attempts]);
                        if ($unlockAttempts > $maxUnlockTries) {
                            $blockUnlock = true;
                        }
                    } /// nothing to do...
                } else {
                    // too many unlock attempts -- this must be manually unlocked in the admin panel from a computer on a different ip address.
                    if ($currentlyBlocked->ip == $thisIp) {
                        $blockUnlock = true;
                    }
                }
            }// there is not a matching record to unlock-- either the block expired or the record does not exist.
        }

        // blocked unlock redirect
        if ($blockUnlock) {
            dd('blocking unlock');
        }
        // blocked redirect
        if ($blockAccess) {
            echo '<script>alert(\''.date('m/j/y g:h a', time()).': Sorry, I am blocking access to your ip address '.$thisIp.' due to multiple failed logins. I will allow access again at '.date('m/j/y g:h a', $currentlyBlocked->blocked_until).'. Your admins have been notified and may allow access sooner if they determine there is no threat to the security of the site.\');</script>';
            dd('ACCESS DENIED.');
        }

        // user false // not logged in and/or no credentials
        if ($user == false) {
            //dd('User login failed: '.$failedLoginReason);
            return redirect('login');
            // exit('<script>alert(\'Uh oh, looks like your login expired. Let me take you to DevCo to get you logged in.\'); window.location=\''.$devcoLoginUrl.'?redirect='.urlencode($request->fullUrl())   .'\';</script>');
        }

        // 2fa redirect
        if ($twoFactorConfirmed == false) {
            dd('need to two factor auth');
        }

        // creation of a fresh socket_id for that user

        // $current_user = Auth::user();
        // $token = str_random(10);
        // $current_user->socket_id = $token;
        // $current_user->save();

        //////////////// OLD STUFF ///////////////////

        // if(!$request->user()){
        //     $name = $this->auth->getRecallerName();
        //     if($name){
        //         // make sure life span of cookie to 20 minutes...

        //         $rememberMeCookieValue = $request->cookie($name);

        //         if(!is_null($rememberMeCookieValue)){
        //             $encryptor = app(\Illuminate\Contracts\Encryption\Encrypter::class);

        //             $rememberMeCookieValue = $encryptor->decrypt($rememberMeCookieValue,false);
        //             $credentials = explode('|', $rememberMeCookieValue);
        //             if(is_array($credentials) && count($credentials)>2){
        //                 $rememberedUser = User::where('id',$credentials[0])->where('remember_token',$credentials[1])->where('password', $credentials[2])->first();
        //                 if(!is_null($rememberedUser)){
        //                     $this->auth->loginUsingId($rememberedUser->id,true);
        //                     $key = auth()->getRecallerName();
        //                     cookie()->queue($key, $request->cookie($key), 20);
        //                 } else {
        //                     // redirect to devco
        //                     dd('redirect to devco - could not find the user.');
        //                 }
        //             } else {
        //                 $rememberMeCookieValue = $encryptor->decrypt($rememberMeCookieValue,false);
        //                 dd('cannot find the user info in the explode: '.$rememberMeCookieValue);
        //             }
        //         } else {
        //             dd('there is no remember me token silly... what do you do now??? REFACTOR so that we can do this cleanly and put them to the credentials.');
        //         }

        //     } else {

        //         $credentials = $request->only('user_id', 'token');
        //         $ip = $request->ip();
        //         $user_agent = $request->header('User-Agent');

        //         //dd($credentials,$ip,$user_agent);

        //         // keep track of tries
        //         // $auth_tracker = AuthTracker::where('ip','=',$ip)->where('user_id','=',$request->get('user_id'))->first();
        //         // if(!$auth_tracker){
        //         //     // maybe same IP, different user_id
        //         //     $auth_tracker = AuthTracker::is_blocked_by_ip($ip);

        //         //     if($auth_tracker) {
        //         //         $auth_tracker->incrementTries();
        //         //     }else{
        //         //         $auth_tracker = new AuthTracker([
        //         //                         'token' => $request->get('token'),
        //         //                         'ip' => $ip,
        //         //                         'user_agent' => $user_agent,
        //         //                         'user_id' => $request->get('user_id'),
        //         //                         'tries' => 1,
        //         //                         'blocked_until' => null
        //         //                     ]);
        //         //     }
        //         // }

        //         // if(!isset($credentials['user_id'])){
        //         //     // if($auth_tracker){
        //         //     //     $auth_tracker->incrementTries();
        //         //     // }
        //         //     //Auth::logout();
        //         //     throw new AuthenticationException('Unauthenticated 111. Missing user id.'.Auth::user());
        //         // }

        //         // if(!isset($credentials['token'])){
        //         //     // if($auth_tracker){
        //         //     //     $auth_tracker->incrementTries();
        //         //     // }
        //         //     //Auth::logout();
        //         //     throw new AuthenticationException('Unauthenticated 118. Missing token.');
        //         // }

        //         // we have user_id and token, check credentials with Devco
        //          $check_credentials = $this->_auth_service->userAuthenticateToken($credentials['user_id'], $credentials['token'], $ip, $user_agent);

        //         //dd($check_credentials);
        //         if(!$check_credentials->data->attributes->{'authenticated'} || !$check_credentials->data->attributes->{'user-activated'} || !$check_credentials->data->attributes->{'user-exists'}){

        //             // if($auth_tracker){
        //             //     $auth_tracker->incrementTries();
        //             // }
        //             //Auth::logout();
        //             throw new AuthenticationException('Unauthenticated 130.');

        //         }

        //         // if($auth_tracker){
        //         //     $auth_tracker->resetTries();
        //         // }

        //         // we got a real user, check if that user is in our system
        //         $user_key = $devcoCredentials->{'user-key'};
        //         $email = $devcoCredentials->{'email'};
        //         $first_name = $devcoCredentials->{'first-name'};
        //         $last_name = $devcoCredentials->{'last-name'};

        //         $user = User::where('devco_key', '=', $user_key)->first();

        //         if(!$user){
        //             // no user found - add them to the database
        //             $user = new User;
        //             $user->devco_key = $user_key;
        //             $user->name = $first_name." ".$last_name;
        //             $user->email = $email;
        //             $user->password = Hash::make(Str::random(50));
        //             $user->active = 1;
        //             $user->save();
        //         }

        //         //Auth::loginUsingId($user->id);
        //         Auth::loginUsingId($user->id,true);
        //         $key = auth()->getRecallerName();
        //         cookie()->queue($key, cookie()->get($key), 20);
        //         //dd($user->id,Auth::user(),Auth::check());
        //     }

        // }else{

        //     // user is already logged in
        //     // $user = Auth::user();
        //     //dd('User is logged in already'.$user);
        //     // make sure the user corresponds to the Devco user
        //     ///login user by user id

        //     //
        // }
    }

    //throw new HttpException(503);
}

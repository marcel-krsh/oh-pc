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
        $pcapi_refresh_token = SystemSetting::get('devco_refresh_token'); 
        $pcapi_access_token = SystemSetting::get('devco_access_token'); 

        if($pcapi_refresh_token === null || $pcapi_access_token === null){
            $this->_auth_service->rootAuthenticate();
        }

        // how do we know if the access_token needs to be replaced?

        $this->authenticate($request);
        // $this->checkDevcoSession($request);

        // temporary solution
        // if($request->has('user_id')){
        //     Auth::loginUsingId($request->get('user_id'));
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

            if(!$request->has('user_id') || !$request->has('token')){
                dd("user not logged in, not known, missing credentials");
                // throw new AuthenticationException('Unauthenticated.');
            } 

            // check credentials with Devco
            $check_credentials = $this->_auth_service->userAuthenticateToken($request->get('token'), $ip, $user_agent);

            dd($check_credentials->data->attributes);

            // dd($devco_auth->rootAuthenticate());
            // dd($devco_auth->getLoginUrl());

            // test API
            
            //dd($_auth_service->listPeople());
            //dd($_auth_service->listCounties());

            // throw new AuthenticationException('Unauthenticated.');
        }
        
        // login user by user id
        //Auth::loginUsingId(1);
    }

//throw new HttpException(503);
}

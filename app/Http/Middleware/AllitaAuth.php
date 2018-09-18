<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Auth;
use Session;
use App\Services\AuthService;
use App\Services\DevcoService;

class AllitaAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->authenticate($request);
        $this->checkDevcoSession($request);

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
                // throw new AuthenticationException('Unauthenticated.');
            } 

            // check credentials with Devco
            $devco_auth = new AuthService;
            //dd($devco_auth->rootAuthenticate());
            // dd($devco_auth->getLoginUrl());

            // test API
            $devco = new DevcoService;
            //dd($devco->listPeople());
            dd($devco->listCounties());

            // throw new AuthenticationException('Unauthenticated.');
        }
        
        // login user by user id
        //Auth::loginUsingId(1);
    }

//throw new HttpException(503);
}

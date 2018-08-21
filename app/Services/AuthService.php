<?php

//declare(strict_types=1);

namespace App\Services;

class AuthService
{

    /**
     * Base URL For API calls
     * @var string
     */
    private $_url;

    /**
     * System Level Dev|Co API Token
     * @var string
     */
    private $_devco_token;

    /**
     * Guzzle Client for calls
     * @var
     */
    private $_client;



    public function __construct()
    {
        $this->_url = config('allita.api.pcapi_url');
        $this->_devco_token = config('allita.api.devco_token');
        $this->_client = null;
    }

    // this is at the api level
    public function getApiToken(string $devco_user, string $devco_pass)
    {
        // http://172.16.50.120/api/authenticate?username=user&password=123
        http://devco.ohiohome.org/AuthorityOnlineALT/Unified/UnifiedHeader.aspx


    }

    public function authenticate()
    {

    }






    /**
     * Auth Header HTML
     *
     * @param $options
     *
     * @return \Psr\Http\Message\StreamInterface|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    static public function authHeaderHtml($options = null)
    {
        $html = '';

        return $html;
    }

    /**
     * Device Is Authorized
     *
     * @param $user_id
     * @param $device_id
     *
     * @return bool
     */
    static public function deviceIsAuthorized($user_id, $device_id)
    {
        return true;
    }

    /**
     * Attempt Device Authorization
     *
     * @param $device_id
     * @param $verification_code
     *
     * @return bool
     */
    static public function attemptDeviceAuthorization($device_id, $verification_code)
    {
        return true;
    }
}

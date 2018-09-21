<?php

//declare(strict_types=1);

namespace App\Services;

use App\Models\SystemSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AuthService
{

    /**
     * Base URL For API calls
     * @var string
     */
    private $_url;

    /**
     * Base Directory For API calls
     * @var string
     */
    private $_base_directory;

    /**
     * Username For API calls
     * @var string
     */
    private $_username;

    /**
     * Password For API calls
     * @var string
     */
    private $_password;

    /**
     * Login URL For User Redirection
     * @var string
     */
    private $_login_url;

    /**
     * System Level Dev|Co API Token
     * @var string
     */
    private $_devco_token;

    /**
     * System Level PC API Key
     * @var string
     */
    private $_pcapi_key;

    /**
     * System Level PC API Refresh Token
     * @var string
     */
    private $_pcapi_refresh_token;

    /**
     * System Level PC API Access Token
     * @var string
     */
    private $_pcapi_access_token;

    /**
     * Guzzle Client for calls
     * @var
     */
    private $_client;

    public function __construct()
    {
        $this->_url = config('allita.api.url');
        $this->_base_directory = config('allita.api.base_directory');
        $this->_username = config('allita.api.username');
        $this->_password = config('allita.api.password');
        $this->_login_url = config('allita.api.login_url');

        $this->_pcapi_key = config('allita.api.key');
        $this->_pcapi_refresh_token = SystemSetting::get('devco_refresh_token'); //SystemSetting::get('devco_refresh_token');
        $this->_pcapi_access_token = SystemSetting::get('devco_access_token'); //SystemSetting::get('devco_access_token');

        // if($this->_pcapi_refresh_token === null || $this->_pcapi_access_token === null){
        //     $gettingTokens = new self();
        //     $gettingTokens->rootAuthenticate();
        //     $this->reloadTokens();
        // }

        $this->_client = new Client([
            'base_uri' => $this->_url,
            'timeout'  => 10.0,
        ]);
    }

    public function reloadTokens()
    {
        $this->_pcapi_refresh_token = SystemSetting::get('devco_refresh_token');
        $this->_pcapi_access_token = SystemSetting::get('devco_access_token');
    }

    /**
     * Root (System Level) Key Reset
     */
    public function rootKeyReset()
    {
        $endpoint = "{$this->_base_directory}/root/key-reset?username={$this->_username}&password={$this->_password}&key={$this->_pcapi_key}";

    }

    /**
     * Root (System Level) Authenticate
     *
     * @return bool
     */
    public function rootAuthenticate()
    {
        $endpoint = "{$this->_base_directory}/root/authenticate?username={$this->_username}&password={$this->_password}&key={$this->_pcapi_key}";
        $is_successful = false;

        try {
            $response = $this->_client->request('GET', $endpoint);
            if ($response->getStatusCode() === 200) {
                $result = json_decode($response->getBody()->getContents());
                //dd($result);
                $this->_updateAccessToken($result->access_token);
                $this->_updateRefreshtoken($result->refresh_token);
                $is_successful = true;
            } else {
                // @todo: Throw PC-API Exception
                throw new \Exception("Unexpected Status Code ({$response->getStatusCode()})");
            }
        } catch (GuzzleException | \Exception $e) {
            // @todo: Throw PC-API Exception
            dd($e->getMessage());
        }

        return $is_successful;
    }

    public function rootRefreshToken()
    {
        // https://www.oauth.com/oauth2-servers/access-tokens/access-token-response/
        $endpoint = "{$this->_base_directory}/root/refresh-token?token={$this->_pcapi_refresh_token}";

    }

    /**
     * User Authenticate Token
     *
     * @param int $user_id
     * @param string $user_token
     * @param null   $ip_address
     * @param null   $useragent
     */
    public function userAuthenticateToken(int $user_id, string $user_token, $ip_address = null, $useragent = null)
    {
        $endpoint = "{$this->_base_directory}/devco/user/authenticate-token?devcotoken={$user_token}&user_id={$user_id}&token={$this->_pcapi_access_token}&ipaddress={$ip_address}&useragent={$useragent}";

        try {
            $response = $this->_client->request('GET', $endpoint);
            if ($response->getStatusCode() === 200) {
                $result = json_decode($response->getBody()->getContents());
                
                return $result;
            } else {
                // @todo: Throw PC-API Exception
                throw new \Exception("Unexpected Status Code ({$response->getStatusCode()})");
            }
        } catch (GuzzleException | \Exception $e) {
            // @todo: Throw PC-API Exception
            dd($e->getMessage());
        }
    }

    /**
     * Update Access Token
     *
     * @param $token
     *
     * @return mixed
     */
    private function _updateAccessToken($token)
    {
        return SystemSetting::updateOrCreate([
            'key' => 'devco_access_token'
        ],[
            'value' => $token
        ]);
    }

    /**
     * Update Refresh Token
     * @param $token
     *
     * @return mixed
     */
    private function _updateRefreshToken($token)
    {
        return SystemSetting::updateOrCreate([
            'key' => 'devco_refresh_token'
        ],[
            'value' => $token
        ]);
    }


    //
    //
    //
    //
    //


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

    public function getUrl()
    {
        return $this->_url;
    }

    public function getLoginUrl()
    {
        return $this->_login_url;
    }

    public function getBaseDirectory()
    {
        return $this->_base_directory;
    }
}

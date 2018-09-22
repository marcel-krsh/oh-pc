<?php

//declare(strict_types=1);

namespace App\Services;

use App\Models\SystemSetting;
use Carbon\Carbon;
use DivineOmega\DotNetTicks\Ticks;
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
     * PC API access token expiration time
     * @var integer
     */
    private $_pcapi_access_token_expires;

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

        $this->loadTokensFromDatabase();

        if ($this->accessTokenNeedsRefresh()) {
            //$this->rootRefreshToken();
            $this->rootAuthenticate();
        }

        $this->_client = new Client([
            'base_uri' => $this->_url,
            'timeout'  => 10.0,
        ]);
    }

    /**
     * Load Tokens From Database
     *
     * We store the token data in the database to persist between users and
     * their requests.
     */
    public function loadTokensFromDatabase()
    {
        $this->_pcapi_refresh_token = SystemSetting::get('pcapi_refresh_token');
        $this->_pcapi_access_token = SystemSetting::get('pcapi_access_token');
        $this->_pcapi_access_token_expires = SystemSetting::get('pcapi_access_token_expires');
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

        // fix this
        $this->_client = new Client([
            'base_uri' => $this->_url,
            'timeout'  => 10.0,
        ]);

        try {
            $response = $this->_client->request('GET', $endpoint);
            if ($response->getStatusCode() === 200) {
                $result = json_decode($response->getBody()->getContents());

                $timestamp = intval((new Ticks($this->_getTokenExpiresValueInTicks($result->access_token)))->timestamp());
dd($timestamp);
                $expires_at = Carbon::createFromTimeString($timestamp);

                $this->_updateAccessToken($result->access_token);
                $this->_updateAccessTokenExpires((string) $expires_at->toDateTimeString());
                $this->_updateRefreshToken($result->refresh_token);

                $is_successful = true;
            } else {
                // @todo: Throw PC-API Exception
                throw new \Exception("Unexpected Status Code ({$response->getStatusCode()})");
            }
        } catch (GuzzleException | \Exception $e) {
            // @todo: Throw PC-API Exception
            echo $this->_url."<br>";
            echo $endpoint."<br>";
            dd($e->getMessage());
        }

        return $is_successful;
    }

    private function _getTokenExpiresValueInTicks($token)
    {
        $raw_token = base64_decode($token);
        $raw_token_parts = explode(':', $raw_token);

        return $raw_token_parts[2];
    }

    public function accessTokenNeedsRefresh()
    {
        $result = false;

        // is there a token value?
        if (!$this->_pcapi_access_token) {
            $result = true;
        }

        // is the expires time in the past?
        if ($this->_pcapi_access_token_expires < date('Y-m-d H:i:s')) {
            $result = true;
        }
        return $result;
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
        $endpoint = "{$this->_base_directory}/devco/user/authenticate-token?devcotoken={$user_token}&token={$this->_pcapi_access_token}&ipaddress={$ip_address}&useragent={$useragent}";

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
            dd('Line 174 Auth Service gave an exception from the API server: '.$e->getMessage());
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
            'key' => 'pcapi_access_token'
        ],[
            'value' => $token
        ]);
    }

    /**
     * Update Access Token Expires
     *
     * @param $expires
     *
     * @return mixed
     */
    private function _updateAccessTokenExpires($expires)
    {
        return SystemSetting::updateOrCreate([
            'key' => 'pcapi_access_token_expires'
        ],[
            'value' => $expires
        ]);
    }

    /**
     * Update Refresh Token
     *
     * @param $token
     *
     * @return mixed
     */
    private function _updateRefreshToken($token)
    {
        return SystemSetting::updateOrCreate([
            'key' => 'pcapi_refresh_token'
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

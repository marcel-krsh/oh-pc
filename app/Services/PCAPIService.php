<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Services\AuthService;
use GuzzleHttp\Client;

class PCAPIService
{
    private $_auth;
    private $_api_v;

    public function __construct()
    {
        $this->_auth = new AuthService;
        $this->_api_v = '/api/v1/';
    }

    public function get($url, $parameters = [])
    {
        $this->_auth = new AuthService;

        if ($this->_auth->accessTokenNeedsRefresh()) {
            //$this->_auth->rootRefreshToken();
            $this->_auth->rootAuthenticate();
        }

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'allita/1.0',
            ],

        ]);

        $response = $client->request('GET', $this->_api_v.$url.'&token='.SystemSetting::get('pcapi_access_token'));

        return $response->getBody();
    }

    public function getContents($url, $parameters = [])
    {
        // $this->_auth = new AuthService;

        if ($this->_auth->accessTokenNeedsRefresh()) {
            //$this->_auth->rootRefreshToken();
            $this->_auth->rootAuthenticate();
        }

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'allita/1.0',
            ],
        ]);

        $response = $client->request('GET', $this->_api_v.$url.'&token='.SystemSetting::get('pcapi_access_token'));

        return json_decode($response->getBody()->getContents());
    }

    public function getFile($url, $parameters = [])
    {
        $this->_auth = new AuthService;

        if ($this->_auth->accessTokenNeedsRefresh()) {
            $this->_auth->rootAuthenticate();
        }

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'allita/1.0',
            ],
        ]);

        $response = $client->request(
            'GET',
            $this->_api_v.$url.'&token='.SystemSetting::get('pcapi_access_token'),
            ['sink' => storage_path('foo.pdf')]
        );

        return $response;
    }

    public function post($url, $payload)
    {
        if ($this->_auth->accessTokenNeedsRefresh()) {
            //$this->_auth->rootRefreshToken();
            $this->_auth->rootAuthenticate();
        }

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'allita/1.0',
            ],
        ]);

        $response = $client->request('POST', $this->_api_v.$url.'&token='.SystemSetting::get('pcapi_access_token'), $payload);

        return $response->getBody();
    }

    public function put($url, $payload)
    {
        if ($this->_auth->accessTokenNeedsRefresh()) {
            //$this->_auth->rootRefreshToken();
            $this->_auth->rootAuthenticate();
        }

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'allita/1.0',
            ],
        ]);

        $response = $client->request('PUT', $this->_api_v.$url.'&token='.SystemSetting::get('pcapi_access_token'), ['form_params'=>[$payload]]);

        return $response->getBody();
    }

    public function delete($url, $parameters = [])
    {
        if ($this->_auth->accessTokenNeedsRefresh()) {
            //$this->_auth->rootRefreshToken();
            $this->_auth->rootAuthenticate();
        }

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'allita/1.0',
            ],
        ]);

        $response = $client->request('DELETE', $this->_api_v.$url.'&token='.SystemSetting::get('pcapi_access_token'));

        return $response->getBody();
    }
}

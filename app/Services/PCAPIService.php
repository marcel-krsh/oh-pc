<?php

namespace App\Services;

use App\Services\AuthService;
use GuzzleHttp\Client;
use App\Models\SystemSetting;

class PCAPIService {

    private $_auth;
    private $_api_v;

    public function __construct()
    {
        $this->_auth = new AuthService;
        $this->_api_v = "/api/v1/";
    }


    public function get($url, $parameters=[])
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
        ]);

        $response = $client->request('GET', $this->_api_v.$url."&token=".SystemSetting::get('pcapi_access_token'));

        return $response->getBody();
    }

    public function post($url, $payload)
    {






    }

    public function put($url, $payload)
    {





    }

    public function delete($url)
    {






    }

}
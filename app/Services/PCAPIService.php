<?php

namespace App\Services;

use App\Services\AuthService;
use GuzzleHttp\Client;

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

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
            'verify' => false,
        ]);

        $response = $client->request('GET', $this->_api_v.$url."");

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
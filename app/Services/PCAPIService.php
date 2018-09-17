<?php

namespace App\Services;

use App\Services\AuthService;
use GuzzleHttp\Client;

class PCAPIService {

    private $_auth;

    public function __construct()
    {
        $this->_auth = new AuthService;

    }


    public function get($url, $parameters=[])
    {
        $this->_auth = new AuthService;

        $client = new Client([
            'base_uri' => $this->_auth->getUrl(),
            'timeout'  => 5.0,
        ]);

        $response = $client->request('GET', $url);

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
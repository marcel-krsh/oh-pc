<?php

namespace App\Services;

use App\Services\AuthService;

class PCAPIService {

    private $_auth;

    public function __construct()
    {
        $this->_auth = new AuthService();
    }


    public function get($url, $parameters)
    {





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
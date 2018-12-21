<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;
use App\Services\AuthService;

class AuthIndexController extends Controller
{

    public function index()
    {
        $header = AuthService::authHeaderHtml();

        return \view('poc.auth.index', [
            'header' => $header
        ]);
    }

    public function store()
    {
    }
}

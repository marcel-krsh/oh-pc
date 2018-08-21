<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthSecondFactorController extends Controller
{

    public function index()
    {
        return \view('poc.auth.second-factor');
    }

    public function create()
    {
        return \view('poc.auth.second-factor-form');
    }

    public function store(Request $request)
    {
        // @todo: auth the device here
    }
}

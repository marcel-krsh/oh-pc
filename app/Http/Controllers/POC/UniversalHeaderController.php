<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;

class UniversalHeaderController extends Controller
{
	public function __construct(){
        $this->allitapc();
    }

    public function index()
    {
        return \view('poc.universal-header.index');
    }
}

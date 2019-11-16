<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    public function index(Request $request)
    {
        $service = new DocumentService;

        $documents = $service->getDocuments();

        dd($documents);
    }
}

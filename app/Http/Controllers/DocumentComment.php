<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentComment;
use App\Models\Entity;
use App\Models\Parcel;
use App\Models\Programs;
use App\Models\User;
use Auth;
use DB;
use File;
use Gate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Storage;

class DocumentComment extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }
}

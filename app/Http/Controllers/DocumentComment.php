<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Request;
use Gate;
use Auth;
use App\User;
use File;
use Storage;
use DB;
use App\Programs;
use App\Document;
use App\DocumentComment;
use App\Entity;
use App\Parcel;

class DocumentCommentController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }
}

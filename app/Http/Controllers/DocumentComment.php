<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Request;
use Gate;
use Auth;
use App\Models\User;
use File;
use Storage;
use DB;
use App\Models\Programs;
use App\Models\Document;
use App\Models\DocumentComment;
use App\Models\Entity;
use App\Models\Parcel;

class DocumentCommentController extends Controller
{
    public function __construct(){
        $this->allitapc();
    }
}

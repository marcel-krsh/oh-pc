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
use App\DocumentCategory;
use App\DocumentRule;
use App\DocumentRuleEntry;
use App\Entity;
use App\Parcel;
use App\LogConverter;
use App\Disposition;
use App\DispositionType;
use App\ProgramRule;
use App\InvoiceItem;
use App\ReimbursementInvoice;
use App\ParcelsToReimbursementInvoice;
use App\Transaction;

class ApprovalRequestController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }
}

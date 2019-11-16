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
use App\Models\DocumentCategory;
use App\Models\DocumentRule;
use App\Models\DocumentRuleEntry;
use App\Models\Entity;
use App\Models\Parcel;
// use App\LogConverter;
use App\Models\Disposition;
use App\Models\DispositionType;
use App\Models\ProgramRule;
use App\Models\InvoiceItem;
use App\Models\ReimbursementInvoice;
use App\Models\ParcelsToReimbursementInvoice;
use App\Models\Transaction;

class ApprovalRequestController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }
}

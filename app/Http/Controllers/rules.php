<?php

namespace App\Http\Controllers;

use App\Models\ProgramRule;
use App\Models\User;
use Auth;
use DB;
use Gate;
use Illuminate\Http\Request;

class rules extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    public function edit(ProgramRule $rule, Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            return view('modals.rules-edit-form', compact('rule'));
        } else {
            return 'Sorry you do not have access to the edit rules.';
        }
    }
}

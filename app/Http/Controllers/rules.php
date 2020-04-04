<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramRule;
use Gate;
use \DB;
use Auth;
use App\Models\User;

class rules extends Controller
{
     public function __construct(){
        $this->allitapc();
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

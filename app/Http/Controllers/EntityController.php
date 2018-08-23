<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\State;
use Illuminate\Http\Request;
use App\LogConverter;

class EntityController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $stateIds = State::lists('id');
        $this->validate($request, ['entity_name'=>'unique:entities|required',
            'state_id'=>'required|integer|in:$stateIds',
            'email_address'=>'required|email']);
        $e = Entity::create(['entity_name'=>$request->input('entity_name'), 'state_id'=>$request->input('state_id'),
            'email_address'=> $request->input('email_address')]);
        $lc = new LogConverter('entity', 'create');
        $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' created entity ' . $e->entity_name)->save();
        session()->flash('notify', 'Your new entity has been created!');
        return back();
    }

    public function create()
    {
        // use form builder?
    }
}

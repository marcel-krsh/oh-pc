<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Request;
use Gate;
use Auth;
use Session;
use App\User;
use File;
use Storage;
use DB;
use App\Programs;
use App\Entity;
use App\Parcel;
use App\Note;

class NoteController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * Show the note list for a specific parcel.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function showTabFromParcelId(Parcel $parcel)
    {
        // Search (in session)
        if (Session::has('notes-search') && Session::get('notes-search') != '') {
            $search = Session::get('notes-search');
            $notes = Note::where('parcel_id', $parcel->id)->where('note', 'LIKE', '%'.$search.'%')->with('owner')->orderBy('created_at', 'desc')->get();
        } else {
            $notes = Note::where('parcel_id', $parcel->id)->with('owner')->orderBy('created_at', 'desc')->get();
        }

        
        $attachment = 'attachment';

        $owners_array = [];
        foreach ($notes as $note) {
            // create initials
            $words = explode(" ", $note->owner->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $note->initials = $initials;

            // create associative arrays for initials and names
            if (!array_key_exists($note->owner->id, $owners_array)) {
                $owners_array[$note->owner->id]['initials'] = $initials;
                $owners_array[$note->owner->id]['name'] = $note->owner->name;
                $owners_array[$note->owner->id]['color'] = $note->owner->badge_color;
                $owners_array[$note->owner->id]['id'] = $note->owner->id;
            }
        }

        return view('parcels.parcel_notes', compact('parcel', 'notes', 'owners_array', 'attachment'));
    }

    public function newNoteEntry(Parcel $parcel)
    {
        return view('modals.new-note-entry', compact('parcel'));
    }

    public function searchNotes(Parcel $parcel, Request $request)
    {
        if ($request->has('notes-search')) {
            Session::set('notes-search', $request->get('notes-search'));
        } else {
            Session::forget('notes-search');
        }
        return 1;
    }

    public function create(Request $request)
    {
        if ($request->get('parcel') && $request->get('file-note')) {
            try {
                $parcel = Parcel::where('id', $request->get('parcel'))->first();
            } catch (\Illuminate\Database\QueryException $ex) {
                dd($ex->getMessage());
            }

            $user = Auth::user();

            $note = new Note([
                'owner_id' => $user->id,
                'parcel_id' => $parcel->id,
                'note' => $request->get('file-note')
            ]);
            $note->save();
            $lc = new LogConverter('parcel', 'addnote');
            $lc->setFrom($user)->setTo($parcel)->setDesc($user->email.' added note to parcel')->save();

            return 1;
        } else {
            return "Something went wrong. We couldn't save your note.".$request->get('file-note');
        }
    }

    public function notesFromParcelIdJson(Parcel $parcel)
    {
        // not being used at this time.
        $notes = Note::where('parcel_id', $parcel->id)->get();

        return $notes->toJSON();
    }

    public function printNotes(Parcel $parcel)
    {
        $notes = Note::where('parcel_id', $parcel->id)->with('owner')->orderBy('created_at', 'desc')->get();

        $owners_array = [];
        foreach ($notes as $note) {
            // create initials
            $words = explode(" ", $note->owner->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $note->initials = $initials;

            // create associative arrays for initials and names
            if (!array_key_exists($note->owner->id, $owners_array)) {
                $owners_array[$note->owner->id]['initials'] = $initials;
                $owners_array[$note->owner->id]['name'] = $note->owner->name;
                $owners_array[$note->owner->id]['color'] = $note->owner->badge_color;
                $owners_array[$note->owner->id]['id'] = $note->owner->id;
            }
        }

        return view('parcels.print_notes', compact('parcel', 'notes', 'owners_array'));
    }
}

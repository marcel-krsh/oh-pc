<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\Project;
use App\Models\Note;
use App\Models\UnitInspection;

class NoteController extends Controller
{
     public function __construct(){
        $this->allitapc();
    }

    /**
     * Show the note list for a specific project.
     *
     * @param  int  $project_id
     * @return Response
     */
    public function showTabFromProjectId($project_id)
    {   
        $project = Project::where('id','=',$project_id)->first();

        // Search (in session)
        if (Session::has('notes-search') && Session::get('notes-search') != '') {
            $search = Session::get('notes-search');
            $notes = Note::where('project_id', $project->id)->where('note', 'LIKE', '%'.$search.'%')->with('owner')->orderBy('created_at', 'desc')->get();
            //$notes = Note::where('project_id', $project->id)->whereRaw('LOWER(`note`) LIKE ? ',['%'.trim(strtolower($search)).'%'])->with('owner')->orderBy('created_at', 'desc')->get();
        } else {
            $notes = Note::where('project_id', $project->id)->with('owner')->orderBy('created_at', 'desc')->get();
        }


        
        $attachment = 'attachment';

        $owners_array = [];
        foreach ($notes as $note) {
            $note->initials = $note->owner->initials();

            // create associative arrays for initials and names
            if (!array_key_exists($note->owner->id, $owners_array)) {
                $owners_array[$note->owner->id]['initials'] = $note->initials;
                $owners_array[$note->owner->id]['name'] = $note->owner->full_name();
                $owners_array[$note->owner->id]['color'] = $note->owner->badge_color;
                $owners_array[$note->owner->id]['id'] = $note->owner->id;
            }
        }

       // $units = UnitInspection::where('audit_key', '=', '5974')->with('unit','unit.building.address')->take(10)->get();
        // $units = UnitInspection::where('audit_key', '=', '5974')->selectRaw('unit_id, ANY_VALUE(audit_key) as audit_key, ANY_VALUE(project_id) as project_id, ANY_VALUE(project_key) as project_key, ANY_VALUE(building_id) as building_id, ANY_VALUE(building_key) as building_key')->groupBy('unit_id', 'audit_key', 'project_id', 'project_key', 'building_id', 'building_key')->take(10)->get();
        
        // $units_added = array();

        // foreach ($units as $unit) {
        //     if(!in_array($unit->unit_id, $units_added)){
        //         $units_added[] = $unit->unit_id;
        //     }
        // }
        // dd($units_added);

        return view('projects.partials.notes', compact('project', 'notes', 'owners_array', 'attachment'));
    }

    public function newNoteEntry(Project $project)
    {
        return view('modals.new-note-entry', compact('project'));
    }

    public function searchNotes(Project $project, Request $request)
    {
        if ($request->has('notes-search')) {
            Session::put('notes-search', $request->get('notes-search'));
        } else {
            Session::forget('notes-search');
        }
        return 1;
    }

    public function create(Request $request)
    {
        if ($request->get('project') && $request->get('file-note')) {
            try {
                $project = Project::where('id', $request->get('project'))->first();
            } catch (\Illuminate\Database\QueryException $ex) {
                dd($ex->getMessage());
            }

            $user = Auth::user();

            $note = new Note([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'note' => $request->get('file-note')
            ]);
            $note->save();
            // $lc = new LogConverter('project', 'addnote');
            // $lc->setFrom($user)->setTo($project)->setDesc($user->email.' added note to project')->save();

            return 1;
        } else {
            return "Something went wrong. We couldn't save your note.".$request->get('file-note');
        }
    }

    public function notesFromProjectIdJson(Project $project)
    {
        // not being used at this time.
        $notes = Note::where('project_id', $project->id)->get();

        return $notes->toJSON();
    }

    public function printNotes(Project $project)
    {
        $notes = Note::where('project_id', $project->id)->with('owner')->orderBy('created_at', 'desc')->get();

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

        return view('projects.print_notes', compact('project', 'notes', 'owners_array'));
    }
}

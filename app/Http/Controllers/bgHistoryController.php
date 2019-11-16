<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\ActivityLog;
use App\Models\Parcel;
use App\Models\Role;
use App\Models\User;
use Auth;
use DB;
use Excel;
use Gate;
use Illuminate\Http\Request;
use Session;

class bgHistoryController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    public function parcelHistory(Parcel $parcel)
    {
        if (! Gate::allows('view-all-parcels')) {
            return "Oops, are you sure you're allowed to do this?";
        }

        // Search (in session)
        if (Session::has('activities-search') && Session::get('activities-search') != '') {
            $search = Session::get('activities-search');
            $activities = ActivityLog::where('subject_type', '=', \App\Models\Parcel::class)
                                ->where('subject_id', '=', $parcel->id)
                                ->where('description', 'LIKE', '%'.$search.'%')
                                ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
                                ->orderBy('activity_log.created_at', 'desc')
                                ->select(
                                    'activity_log.id as id',
                                    'activity_log.created_at as date',
                                    'activity_log.description as description',
                                    'activity_log.properties as properties',
                                    'users.id as user_id',
                                    'users.name as name',
                                    'users.email as email',
                                    'users.badge_color as badge_color'
                                )
                                ->get();
        } else {
            $activities = ActivityLog::where('subject_type', '=', \App\Models\Parcel::class)
                                ->where('subject_id', '=', $parcel->id)
                                ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
                                ->orderBy('activity_log.created_at', 'desc')
                                ->select(
                                    'activity_log.id as id',
                                    'activity_log.created_at as date',
                                    'activity_log.description as description',
                                    'activity_log.properties as properties',
                                    'users.id as user_id',
                                    'users.name as name',
                                    'users.email as email',
                                    'users.badge_color as badge_color'
                                )
                                ->get();
        }

        $owners_array = [];
        foreach ($activities as $activity) {
            // create initials
            $words = explode(' ', $activity->name);
            $initials = '';
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $activity->initials = $initials;

            // someone misspelled "visible" in thousands of entries in the db... I don't want to mess with the db
            $activity->description = str_replace('visable', 'visible', $activity->description); // arrrg

            // create associative arrays for initials and names
            if (! array_key_exists($activity->user_id, $owners_array)) {
                $owners_array[$activity->user_id]['initials'] = $initials;
                $owners_array[$activity->user_id]['name'] = $activity->name;
                $owners_array[$activity->user_id]['color'] = $activity->badge_color;
                $owners_array[$activity->user_id]['id'] = $activity->user_id;
            }

            // decode properties json for easy blade use
            $activity->properties_array = json_decode($activity->properties);
        }

        return view('parcels.parcel_history', compact('parcel', 'activities', 'owners_array'));
    }

    public function searchActivities(Parcel $parcel, Request $request)
    {
        if ($request->has('activities-search')) {
            Session::put('activities-search', $request->get('activities-search'));
        } else {
            Session::forget('activities-search');
        }

        return 1;
    }
}

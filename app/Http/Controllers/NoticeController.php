<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Session;

class NoticeController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    /**
     * Show the note list for a specific parcel.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function notices(Request $request)
    {
        if (null !== $request->get('unread')) {
            $unread = $request->get('unread');
        } else {
            $unread = 0;
        }

        if ($request->get('unread') == 1) {
            $notices = Notice::with('owner')->where('user_id', Auth::user()->id)->whereNull('read')->orderBy('created_at', 'desc')->get();
        } else {
            $notices = Notice::with('owner')->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        }

        foreach ($notices as $notice) {
            // create initials
            $words = explode(' ', $notice->owner->name);
            $initials = '';
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $notice->initials = $initials;

            // create associative arrays for initials and names
            if (! array_key_exists($notice->owner->id, $owners_array)) {
                $owners_array[$notice->owner->id]['initials'] = $initials;
                $owners_array[$notice->owner->id]['name'] = $notice->owner->name;
                $owners_array[$notice->owner->id]['color'] = $notice->owner->badge_color;
                $owners_array[$notice->owner->id]['id'] = $notice->owner->id;
            }
        }

        return view('notices.notices', compact('notices', 'owners', 'owners_array', 'unread'));
    }

    public function newNotice()
    {
        if (Auth::user()->entity_type == 'landbank') {
            $where = 'entity_id';
            $whereOperator = '=';
            $whereValue = Auth::user()->entity_id;
        } else {
            $where = 'user_id';
            $whereOperator = '>';
            $whereValue = 0;
        }
        $users = User::join('entity', 'users.entity_id', 'entity.id')->select('users.*', 'entity.name')->where('users.active', 1)->where('entity.active', 1)->where($where, $whereOperator, $whereValue)->orderBy('entity.entity_name', 'asc')->orderBy('users.name', 'asc')->get();

        return view('modals.new-notice-entry', compact('users'));
    }

    public function searchNotices(Request $request)
    {
        if ($request->has('notices-search')) {
            Session::put('notices-search', $request->get('notices-search'));
        } else {
            Session::forget('notices-search');
        }

        return 1;
    }

    public function createNotice(Request $request)
    {
        // create a notice for each user selected
        if (Auth::user()->canManageUsers()) {
            foreach ($users as $user) {
                $notice = new Notice([
                    $user = User::find($user),
                    'owner_id' => Auth::user()->id,
                    'user_id' => $user->id,
                    'subject' => $request->get('subject'),
                    'body' => $request->get('body'),
                ]);
                $notice->save();
                $lc = new LogConverter('notice', 'addnotice');
                $lc->setFrom($user)->setTo($notice)->setDesc($user->email.' added notice to '.$user->name)->save();

                try {
                    $current_recipient = $user;
                    $emailNotification = new EmailNoticeNotification($user, $notice->id);
                    \Mail::to($current_recipient->email)->send($emailNotification);
                    //   \Mail::to('jotassin@gmail.com')->send($emailNotification);
                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                }
            }

            return 1;
        } else {
            return 'Sorry, you don\'t have permission to send notices';
        }
    }
}

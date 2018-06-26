<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Request;
use Route;
use Gate;
use Auth;
use Session;
use App\User;
use File;
use Storage;
use DB;
use App\Program;
use App\Entity;
use App\Parcel;
use App\Document;
use App\DocumentCategory;
use App\Mail\EmailNotification;
use App\Communication;
use App\CommunicationRecipient;
use App\CommunicationDocument;
use App\LogConverter;

class CommunicationController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        //Auth::onceUsingId(2);
    }

    /**
     * Show the communication list for a specific parcel.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function showTabFromParcelId(Parcel $parcel)
    {
        //Search (in session)
        if (Session::has('communications-search') && Session::get('communications-search') != '') {
            $search = Session::get('communications-search');
            $search_messages = Communication::where('parcel_id', $parcel->id)
                    ->where('message', 'LIKE', '%'.$search.'%')
                    ->with('owner')
                    ->with('recipients')
                    ->orderBy('created_at', 'desc')
                    ->pluck('id')->toArray();
            
            $messages = Communication::where(function ($query) use ($search_messages) {
                $query->whereIn('parent_id', $search_messages)
                              ->orWhereIn('id', $search_messages);
            })
                    ->with('owner')
                    ->with('recipients')
                    ->orderBy('created_at', 'desc')
                    ->get();
        } else {
            $messages = Communication::where('parcel_id', $parcel->id)
                    ->where('parent_id', null)
                    ->with('owner')
                    ->orderBy('created_at', 'desc')
                    ->get();
        }

        //$document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

        $current_user = Auth::user();

        $owners_array = array();
        foreach ($messages as $message) {
            // create initials
            $words = explode(" ", $message->owner->name);
            $initials = "";
            foreach ($words as $w) {
                $initials .= $w[0];
            }
            $message->initials = $initials;

            // create associative arrays for initials and names
            if (!array_key_exists($message->owner->id, $owners_array)) {
                $owners_array[$message->owner->id]['initials'] = $initials;
                $owners_array[$message->owner->id]['name'] = $message->owner->name;
                $owners_array[$message->owner->id]['color'] = $message->owner->badge_color;
                $owners_array[$message->owner->id]['id'] = $message->owner->id;
            }

            // get recipients details
            // could be a better query... TBD
            $recipients_array = array();
            foreach ($message->recipients as $recipient) {
                $recipients_array[$recipient->id] = User::find($recipient->user_id);
            }
            $message->recipient_details = $recipients_array;

            $message->summary = strlen($message->message) > 400 ? substr($message->message, 0, 200)."..." : $message->message;


            // in case of a search result with replies, the parent message isn't listed
            // if there is parent_id then use it, otherwise use id
            if ($message->parent_id) {
                $message->replies = Communication::where('parent_id', $message->parent_id)
                ->orWhere('id', $message->parent_id)
                ->count();

                $message_id_array = Communication::where('parcel_id', $parcel->id)
                    ->where('id', $message->parent_id)
                    ->orWhere('parent_id', $message->parent_id)
                    ->pluck('id')->toArray();
            } else {
                $message->replies = Communication::where('parent_id', $message->id)
                ->orWhere('id', $message->id)
                ->count();
                
                $message_id_array = Communication::where('parcel_id', $parcel->id)
                    ->where('id', $message->id)
                    ->orWhere('parent_id', $message->id)
                    ->pluck('id')->toArray();
            }
               
            $message->unseen = CommunicationRecipient::whereIn('communication_id', $message_id_array)
                ->where('user_id', $current_user->id)
                ->where('seen', 0)
                ->count();
        }

        return view('parcels.parcel_communications', compact('parcel', 'messages', 'owners', 'owners_array'));
    }



    public function newCommunicationEntry($parcel_id = null)
    {
        if($parcel_id !== null){

            $parcel = Parcel::where('id','=',$parcel_id)->first();

            $documents = Document::where('parcel_id', $parcel->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

            // build a list of all categories used for uploaded documents in this parcel
            $categories_used = array();
            // category keys for name reference ['id' => 'name']
            $document_categories_key = array();

            if (count($documents)) {
                // create an associative array to simplify category references for each document
                foreach ($documents as $document) {
                    $categories = array(); // store the new associative array cat id, cat name
                     
                    if ($document->categories) {
                        $categories_decoded = json_decode($document->categories, true); // cats used by the doc
                            $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                    } else {
                        $categories_decoded = array();
                    }

                    foreach ($document_categories as $document_category) {
                        $document_categories_key[$document_category->id] = $document_category->document_category_name;

                        // sub key for each document's categories for quick reference
                        if (in_array($document_category->id, $categories_decoded)) {
                            $categories[$document_category->id] = $document_category->document_category_name;
                        }
                    }
                    $document->categoriesarray = $categories;
                }
            } else {
                $documents = array();
            }

            $recipients_from_hfa = User::where('entity_id', '1')
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            if (Auth::user()->entity_id != 1) {
                $recipients = User::where('entity_id', Auth::user()->entity_id)
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $recipients = User::where('entity_id', '!=', 1)
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            }

            return view('modals.new-outbound-email-entry', compact('parcel', 'documents', 'document_categories', 'recipients', 'recipients_from_hfa'));

        }else{

            $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

            // build a list of all categories used for uploaded documents in this parcel
            $categories_used = array();
            // category keys for name reference ['id' => 'name']
            $document_categories_key = array();
            $documents = array();

            $recipients_from_hfa = User::where('entity_id', '1')
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            if (Auth::user()->entity_id != 1) {
                $recipients = User::where('entity_id', Auth::user()->entity_id)
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $recipients = User::where('entity_id', '!=', 1)
                    ->where('active', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            }

            $parcel = null;

            return view('modals.new-outbound-email-entry', compact('parcel', 'documents', 'document_categories', 'recipients', 'recipients_from_hfa'));
        
        }
        
    }



    public function searchCommunications(Parcel $parcel, Request $request)
    {
        if ($request->has('communications-search')) {
            Session::set('communications-search', $request->get('communications-search'));
        } else {
            Session::forget('communications-search');
        }
        return 1;
    }


    public function communicationsFromParcelIdJson(Parcel $parcel)
    {
        // not being used at this time.
        $messages = Communication::where('parcel_id', $parcel->id)->get();

        return $messages->toJSON();
    }

    /**
     * View Replies
     *
     * @param null $parcel_id
     * @param      $message_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function viewReplies($parcel_id=null, $message_id)
    {
        $message = Communication::where('id', $message_id)
                    ->with('owner')
                    ->firstOrFail();

        if ($parcel_id === null || $parcel_id == 0) {
            // used to redirect to dashboard communications
            // tab instead of parcel's communications tab
            $noparcel = 1;
            $parcel = $message->parcel;
        } else {
            $noparcel = 0;
            $parcel = Parcel::find((int) $parcel_id);
        }

        // if(!$parcel) {
        //     throw new \Exception('Parcel not found.');
        // }

        $replies = Communication::where('parent_id', $message->id)
                    ->with('owner')
                    ->orderBy('created_at', 'asc')
                    ->get();

        // set "seen" as 1 when user reads messages
        $current_user = Auth::user();

        $message_id_array = array();
        $message_id_array[] = $message->id;
        foreach ($replies as $reply) {
            $message_id_array[] = $reply->id;
        }
        $user_needs_to_read_more = CommunicationRecipient::whereIn('communication_id', $message_id_array)->where('user_id', $current_user->id)->where('seen', 0)->update(['seen' => 1]);

        if($parcel){
            // fetch documents and categories
            $documents = Document::where('parcel_id', $parcel->id)
                ->orderBy('created_at', 'desc')
                ->get();
            $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();
        }else{
            $documents = null;
            $document_categories = null;
        }
        
        $owner_name_trimmed = rtrim($message->owner->name);
        $words = explode(" ", $owner_name_trimmed);
        $initials = "";
        foreach ($words as $w) {
            $initials .= $w[0];
        }
        $message->initials = $initials;

        $recipients_array = array();
        foreach ($message->recipients as $recipient) {
            $recipients_array[$recipient->id] = User::find($recipient->user_id);
        }
        $message->recipient_details = $recipients_array;

        $categories_used = array();
        $document_categories_key = array();
        if (count($message->documents)) {
            foreach ($message->documents as $document) {
                $categories = array();
                if ($document->document->categories) {
                    $categories_decoded = json_decode($document->document->categories, true); // cats used by the doc
                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = array();
                }
                foreach ($document_categories as $document_category) {
                    $document_categories_key[$document_category->id] = $document_category->document_category_name;
                    // sub key for each document's categories for quick reference
                    if (in_array($document_category->id, $categories_decoded)) {
                        $categories[$document_category->id] = $document_category->document_category_name;
                    }
                }
                $document->categoriesarray = $categories;
            }
        } else {
            $message->documents = array();
        }

        foreach ($replies as $reply) {
            // create initials

            $words = explode(" ", $reply->owner->name);
            $initials = userInitials($reply->owner->name);
            // foreach ($words as $w) {
            //   if(strlen($w[0])>0){
            //         $initials .= $w[0];
            //     }
            // }
            $reply->initials = $initials;

            // get the recipients' details
            $recipients_array = array();
            foreach ($reply->recipients as $recipient) {
                $recipients_array[$recipient->id] = User::find($recipient->user_id);
            }
            $reply->recipient_details = $recipients_array;

            // get the category names for each document in each reply
            // build a list of all categories used for uploaded documents
            $categories_used = array();
            // category keys for name reference ['id' => 'name']
            $document_categories_key = array();

            if (count($reply->documents)) {
                foreach ($reply->documents as $document) {
                    $categories = array();
                    if ($document->document->categories) {
                        $categories_decoded = json_decode($document->document->categories, true); // cats used by the doc
                        $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                    } else {
                        $categories_decoded = array();
                    }
                    foreach ($document_categories as $document_category) {
                        $document_categories_key[$document_category->id] = $document_category->document_category_name;
                        // sub key for each document's categories for quick reference
                        if (in_array($document_category->id, $categories_decoded)) {
                            $categories[$document_category->id] = $document_category->document_category_name;
                        }
                    }
                    $document->categoriesarray = $categories;
                }
            } else {
                $reply->documents = array();
            }
        }


        // help build the upload category list
        if (count($documents)) {
            // create an associative array to simplify category references for each document
            foreach ($documents as $document) {
                $categories = array(); // store the new associative array cat id, cat name
                 
                if ($document->categories) {
                    $categories_decoded = json_decode($document->categories, true); // cats used by the doc
                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = array();
                }

                foreach ($document_categories as $document_category) {
                    $document_categories_key[$document_category->id] = $document_category->document_category_name;

                    // sub key for each document's categories for quick reference
                    if (in_array($document_category->id, $categories_decoded)) {
                        $categories[$document_category->id] = $document_category->document_category_name;
                    }
                }
                $document->categoriesarray = $categories;
            }
        } else {
            $documents = array();
        }

        //prevents the UIkit notify to show up after reading the message
        $user_needs_to_read_more = CommunicationRecipient::where('communication_id', $message->id)
                ->where('user_id', $current_user->id)
                ->where('seen', 0)
                ->update(['seen' => 1]);
                
        return view('modals.communication-replies', compact('message', 'replies', 'parcel', 'documents', 'document_categories', 'noparcel'));
    }



    public function create(Request $request)
    {
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        if (isset($forminputs['communication']) && $forminputs['communication'] > 0) {
            $is_reply = $forminputs['communication'];
        } else {
            $is_reply = 0;
        }

        if ($forminputs['messageBody']) {
            if(isset($forminputs['parcel'])){
                try {
                    $parcel_id = (int) $forminputs['parcel'];
                    $parcel = Parcel::where('id', $parcel_id)->first();
                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                }
                $parcel_id = $parcel->id;
            }else{
                $parcel_id = null;
            }

            $user = Auth::user();

            // create message
            $message_posted = (string) $forminputs['messageBody'];
            if ($is_reply) {
                $original_message = Communication::where('id', $is_reply)->first();
                /// CHECK IF REPLY HAS A PARENT.. IF SO, WE NEED TO SET THE ORIGINAL MESSAGE TO THAT.
                /// EVENTUALLY THIS SHOULD BECOME INDENTED IN THE VIEW
                if (!is_null($original_message->parent_id)) {
                    //orginal message has a parent id
                    $originalMessageId = $original_message->parent_id;
                } else {
                    $originalMessageId = $original_message->id;
                }
                $message = new Communication([
                    'owner_id' => $user->id,
                    'parcel_id' => $parcel_id,
                    'parent_id' => $originalMessageId,
                    'message' => $message_posted
                ]);
                $lc = new LogConverter('communication', 'create');
                $lc->setFrom(Auth::user())->setTo($message)->setDesc(Auth::user()->email . ' created a new communication')->save();
            } else {

                $subject = (string) $forminputs['subject'];
                $message = new Communication([
                    'owner_id' => $user->id,
                    'parcel_id' => $parcel_id,
                    'message' => $message_posted,
                    'subject' => $subject
                ]);
                $lc = new LogConverter('communication', 'create');
                $lc->setFrom(Auth::user())->setTo($message)->setDesc(Auth::user()->email . ' created a new communication')->save();
            }
            $message->save();

            // save recipients
            if ($is_reply) {
                // get existing recipients if a reply
                $message_recipients_array = CommunicationRecipient::where('communication_id', $original_message->id)->pluck('user_id')->toArray();
                 
                foreach ($message_recipients_array as $recipient_id) {
                    if ($recipient_id == $user->id) {
                        $recipient = new CommunicationRecipient([
                            'communication_id' => $message->id,
                            'user_id' => (int) $recipient_id,
                            'seen' => 1
                        ]);
                        $recipient->save();
                    } else {
                        $recipient = new CommunicationRecipient([
                            'communication_id' => $message->id,
                            'user_id' => (int) $recipient_id
                        ]);
                        $recipient->save();
                    }
                }
                // add reply author
                if (!in_array($original_message->owner_id, $message_recipients_array)) {
                    $recipient = new CommunicationRecipient([
                        'communication_id' => $message->id,
                        'user_id' => (int) $original_message->owner_id,
                        'seen' => 1
                    ]);
                    $recipient->save();
                }
            } else {
                if (isset($forminputs['recipients'])) {
                    $message_recipients_array = $forminputs['recipients'];
                    foreach ($forminputs['recipients'] as $recipient_id) {
                        $recipient = new CommunicationRecipient([
                            'communication_id' => $message->id,
                            'user_id' => (int) $recipient_id
                        ]);
                        $recipient->save();
                    }
                }
            }
            
            // save documents
            if (isset($forminputs['documents'])) {
                foreach ($forminputs['documents'] as $document_id) {
                    $document = new CommunicationDocument([
                        'communication_id' => $message->id,
                        'document_id' => (int) $document_id
                    ]);
                    $document->save();
                }
            }

            // send emails
            try {
                foreach ($message_recipients_array as $userToNotify) {
                    if ($userToNotify != $user->id) { // don't send an email to sender
                        $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                        $emailNotification = new EmailNotification($userToNotify, $message->id);
                        \Mail::to($current_recipient->email)->send($emailNotification);
                    }
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                dd($ex->getMessage());
            }

            return 1;
        } else {
            return "Something went wrong. We couldn't save your message. Make sure you have at least one recipient and that your message isn't empty.";
        }
    }

    public function getUnseenMessages()
    {
        $current_user = Auth::user();

        $messages_unseen = CommunicationRecipient::where('user_id', $current_user->id)
                    ->where('seen', 0)
                    ->with('communication')
                    ->with('communication.owner')
                    ->with('communication.parcel')
                    ->orderBy('id', 'desc')
                    ->get();

        $output_array = array();
        $output_array['count'] = count($messages_unseen);
        foreach ($messages_unseen as $message_unseen) {

            if($message_unseen->communication->parent_id){
                $message['parent_id'] = $message_unseen->communication->parent_id;
            }else{
                $message['parent_id'] = null;
            }
            $message['communication_id'] = $message_unseen->communication_id;
            $message['summary'] = strlen($message_unseen->communication->message) > 400 ? substr($message_unseen->communication->message, 0, 200)."..." : $message_unseen->communication->message;
            $message['owner_name'] = $message_unseen->communication->owner->name;
            if($message_unseen->communication->parcel !== null){
                $message['parcel_id'] = $message_unseen->communication->parcel->parcel_id;
            }else{
                $message['parcel_id'] = null;
            }
            $output_array['messages'][] = $message;
        }
        // dd($output_array);
        return $output_array;
    }

    public function getNewMessages()
    {
        $current_user = Auth::user();

        if (session('last-message')) {
            $previous_messages_id = (int) session('last-message');
        } else {
            $previous_messages_id = 0;
        }

        $current_messages_id = CommunicationRecipient::select('communication_id')
                ->where('user_id', $current_user->id)
                ->where('seen', 0)
                ->orderBy('id', 'desc')
                ->first();
        if ($current_messages_id) {
            if ($current_messages_id->communication_id > $previous_messages_id) {
                $delta = $current_messages_id->communication_id - $previous_messages_id;
                $messages_unseen = CommunicationRecipient::where('user_id', $current_user->id)
                    ->where('seen', 0)
                    ->with('communication')
                    ->with('communication.owner')
                    ->with('communication.parcel')
                    ->orderBy('id', 'desc')
                    ->where('id', '>', $previous_messages_id)
                    ->take($delta)
                    ->get();
                foreach ($messages_unseen as $message_unseen) {
                    $message_unseen->communication->summary = strlen($message_unseen->communication->message) > 400 ? substr($message_unseen->communication->message, 0, 200)."..." : $message_unseen->communication->message;
                }

                session(['last-message'=>$current_messages_id->communication_id]);
                return $messages_unseen;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function goToMessage($messageid)
    {
        // is user in the recipient list for this specific message?
        $user = Auth::user();
        $message = Communication::where('id', $messageid)->get()->first();
        if ($message) {
            if (CommunicationRecipient::where('communication_id', '=', $message->id)->where('user_id', '=', $user->id)->exists() || $message->owner_id == $user->id) {
                //prevents the UIkit notify to show up after reading the message
                $user_needs_to_read_more = CommunicationRecipient::where('communication_id', $message->id)->where('user_id', $user->id)->where('seen', 0)->update(['seen' => 1]);
                session(['open_parcel'=>$message->parcel_id, 'parcel_subtab'=>'communications','dynamicModalLoad'=>$message->id]);

                return redirect('/');
            }
        }
        session(['open_parcel'=>'', 'parcel_subtab'=>'','dynamicModalLoad'=>'']);
        $message = "You are not authorized to view this message.";
        $error = "Looks like you are trying to access a message not sent to you.";
        $type = "danger";
        return view('pages.error', compact('error', 'message', 'type'));
    }

    public function communicationsTab()
    {
        $current_user = Auth::user();

        //Search (in session)
        if (Session::has('communications-search') && Session::get('communications-search') != '') {
            $search = Session::get('communications-search');
            $search_messages = Communication::where(function ($query) use ($search) {
                $query->where('message', 'LIKE', '%'.$search.'%');
                $query->orWhereHas('parcel', function ($query) use ($search) {
                    $query->where('parcel_id', 'LIKE', '%'.$search.'%');
                });
            })
                    ->where(function ($query) use ($current_user) {
                        $query->where('owner_id', '=', $current_user->id);
                        $query->orWhereHas('recipients', function ($query) use ($current_user) {
                            $query->where('user_id', '=', $current_user->id);
                        });
                    })
                    ->with('owner')
                    ->with('recipients')
                    ->where(function ($query) {
                        if ($query->has('recipients')) {
                            $query->orderBy('recipients.id');
                        } else {
                            $query->orderBy('id');
                        }
                    })
                    //->orderBy('created_at', 'desc')
                    ->pluck('id')->toArray();
            
            $all_messages = Communication::where(function ($query) use ($search_messages) {
                $query->whereIn('parent_id', $search_messages)
                              ->orWhereIn('id', $search_messages);
            })
                    ->with('owner')
                    ->with('recipients')
                    ->orderBy('created_at', 'desc')
                    ->get();

            if (count($all_messages)) {
                // now that we have all the messages ordered we need to only keep parents
                $parents_array = array();
                foreach ($all_messages as $all_message) {
                    if ($all_message->parent_id === null) {
                        if (!in_array($all_message->id, $parents_array)) {
                            $parents_array[] = $all_message->id;
                        }
                    } else {
                        if (!in_array($all_message->parent_id, $parents_array)) {
                            $parents_array[] = $all_message->parent_id;
                        }
                    }
                }
                $orderMessageByIdProvided = implode(',', array_fill(0, count($parents_array), '?'));
                $messages = Communication::whereIn('id', $parents_array)
                                ->orderByRaw("field(id,{$orderMessageByIdProvided})", $parents_array)
                                ->simplePaginate(100);
            } else {
                $messages = null;
            }
        } else {
            $all_messages = Communication::where(function ($query) use ($current_user) {
                $query->where('owner_id', '=', $current_user->id);
                $query->whereHas('replies');
            })
                        ->orWhereHas('recipients', function ($query) use ($current_user) {
                            $query->where('user_id', '=', $current_user->id);
                        })
                        ->with('owner')
                        ->orderBy('created_at', 'desc')
                        ->get();

            if (count($all_messages)) {
                // now that we have all the messages ordered we need to only keep parents
                $parents_array = array();
                foreach ($all_messages as $all_message) {
                    if ($all_message->parent_id === null) {
                        if (!in_array($all_message->id, $parents_array)) {
                            $parents_array[] = $all_message->id;
                        }
                    } else {
                        if (!in_array($all_message->parent_id, $parents_array)) {
                            $parents_array[] = $all_message->parent_id;
                        }
                    }
                }
                $orderMessageByIdProvided = implode(',', array_fill(0, count($parents_array), '?'));
                $messages = Communication::whereIn('id', $parents_array)
                                ->orderByRaw("field(id,{$orderMessageByIdProvided})", $parents_array)
                                ->simplePaginate(100);
            //->get();
            } else {
                $messages = null;
            }
        }

        $owners_array = array();

        if ($messages) {
            foreach ($messages as $message) {
                // create initials
                $words = explode(" ", $message->owner->name);
                $initials = "";
                foreach ($words as $w) {
                    if (is_array($w)) {
                        $initials .= $w[0];
                    }
                }
                $message->initials = $initials;

                // create associative arrays for initials and names
                if (!array_key_exists($message->owner->id, $owners_array)) {
                    $owners_array[$message->owner->id]['initials'] = $initials;
                    $owners_array[$message->owner->id]['name'] = $message->owner->name;
                    $owners_array[$message->owner->id]['color'] = $message->owner->badge_color;
                    $owners_array[$message->owner->id]['id'] = $message->owner->id;
                }

                // get recipients details
                // could be a better query... TBD
                $recipients_array = array();
                foreach ($message->recipients as $recipient) {
                    $recipients_array[$recipient->id] = User::find($recipient->user_id);
                }
                $message->recipient_details = $recipients_array;

                $message->summary = strlen($message->message) > 200 ? substr($message->message, 0, 200)."..." : $message->message;


                // in case of a search result with replies, the parent message isn't listed
                // if there is parent_id then use it, otherwise use id
                if ($message->parent_id) {
                    // $message->replies = Communication::where('parent_id', $message->parent_id)
                    // ->orWhere('id', $message->parent_id)
                    // ->count();

                    $message_id_array = Communication::where('id', $message->parent_id)
                        ->orWhere('parent_id', $message->parent_id)
                        ->pluck('id')->toArray();
                } else {
                    // $message->replies = Communication::where('parent_id', $message->id)
                    // ->orWhere('id', $message->id)
                    // ->count();
                    
                    $message_id_array = Communication::where('id', $message->id)
                        ->orWhere('parent_id', $message->id)
                        ->pluck('id')->toArray();
                }
                   
                $message->unseen = CommunicationRecipient::whereIn('communication_id', $message_id_array)
                    ->where('user_id', $current_user->id)
                    ->where('seen', 0)
                    ->count();

                // combine all documents from main message and its replies
                $all_docs = array();
                if ($message->documents) {
                    foreach ($message->documents as $message_document) {
                        $all_docs[] = $message_document;
                    }
                }
                if ($message->replies) {
                    foreach ($message->replies as $message_reply) {
                        if ($message_reply->documents) {
                            foreach ($message_reply->documents as $message_reply_document) {
                                $all_docs[] = $message_reply_document;
                            }
                        }
                    }
                }
                $message->all_docs = $all_docs;
            }
        }
        
        $owners_array = collect($owners_array)->sortBy('name')->toArray();
        $programs = Program::orderBy('program_name', 'ASC')->get();

        return view('dashboard.communications', compact('messages', 'owners', 'owners_array', 'current_user', 'programs'));
    }
}

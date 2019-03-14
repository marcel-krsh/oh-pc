<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DocumentTrait;
use App\Mail\EmailNotification;
use App\Models\CachedAudit;
use App\Models\Communication;
use App\Models\CommunicationDocument;
use App\Models\CommunicationRecipient;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Project;
use App\Models\SyncDocuware;
use App\Models\SystemSetting;
use App\Models\User;
//use App\LogConverter;
use Auth;
use Illuminate\Http\Request;
use Session;

class CommunicationController extends Controller
{
    use DocumentTrait;

    public function __construct(Request $request)
    {
        // $this->middleware('auth');
        //Auth::onceUsingId(2);
        //
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            //Auth::onceUsingId(1); // TEST BRIAN
            //Auth::onceUsingId(286); // TEST BRIAN
            Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
        }
    }

    /**
     * Show the communication list for a specific project.
     *
     * @param  int  $project_id
     * @return Response
     */
    public function showTabFromProjectId(Project $project)
    {
        //dd($project);
        //Search (in session)
        if (Session::has('communications-search') && Session::get('communications-search') != '') {
            $search = Session::get('communications-search');
            $search_messages = Communication::where('project_id', $project->id)
                ->where('message', 'LIKE', '%' . $search . '%')
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
            $messages = Communication::where('project_id', $project->id)
                ->where('parent_id', null)
                ->with('owner')
                ->orderBy('created_at', 'desc')
                ->get();

            //dd('Project based: ',$messages, $project);
        }

        //$document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

        $current_user = Auth::user();

        $owners_array = [];
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
            $recipients_array = [];
            foreach ($message->recipients as $recipient) {
                $recipients_array[$recipient->id] = User::find($recipient->user_id);
            }
            $message->recipient_details = $recipients_array;

            $message->summary = strlen($message->message) > 400 ? substr($message->message, 0, 200) . "..." : $message->message;

            // in case of a search result with replies, the parent message isn't listed
            // if there is parent_id then use it, otherwise use id
            if ($message->parent_id) {
                $message->replies = Communication::where('parent_id', $message->parent_id)
                    ->orWhere('id', $message->parent_id)
                    ->count();

                $message_id_array = Communication::where('project_id', $project->id)
                    ->where('id', $message->parent_id)
                    ->orWhere('parent_id', $message->parent_id)
                    ->pluck('id')->toArray();
            } else {
                $message->replies = Communication::where('parent_id', $message->id)
                    ->orWhere('id', $message->id)
                    ->count();

                $message_id_array = Communication::where('project_id', $project->id)
                    ->where('id', $message->id)
                    ->orWhere('parent_id', $message->id)
                    ->pluck('id')->toArray();
            }

            $message->unseen = CommunicationRecipient::whereIn('communication_id', $message_id_array)
                ->where('user_id', $current_user->id)
                ->where('seen', 0)
                ->count();
        }

        return view('projects.pproject_communications', compact('project', 'messages', 'owners', 'owners_array'));
    }

    public function newCommunicationEntry($project_id = null)
    {
        $ohfa_id = SystemSetting::get('ohfa_organization_id');
        if ($project_id !== null) {
            $project = Project::where('id', '=', $project_id)->first();
            // $docuware_documents = SyncDocuware::where('project_id', $project->id)
            //     ->orderBy('created_at', 'desc')
            //     ->get();
            $docuware_documents = $this->projectDocuwareDocumets($project);
            $local_documents = Document::where('project_id', $project->id)
                ->with('assigned_categories')
                ->orderBy('created_at', 'desc')
                ->get();
            $document_categories = DocumentCategory::where('parent_id', '<>', 0)
            		->active()
                ->orderby('document_category_name', 'asc')
                ->with('parent')
                ->get();
            $documents = $docuware_documents->merge($local_documents);
            // build a list of all categories used for uploaded documents in this project
            $categories_used = [];
            // category keys for name reference ['id' => 'name']
            $document_categories_key = [];

            if (count($documents)) {
                // create an associative array to simplify category references for each document
                foreach ($documents as $document) {
                    $categories = []; // store the new associative array cat id, cat name

                    if ($document->categories) {
                        $categories_decoded = json_decode($document->categories, true); // cats used by the doc
                        $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                    } else {
                        $categories_decoded = [];
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
                $documents = [];
            }

            $recipients_from_hfa = User::where('organization_id', '=', $ohfa_id)
                ->leftJoin('people', 'people.id', 'users.person_id')
                ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
                ->join('users_roles', 'users_roles.user_id', 'users.id')
                ->select('users.*', 'last_name', 'first_name', 'organization_name', 'role_id')
                ->where('active', 1)
                ->orderBy('last_name', 'asc')
                ->get();

            if (Auth::user()->pm_access()) {
                $recipients = User::where('organization_id', '=', Auth::user()->organization_id)
                    ->leftJoin('people', 'people.id', 'users.person_id')
                    ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
                    ->join('users_roles', 'users_roles.user_id', 'users.id')
                    ->select('users.*', 'last_name', 'first_name', 'organization_name')
                    ->where('active', 1)
                    ->orderBy('last_name', 'asc')
                    ->get();
            } else {
                $recipients = User::where('organization_id', '!=', $ohfa_id)
                    ->leftJoin('people', 'people.id', 'users.person_id')
                    ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
                    ->join('users_roles', 'users_roles.user_id', 'users.id')
                    ->select('users.*', 'last_name', 'first_name', 'organization_name')
                    ->where('active', 1)
                    ->orderBy('organization_name', 'asc')
                    ->orderBy('last_name', 'asc')
                    ->get();
            }

            return view('modals.new-communication', compact('project', 'documents', 'document_categories', 'recipients', 'recipients_from_hfa', 'ohfa_id'));
        } else {
            $project = null;
            $document_categories = DocumentCategory::where('parent_id', '<>', 0)->where('active', '1')->orderby('document_category_name', 'asc')->get();

            // build a list of all categories used for uploaded documents in this project
            $categories_used = [];
            // category keys for name reference ['id' => 'name']
            $document_categories_key = [];
            $documents = [];

            $recipients_from_hfa = User::where('organization_id', '=', $ohfa_id)
                ->where('active', 1)
                ->leftJoin('people', 'people.id', 'users.person_id')
                ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
                ->join('users_roles', 'users_roles.user_id', 'users.id')
                ->select('users.*', 'last_name', 'first_name', 'organization_name')
                ->where('active', 1)
                ->orderBy('last_name', 'asc')
                ->get();

            // $recipients = User::where('organization_id', '!=', $ohfa_id)
            //     ->orWhereNull('organization_id')
            //     ->where('active', 1)
            //     ->orderBy('name', 'asc')->get();

            if (Auth::user()->pm_access()) {
                $recipients = User::where('organization_id', '=', Auth::user()->organization_id)
                    ->leftJoin('people', 'people.id', 'users.person_id')
                    ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
                    ->join('users_roles', 'users_roles.user_id', 'users.id')
                    ->select('users.*', 'last_name', 'first_name', 'organization_name')
                    ->where('active', 1)
                    ->orderBy('last_name', 'asc')
                    ->get();
            } else {
                $recipients = User::where('organization_id', '!=', $ohfa_id)
                    ->leftJoin('people', 'people.id', 'users.person_id')
                    ->leftJoin('organizations', 'organizations.id', 'users.organization_id')
                    ->join('users_roles', 'users_roles.user_id', 'users.id')
                    ->select('users.*', 'last_name', 'first_name', 'organization_name')
                    ->where('active', 1)
                    ->orderBy('organization_name', 'asc')
                    ->orderBy('last_name', 'asc')
                    ->get();
            }

            $audit = null;

            return view('modals.new-communication', compact('audit', 'documents', 'document_categories', 'recipients', 'recipients_from_hfa', 'ohfa_id', 'project'));
        }
    }

    public function searchCommunications(CachedAudit $audit, Request $request)
    {
        if ($request->has('communications-search')) {
            Session::put('communications-search', $request->get('communications-search'));
        } else {
            Session::forget('communications-search');
        }
        return 1;
    }

    public function communicationsFromProjectIdJson(Project $project)
    {
        // not being used at this time.
        $messages = Communication::where('project_id', $project->id)->get();

        return $messages->toJSON();
    }

    /**
     * View Replies
     *
     * @param null $project_id
     * @param      $message_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function viewReplies($audit_id = null, $message_id)
    {
        $message = Communication::where('id', $message_id)
            ->with('owner')
            ->firstOrFail();

        if ($audit_id === null || $audit_id == 0) {
            // used to redirect to dashboard communications
            // tab instead of project's communications tab
            $noaudit = 1;
            $audit = $message->audit;
        } else {
            $noaudit = 0;
            $audit = CachedAudit::find((int) $audit_id);
        }

        // if(!$project) {
        //     throw new \Exception('Parcel not found.');
        // }

        $replies = Communication::where('parent_id', $message->id)
            ->with('owner')
            ->orderBy('created_at', 'asc')
            ->get();

        // set "seen" as 1 when user reads messages
        $current_user = Auth::user();

        $message_id_array = [];
        $message_id_array[] = $message->id;
        foreach ($replies as $reply) {
            $message_id_array[] = $reply->id;
        }
        $user_needs_to_read_more = CommunicationRecipient::whereIn('communication_id', $message_id_array)->where('user_id', $current_user->id)->where('seen', 0)->update(['seen' => 1]);

        if ($audit) {
            // fetch documents and categories
            $documents = SyncDocuware::where('project_id', $project->id)
                ->orderBy('created_at', 'desc')
                ->get();
            $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();
        } else {
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

        $recipients_array = [];
        foreach ($message->recipients as $recipient) {
            $recipients_array[$recipient->id] = User::find($recipient->user_id);
        }
        $message->recipient_details = $recipients_array;

        $categories_used = [];
        $document_categories_key = [];
        if (count($message->documents)) {
            foreach ($message->documents as $document) {
                $categories = [];
                if ($document->document->categories) {
                    $categories_decoded = json_decode($document->document->categories, true); // cats used by the doc
                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = [];
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
            $message->documents = [];
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
            $recipients_array = [];
            foreach ($reply->recipients as $recipient) {
                $recipients_array[$recipient->id] = User::find($recipient->user_id);
            }
            $reply->recipient_details = $recipients_array;

            // get the category names for each document in each reply
            // build a list of all categories used for uploaded documents
            $categories_used = [];
            // category keys for name reference ['id' => 'name']
            $document_categories_key = [];

            if (count($reply->documents)) {
                foreach ($reply->documents as $document) {
                    $categories = [];
                    if ($document->document->categories) {
                        $categories_decoded = json_decode($document->document->categories, true); // cats used by the doc
                        $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                    } else {
                        $categories_decoded = [];
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
                $reply->documents = [];
            }
        }

        // help build the upload category list
        if ($documents !== null && count($documents)) {
            // create an associative array to simplify category references for each document
            foreach ($documents as $document) {
                $categories = []; // store the new associative array cat id, cat name

                if ($document->categories) {
                    $categories_decoded = json_decode($document->categories, true); // cats used by the doc
                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = [];
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
            $documents = [];
        }

        //prevents the UIkit notify to show up after reading the message
        $user_needs_to_read_more = CommunicationRecipient::where('communication_id', $message->id)
            ->where('user_id', $current_user->id)
            ->where('seen', 0)
            ->update(['seen' => 1]);

        return view('modals.communication-replies', compact('message', 'replies', 'audit', 'documents', 'document_categories', 'noaudit'));
    }

    public function create(Request $request)
    {
        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);
        //return $forminputs;
        if (isset($forminputs['communication']) && $forminputs['communication'] > 0) {
            $is_reply = $forminputs['communication'];
        } else {
            $is_reply = 0;
        }

        if ($forminputs['messageBody']) {
            if (isset($forminputs['audit'])) {
                try {
                    $audit_id = (int) $forminputs['audit'];
                    $audit = CachedAudit::where('id', $audit_id)->first();
                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                }
                $audit_id = $audit->id;
            } else {
                $audit_id = null;
            }
            if (isset($forminputs['project_id'])) {
                try {
                    $project_id = (int) $forminputs['project_id'];
                    $project = Project::where('id', $project_id)->first();
                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                }
                $project_id = $project->id;
            } else {
                $project_id = null;
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
                    'audit_id' => $audit_id,
                    'project_id' => $project_id,
                    'parent_id' => $originalMessageId,
                    'message' => $message_posted,
                ]);
                //$lc = new LogConverter('communication', 'create');
                //$lc->setFrom(Auth::user())->setTo($message)->setDesc(Auth::user()->email . ' created a new communication')->save();
            } else {
                $subject = (string) $forminputs['subject'];
                $message = new Communication([
                    'owner_id' => $user->id,
                    'audit_id' => $audit_id,
                    'project_id' => $project_id,
                    'message' => $message_posted,
                    'subject' => $subject,
                ]);
                //$lc = new LogConverter('communication', 'create');
                //$lc->setFrom(Auth::user())->setTo($message)->setDesc(Auth::user()->email . ' created a new communication')->save();
            }
            $message->save();

            //save documents
            //Here we have 2 types of docs, local and docuware
            //Saving local documents
            if (isset($forminputs['local_documents']) && $forminputs['local_documents'] > 0) {
                $local_documents = $forminputs['local_documents'];
                $unique_docs = array_unique($local_documents);
                foreach ($unique_docs as $document_id) {
                    $doc_id = explode("-", $document_id);
                    $document = new CommunicationDocument([
                        'communication_id' => $message->id,
                        'document_id' => $doc_id[1],
                    ]);
                    $document->save();
                }
            }
            //saving docuware docs
            if (isset($forminputs['docuware_documents']) && $forminputs['docuware_documents'] > 0) {
                $docuware_documents = $forminputs['docuware_documents'];
                $unique_docs = array_unique($docuware_documents);
                foreach ($unique_docs as $document_id) {
                    $doc_id = explode("-", $document_id);
                    $document = new CommunicationDocument([
                        'communication_id' => $message->id,
                        'sync_docuware_id' => $doc_id[1],
                    ]);
                    $document->save();
                }
            }

            // save recipients
            if ($is_reply) {
                // get existing recipients if a reply
                $message_recipients_array = CommunicationRecipient::where('communication_id', $original_message->id)->pluck('user_id')->toArray();

                foreach ($message_recipients_array as $recipient_id) {
                    if ($recipient_id == $user->id) {
                        $recipient = new CommunicationRecipient([
                            'communication_id' => $message->id,
                            'user_id' => (int) $recipient_id,
                            'seen' => 1,
                        ]);
                        $recipient->save();
                    } else {
                        $recipient = new CommunicationRecipient([
                            'communication_id' => $message->id,
                            'user_id' => (int) $recipient_id,
                        ]);
                        $recipient->save();
                    }
                }
                // add reply author
                if (!in_array($original_message->owner_id, $message_recipients_array)) {
                    $recipient = new CommunicationRecipient([
                        'communication_id' => $message->id,
                        'user_id' => (int) $original_message->owner_id,
                        'seen' => 1,
                    ]);
                    $recipient->save();
                }
            } else {
                if (isset($forminputs['recipients'])) {
                    $message_recipients_array = $forminputs['recipients'];
                    foreach ($message_recipients_array as $recipient_id) {
                        if ($recipient_id > 0) {
                            $recipient = new CommunicationRecipient([
                                'communication_id' => $message->id,
                                'user_id' => (int) $recipient_id,
                            ]);
                            $recipient->save();
                        } else {
                            dd('Recipient id failed to pass - value received:' . $recipient_id, $message_recipients_array);
                        }
                    }
                }
            }

            // send emails
            try {
                foreach ($message_recipients_array as $userToNotify) {
                    if ($userToNotify != $user->id) {
                        // don't send an email to sender
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
            ->with('communication.audit')
            ->orderBy('id', 'desc')
            ->get();

        $output_array = [];
        $output_array['count'] = count($messages_unseen);
        foreach ($messages_unseen as $message_unseen) {
            if ($message_unseen->communication->parent_id) {
                $message['parent_id'] = $message_unseen->communication->parent_id;
            } else {
                $message['parent_id'] = null;
            }
            $message['communication_id'] = $message_unseen->communication_id;
            $message['summary'] = strlen($message_unseen->communication->message) > 400 ? substr($message_unseen->communication->message, 0, 200) . "..." : $message_unseen->communication->message;
            $message['owner_name'] = $message_unseen->communication->owner->name;
            if ($message_unseen->communication->audit !== null) {
                $message['audit_id'] = $message_unseen->communication->audit->audit_id;
            } else {
                $message['audit_id'] = null;
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
                    ->with('communication.project')
                    ->orderBy('id', 'desc')
                    ->where('id', '>', $previous_messages_id)
                    ->take($delta)
                    ->get();
                foreach ($messages_unseen as $message_unseen) {
                    $message_unseen->communication->summary = strlen($message_unseen->communication->message) > 400 ? substr($message_unseen->communication->message, 0, 200) . "..." : $message_unseen->communication->message;
                }

                session(['last-message' => $current_messages_id->communication_id]);
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
                session(['open_project' => $message->project_id, 'project_subtab' => 'communications', 'dynamicModalLoad' => $message->id]);

                return redirect('/');
            }
        }
        session(['open_project' => '', 'project_subtab' => '', 'dynamicModalLoad' => '']);
        $message = "You are not authorized to view this message.";
        $error = "Looks like you are trying to access a message not sent to you.";
        $type = "danger";
        return view('pages.error', compact('error', 'message', 'type'));
    }

    // public function communications(Request $request)
    // {
    //     $ohfa_id = SystemSetting::get('ohfa_organization_id');

    //     $owners_array = array();
    //     $programs = array();
    //     $messages = array();
    //     //return \view('dashboard.index'); //, compact('user')
    //     return view('dashboard.communications', compact('owners_array', 'programs', 'messages', 'ohfa_id'));
    // }

    public function communicationsFromProjectTab(Project $project, $page = 0)
    {
        return $this->communicationsTab($page, $project);
    }

    public function communicationsTab($page = 0, $project = 0)
    {
        $number_per_page = 100;
        $skip = $number_per_page * $page;
        $current_user = Auth::user();
        $ohfa_id = SystemSetting::get('ohfa_organization_id');
        //Search (in session)
        if (Session::has('communications-search') && Session::get('communications-search') != '') {
            $search = Session::get('communications-search');
            $search_messages = Communication::with('docuware_documents', 'local_documents')
            		->where(function ($query) use ($search, $project) {
                $query->where('message', 'LIKE', '%' . $search . '%');
                if ($project) {
                    $query->orWhere('project_id', $project->id);
                }
            })
                ->where(function ($query) use ($current_user) {
                    $query->where('owner_id', '=', $current_user->id);
                    $query->orWhereHas('recipients', function ($query) use ($current_user) {
                        $query->where('user_id', '=', $current_user->id);
                    });
                });
            if ($project) {
                $search_messages = $search_messages->where('project_id', $project->id);
            }

            $search_messages = $search_messages->with('owner')
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
                $parents_array = [];
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
                    ->skip($skip)->take($number_per_page)->get();
            } else {
                $messages = null;
            }
        } else {
            if ($project) {
                //on project tab - show all messages
                $user_eval = ">";
                $user_spec = "0";
            } else {
                $user_eval = "=";
                $user_spec = Auth::user()->id;
            }
            $messages = Communication::with('docuware_documents', 'local_documents')
            		->where(function ($query) use ($current_user, $user_eval, $user_spec) {
                $query->where(function ($query) use ($current_user) {
                    $query->where('owner_id', '=', $current_user->id);
                    $query->whereHas('replies');
                });
                $query->orWhereHas('recipients', function ($query) use ($current_user, $user_eval, $user_spec) {
                    $query->where('user_id', "$user_eval", $user_spec);
                });
            })->whereNull('parent_id');

            if ($project) {
                $messages = $messages->where('project_id', $project->id);
            }

            $messages = $messages
                ->with('owner')
                ->orderBy('created_at', 'desc')
                ->skip($skip)->take($number_per_page)
                ->get();
            $messages = $messages->reverse();
        }

        $owners_array = [];

        $data = [];
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
                $recipients_array = [];
                foreach ($message->recipients as $recipient) {
                    $recipients_array[$recipient->id] = User::find($recipient->user_id);
                }
                $message->recipient_details = $recipients_array;

                $recipients = $message->owner->name;
                foreach ($message->recipients as $recipient) {
                    $recipients_array[$recipient->id] = User::find($recipient->user_id);
                }

                if (count($message->recipient_details)) {
                    foreach ($recipients_array as $recipient) {
                        if ($recipient != $current_user && $message->owner != $recipient && $recipient->name != '') {
                            $recipients = $recipients . ", " . $recipient->name;
                        } elseif ($recipient == $current_user) {
                            $recipients = $recipients . ", me";
                        }
                    }
                }

                $message->summary = strlen($message->message) > 200 ? substr($message->message, 0, 200) . "..." : $message->message;

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

                if ($message->unseen) {
                    $unseen = $message->unseen;
                    $communication_unread_class = 'communication-unread';
                } else {
                    $unseen = 0;
                    $communication_unread_class = '';
                }

                // combine all documents from main message and its replies
                $all_docs = [];
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

                $created = date("m/d/y", strtotime($message->created_at)) . " " . date('h:i a', strtotime($message->created_at));
                $created_right = date("m/d/y", strtotime($message->created_at)) . "<br />" . date('h:i a', strtotime($message->created_at));

                if (count($message->documents)) {
                    $hasattachment = 'attachment-true';
                } else {
                    $hasattachment = 'attachment';
                }

                if (count($message->documents)) {
                    $attachment_class = 'attachment-true';
                } else {
                    $attachment_class = 'attachment';
                }

                if ($message->audit) {
                    if (Auth::user()->isFromOrganization($ohfa_id)) {
                        $organization_name = $message->audit->organization->organization_name;
                    } else {
                        $organization_name = '';
                    }

                    $organization_address = $message->audit->address . ', ' . $message->audit->city . ', ';
                    if ($message->audit->state) {
                        $organization_address = $organization_address . $message->audit->state;
                    }
                    $organization_address = $organization_address . ' ' . $message->audit->zip;

                    // if($message->audit->county){
                    //     $organization_address = $organization_address. '<br />'.$message->audit->county->county_name;
                    // }
                } else {
                    $organization_address = '';
                    $organization_name = '';
                }

                $filenames = '';
                if ($message->all_docs && count($message->all_docs)) {
                    foreach ($message->all_docs as $document) {
                        $filenames = $filenames . $document->document->filename . ' ';
                    }
                }

                if ($message->audit) {
                    $program_id = $message->audit->program_id;
                } else {
                    $program_id = '';
                }

                $data[] = [
                    'userId' => '',
                    'socketId' => '',
                    'id' => $message->id,
                    'is_reply' => 0,
                    'parentId' => $message->parent_id,
                    'staffId' => 'staff-' . $message->owner->id,
                    'programId' => 'program-' . $program_id,
                    'hasAttachment' => $attachment_class,
                    'communicationId' => 'communication-' . $message->id,
                    'communicationUnread' => $communication_unread_class,
                    'createdDate' => $created,
                    'createdDateRight' => $created_right,
                    'recipients' => $recipients,
                    'userBadgeColor' => 'user-badge-' . Auth::user()->badge_color,
                    'tooltip' => 'pos:top-left;title:' . $unseen . ' unread messages',
                    'unseen' => $unseen,
                    'auditId' => $message->audit_id,
                    'tooltipOrganization' => 'pos:left;title:' . $organization_name,
                    'organizationAddress' => $organization_address,
                    'tooltipFilenames' => 'pos:top-left;title:' . $filenames,
                    'subject' => $message->subject,
                    'summary' => $message->summary,
                ];
            }
        }

        $owners_array = collect($owners_array)->sortBy('name')->toArray();

        if ($page > 0) {
            return response()->json($data);
        } else {
            if ($project) {
                // get the project
                $project = Project::where('id', '=', $project->id)->first();
                return view('projects.partials.communications', compact('data', 'messages', 'owners', 'owners_array', 'current_user', 'ohfa_id', 'project'));
            } else {
                return view('dashboard.communications', compact('data', 'messages', 'owners', 'owners_array', 'current_user', 'ohfa_id', 'project'));
            }
        }
    }
}

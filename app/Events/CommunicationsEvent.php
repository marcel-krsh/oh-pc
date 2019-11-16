<?php

namespace App\Events;

use App\Events\UpdateEvent;
use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Models\SystemSetting;
use App\Models\User;
use Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CommunicationsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function communicationRecipientCreated(CommunicationRecipient $communication_recipient)
    {
        $current_user = Auth::user();
        $ohfa_id = SystemSetting::get('ohfa_organization_id');
        // $id = $communication_recipient->id;
        // $communication_recipient = CommunicationRecipient::where('id', '=', $id)
        //         ->with('user')
        //         ->first();

        $communicationTotal = CommunicationRecipient::where('user_id', '=', $communication_recipient->user_id)
                ->where('seen', '=', 0)
                ->count();

        // update total unread
        $user = User::find($communication_recipient->user_id);
        $data = [
            'event' =>'tab',
            'userId' => $communication_recipient->user_id,
            'communicationTotal' => $communicationTotal,
        ];

        event(new UpdateEvent($user, $data));
        Log::info('Update Event fired.');

        // $new_communication = Communication::where('id', '=', $communication_recipient->communication_id)->first();

        // $is_reply = 0;
        // if ($new_communication->parent_id !== null) {
        //     // this is a reply
        //     // get the parent existing row
        //     $communication = Communication::where('id', '=', $new_communication->parent_id)->first();
        //     $is_reply = 1;
        // } else {
        //     $communication = $new_communication;
        // }

        // if ($communication) {
        //     $recipients_array = [];
        //     $recipients = $communication->owner->name;
        //     foreach ($communication->recipients as $recipient) {
        //         $recipients_array[$recipient->id] = User::find($recipient->user_id);
        //     }

        //     if (count($recipients_array)) {
        //         foreach ($recipients_array as $recipient) {
        //             if ($recipient != $current_user && $communication->owner != $recipient && $recipient->name != '') {
        //                 $recipients = $recipients. ", ".$recipient->name;
        //             } elseif ($recipient == $current_user) {
        //                 $recipients = $recipients. ", me";
        //             }
        //         }
        //     }

        //     $summary = strlen($communication->message) > 200 ? substr($communication->message, 0, 200)."..." : $communication->message;

        //     $created = date("m/d/y", strtotime($communication->created_at))." ". date('h:i a', strtotime($communication->created_at));
        //     $created_right = date("m/d/y", strtotime($communication->created_at)) ."<br />".date('h:i a', strtotime($communication->created_at));

        //     if (count($communication->documents)) {
        //         $hasattachment = 'attachment-true';
        //     } else {
        //         $hasattachment = 'attachment';
        //     }

        //     $communication_unread_class = 'communication-unread';

        //     if (count($communication->documents)) {
        //         $attachment_class = 'attachment-true';
        //     } else {
        //         $attachment_class = 'attachment';
        //     }

        //     if ($communication->audit) {
        //         if (Auth::user()->isFromOrganization($ohfa_id)) {
        //             $organization_name = $communication->audit->organization->organization_name;
        //         } else {
        //             $organization_name = '';
        //         }

        //         $organization_address = $communication->audit->address.', '.$communication->audit->city.', ';
        //         if ($communication->audit->state) {
        //             $organization_address = $organization_address.$communication->audit->state;
        //         }
        //         $organization_address = $organization_address.' '.$communication->audit->zip;

        //         // if($communication->audit->county){
        //         //     $organization_address = $organization_address. '<br />'.$communication->audit->county->county_name;
        //         // }
        //     } else {
        //         $organization_address = '';
        //         $organization_name = '';
        //     }

        //     $message_id_array = Communication::where('id', $communication->id)
        //                 ->orWhere('parent_id', $communication->id)
        //                 ->pluck('id')->toArray();
        //     $unseen = CommunicationRecipient::whereIn('communication_id', $message_id_array)
        //             ->where('user_id', $current_user->id)
        //             ->where('seen', 0)
        //             ->count();

        //     $filenames = '';
        //     if ($communication->all_docs && count($communication->all_docs)) {
        //         foreach ($communication->all_docs as $document) {
        //             $filenames = $filenames.$document->document->filename.' ';
        //         }
        //     }

        //     if ($communication->audit) {
        //         $program_id = $communication->audit->program_id;
        //     } else {
        //         $program_id = '';
        //     }

        //     // this is a new message
        //     // add a new row on top
        //     $data = [
        //         'event' => 'NewMessage',
        //         'data' => [
        //             'userId' => $communication_recipient->user->id,
        //             'socketId' => $communication_recipient->user->socket_id,
        //             'id' => $communication->id,
        //             'is_reply' => $is_reply,
        //             'parent_id' => $communication->parent_id,
        //             'staff_class' => 'staff-'.$communication->owner->id,
        //             'program_class' => 'program-'.$program_id,
        //             'attachment_class' => $attachment_class,
        //             'communication_id' => 'communication-'.$communication->id,
        //             'communication_unread_class' => $communication_unread_class,
        //             'created' => $created,
        //             'created_right' => $created_right,
        //             'recipients' => $recipients,
        //             'user_badge_color' => 'user-badge-'.Auth::user()->badge_color,
        //             'tooltip' => 'pos:top-left;title:'.$unseen.' unread messages',
        //             'unseen' => $unseen,
        //             'audit_id' => $communication->audit_id,
        //             'tooltip_organization' => 'pos:left;title:'.$organization_name,
        //             'organization_address' => $organization_address,
        //             'tooltip_filenames' => 'pos:top-left;title:'.$filenames,
        //             'subject' => $communication->subject,
        //             'summary' => $summary
        //         ]
        //     ];

        //     Redis::publish('communications', json_encode($data));
        // }
    }
}

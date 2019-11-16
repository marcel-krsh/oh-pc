<?php

namespace App\Events;

use App\Jobs\ComplianceSelectionJob;
use App\Mail\EmailScheduleInvitation;
use App\Mail\EmailSystemAdmin;
use App\Models\Audit;
use App\Models\BuildingInspection;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Finding;
use App\Models\Organization;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectContactRole;
use App\Models\ScheduleTime;
use App\Models\SystemSetting;
use App\Models\Unit;
use App\Models\UnitInspection;
use App\Models\UnitProgram;
use App\Models\User;
use App\Services\DevcoService;
use Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Log;

class SchedulesEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function scheduleTimeCreated(ScheduleTime $scheduletime)
    {
        // email calendar to auditor.

        $recipient = $scheduletime->auditor;

        $emailNotification = new EmailScheduleInvitation($recipient->id, $scheduletime, $scheduletime->ics_link());
        \Mail::to($recipient->email)->send($emailNotification);
        //     \Mail::to('jotassin@gmail.com')->send($emailNotification);

        // $admins = ['jotassin@gmail.com'];
        // $emailNotification = new EmailSystemAdmin('testing the scheduletime event.','');
        // \Mail::to($admins)->send($emailNotification);
    }
}

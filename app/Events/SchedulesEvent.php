<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Log;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Audit;
use App\Models\Unit;
use App\Models\Finding;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\Organization;
use App\Models\BuildingInspection;
use App\Models\ProjectContactRole;
use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\ScheduleTime;
use App\Models\UnitProgram;
use Illuminate\Support\Facades\Redis;
use Auth;
use App\Mail\EmailSystemAdmin;
use App\Mail\EmailScheduleInvitation;

use App\Jobs\ComplianceSelectionJob;

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

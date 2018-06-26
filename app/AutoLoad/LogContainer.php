<?php
use Illuminate\Http\Request;
use App\Parcel;
use App\Http\Requests;
use Auth as Auth;
use \DB as DB;
use App\User;
use Spatie\Activitylog\Models\Activity;

class LogContainer
{
    public $logs = array();

    public function __construct()
    {
        $this->resetsearch();
    }

    public function searchByType($eventtype)
    {
        $tlogs = array();
        foreach ($this->logs as $log) {
            if ($log->eventType == $eventtype) {
                array_push($tlogs, $log);
            }
        }
        $this->logs = $tlogs;
        return $this;
    }

    public function searchByEventName($eventname)
    {
        $tlogs = array();
        foreach ($this->logs as $log) {
            if ($log->eventName == $eventname) {
                array_push($tlogs, $log);
            }
        }
        $this->logs = $tlogs;
        
        return $this;
    }

    public function resetsearch()
    {
        $templogs = Activity::all();
        foreach ($logs as $log) {
            $lc = new LogConverter('temp', 'temp');
            $lclog = $lc->loadFromLog($log);
            array_push($this->logs, $lclog);
        }
        return $this;
    }
}

<?php
namespace App;

use Illuminate\Http\Request;
use App\Parcel;
use App\Http\Requests;
use Auth as Auth;
use \DB as DB;
use App\User;
use Spatie\Activitylog\Models\Activity;

//loads or builds and saves logs, outputs in json
class LogConverter
{
    public $id;
    public $eventName;
    public $eventType;
    public $history;
    public $userRoles;
    public $properties;
    public $desc;
    public $logTo;
    public $logFrom;
    public $timeStamp;
    public $notes;
    public $internal;
    public function __construct($eventtype, $eventname)
    {
        $this->eventType=$eventtype;
        $this->eventName=$eventname;
        $this->history = array();
        $this->properties = array();
        $this->userRoles = array();
        $this->notes = array();
        $this->internal = array();
        $this->desc = "";
        $this->timeStamp = time();
    }

    public function addNote($userid, $notedata)
    {
        $newnote = array();
        $newnote['timestamp'] = time();
        $newnote['fromid'] = $userid;
        $newnote['data'] = $notedata;
        array_push($this->notes, $newnote);
        return $this;
    }
    public function addInternal($name, $internaldata)
    {
        $newinternal = array();
        $newinternal[$name] = $internaldata;
        array_push($this->internal, $newinternal);
        return $this;
    }
    //pass in a model converted to array before and after changes to automaticly add change history
    public function smartAddHistory($oldvars, $newvars)
    {
        foreach ($newvars as $name=>$value) {
            if (($name == 'updated_at') || ($name == 'created_at')) {
                //no need to compare these
            } else {
                if (isset($oldvars[$name])) {
                    //key exists in both
                    if ($value == $oldvars[$name]) {
                    } else {
                        $this->addHistory($name, $oldvars[$name], $value);
                    }
                } else {
                    $this->addHistory($name, '', $value);
                }
            }
        }
        return $this;
    }

    public function addHistory($varname, $oldvalue, $newvalue)
    {
        if ($oldvalue == $newvalue) {
            //nothing changed,  no need to log
            return $this;
        }
        $tempitem = array();
        $oldnewpair = [$oldvalue, $newvalue];
        $this->history[$varname] = $oldnewpair;
        return $this;
    }

    public function addProperty($varname, $varvalue)
    {
        $this->properties[$varname] = $varvalue;
        return $this;
    }

    public function addRole($rolename)
    {
        array_push($this->userRoles, $rolename);
        return $this;
    }

    public function setDesc($desc)
    {
        $this->desc = $desc;
        return $this;
    }

    public function setFrom($from)
    {
        $this->logFrom = $from;
        return $this;
    }

    public function setTo($to)
    {
        $this->logTo = $to;
        return $this;
    }

    public function save()
    {
        //TODO Check if id is set,  if so then update instead of creating new
        $l = activity()
        ->performedOn($this->logTo)
        ->causedBy($this->logFrom)
        ->withProperties([
            'EventType' => $this->eventType,
            'EventName'=> $this->eventName,
            'LogTimestamp' => $this->timeStamp,
            'eventHistory' => $this->history,
            'eventProperties' => $this->properties,
            'userRoles' => $this->userRoles,
            'notes' => $this->notes,
            'internal' => $this->internal,
            ])
        ->log($this->desc);
        $this->id = $l->id;
        return $this;
    }

    public function loadFromLog($log)
    {
        $this->id = $log->id;
        $this->logFrom = $log->causer;
        $this->logTo = $log->subject;
        $this->eventType = $log->getExtraProperty('EventType');
        $this->eventName = $log->getExtraProperty('EventName');
        $this->desc = $log->description;
        $this->history = $log->getExtraProperty('eventHistory');
        $this->userRoles = $log->getExtraProperty('userRoles');
        $this->properties = $log->getExtraProperty('eventProperties');
        $this->timeStamp = $log->getExtraProperty('LogTimestamp');
        $this->notes = $log->getExtraProperty('notes');
        $this->internal = $log->getExtraProperty('internal');
        return $this;
    }




    //---------- Helper functions


    public function getjson()
    {
        return json_encode($this->getLogArray());
    }

    public function getLogArray()
    {
        $ret = array();
        $ret['historyId'] = $this->id;
        $ret['eventType'] = $this->eventType;
        $ret['eventName'] = $this->eventName;
        $ret['staffId']=$this->logFrom->id;
        $ret['staffName'] = $this->logFrom->name;
        $ret['staffInitials']=$this->logFrom->name;  //TODO: put through function to convert to initials
        $ret['staffBadgeColor']=$this->logFrom->badge_color;
        $ret['dateTimeOfHistory']=$this->timeStamp; //TODO: Convert this into human readable date/time
        if ($this->eventType == 'user') {
            $ret['userId']=$this->logTo->id;
            $ret['userName']=$this->logFrom->email;
        }
        $templog = "<p>" . $this->desc . "</p>";
        if (count($this->history) > 0) {
            $templog .= "<br><h2>Changed values:</h2><br>";
            foreach ($this->history as $historyitem=>$historyvalue) {
                $templog.=$historyitem . ": " . $historyvalue[0] . " -> " . $historyvalue[1] . '<br>';
            }
        }
        if (count($this->properties) > 0) {
            $templog .= "<br><h2>Properties:</h2><br>";
            foreach ($this->properties as $propertyname=>$propertyvalue) {
                $templog .= $propertyname . ' -> ' . $propertyvalue . '<br>';
            }
        }
        $ret['historyContent'] = $templog;
        return $ret;
    }
}

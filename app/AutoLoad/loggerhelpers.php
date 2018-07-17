<?php
use Illuminate\Http\Request;
use App\Parcel;
use App\Http\Requests;
use Auth as Auth;
use \DB as DB;
use App\User;
use Spatie\Activitylog\Models\Activity;
use App\LogConverter;

/*
function addUserLog($userfrom,$userto,$eventtype,$eventname,$logmessage) {
    activity()->performedOn($userto)->causedBy($userfrom)->withProperties(['EventType' => $eventtype, 'EventName'=>$eventname, 'LogTimestamp' => time()])->log($logmessage);
}
function addUserLogWithArray($userfrom,$userto,$eventtype,$eventname,$changearray,$logmessage) {
    activity()->performedOn($userto)->causedBy($userfrom)->withProperties(['EventType' => $eventtype, 'EventName'=>$eventname, 'userParams'=>$changearray, 'LogTimestamp' => time()])->log($logmessage);
}
function addEntityLog($userfrom,$entity,$eventtype,$eventname,$logmessage) {
    activity()->performedOn($entity)->causedBy($userfrom)->withProperties(['EventType' => $eventtype, 'EventName'=>$eventname, 'LogTimestamp' => time()])->log($logmessage);
}

function addParcelLogWithArray($userfrom,$parcel,$eventtype,$eventname,$properties,$logmessage){
    activity()->performedOn($parcel)->causedBy($userfrom)->withProperties(['EventType' => $eventtype, 'EventName'=>$eventname,'Properties'=>$properties, 'LogTimestamp' => time()])->log($logmessage);

}
*/
function getUserLogs()
{
    $return = array();
    $allLogs = Activity::all();
    foreach ($allLogs as $log) {
        $logtype = $log->getExtraProperty('EventType');
        if ($logtype == 'user') {
            array_push($return, $log);
        }
    }
    return $return;
}

function getEntityLogs()
{
    $return = array();
    $allLogs = Activity::all();
    foreach ($allLogs as $log) {
        $logtype = $log->getExtraProperty('EventType');
        if ($logtype == 'entity') {
            array_push($return, $log);
        }
    }
    return $return;
}

function getParcelLogs()
{
    $return = array();
    $allLogs = Activity::all();
    foreach ($allLogs as $log) {
        $logtype = $log->getExtraProperty('EventType');
        if ($logtype == 'parcel') {
            array_push($return, $log);
        }
    }
    return $return;
}

function searchJsonLogs(Request $request, $logtype, $start, $stop)
{
    $ret=array();
    $alllogs = Activity::all();
    $count = 0;
    foreach ($alllogs as $log) {
        $addlog = false;
        $thislogtype = $log->getExtraProperty('EventType');
        if (($thislogtype == $logtype) || ($logtype == 'all')) {
            $thislogid = $log->id;
            $thislogtype=$log->getExtraProperty('EventType');
            $thislogname=$log->getExtraProperty('EventName');
            $thislogfrom = $log->causer;
            $thislogto = $log->subject;
            $thislogtimestamp = $log->getExtraProperty('LogTimestamp');
            $thisloghistory = $log->getExtraProperty('eventHistory');
            $thislogproperties = $log->getExtraProperty('properties');
            $thislogdesc = $log->description;

            //input
            $sselect = $request->input('searchselect');
            $stext = $request->input('searchtext');
            //TODO: Convert to case
            if ($sselect = 'any') {
                $addlog = searchall($request, $thislogid, $thislogtype, $thislogname, $thislogfrom->email, $thislogto, $thislogtimestamp, $thisloghistory, $thislogproperties, $thislogdesc);
            } elseif ($sselect == 'logdesc') {
                $addlog = searchbydesc($request, $thislogdesc);
            } elseif ($sselect == 'eventname') {
                $addlog = searchbyname($request, $thislogname);
            } elseif ($sselect == 'email') {
                $addlog = searchbyfrom($request, $thislogfrom->email);
            } elseif ($sselect == 'logid') {
                $addlog = searchbyid($request, $thislogid);
            } elseif ($sselect == 'staffname') {
                $addlog = searchbystaffname($request, $thislogfrom->name);
            } elseif ($sselect == 'history') {
                $addlog = searchbyhistory($request, $thishistory);
            } elseif ($sselect == 'propertyname') {
                $addlog = searchbyproperties($request, $thisproperties);
            } else {
                //selected value is not in listbox, attempt to process
            }


            if ($addlog) {
                $lc = new LogConverter('temp', 'temp');
                $lc->loadFromLog($log);
                $logarray = $lc->getLogArray();
                if ($count >= $start) {
                    array_push($ret, $logarray);
                    $count++;
                }
            }
            if ($count > $stop) {
                return json_encode($ret);
            } //if count
        }  // if logtype
    } //end for each
    return json_encode($ret);
}
function getJsonLogs($logtype, $start, $stop)
{
    $ret=array();
    $alllogs = Activity::all();
    $count = 0;
    foreach ($alllogs as $log) {
        $thislogtype = $log->getExtraProperty('EventType');
        if ($thislogtype == $logtype) {
            $lc = new LogConverter('temp', 'temp');
            $lc->loadFromLog($log);
            $logarray = $lc->getLogArray();
            if ($count >= $start) {
                array_push($ret, $logarray);
            }
            $count++;
        } elseif ($logtype == 'all') {
            $lc = new LogConverter('temp', 'temp');
            $lc->loadFromLog($log);
            $logarray = $lc->getLogArray();
            if ($count >= $start) {
                array_push($ret, $logarray);
            }
            $count++;
        }
        if ($count > $stop) {
            return json_encode($ret);
        }
    }
    return json_encode($ret);
}
function getAllLogs()
{
    $return = Activity::all();
    return $return;
}
function searchall(Request $request, $searchid, $searchtype, $searchname, $searchfrom, $searchto, $searchtimestamp, $searchhistory, $searchproperties, $searchdesc)
{
    $sid = searchbyid($request, $searchid);
    $stype = searchbytype($request, $searchtype);
    $sname = searchbyname($request, $searchname);
    $sfrom = searchbyfrom($request, $searchfrom);
    $sto = searchbyto($request, $searchto);
    $stimestamp = searchbytimestamp($request, $searchtimestamp);
    $shistory = searchbyhistory($request, $searchhistory);
    $sproperties = searchbyproperties($request, $searchproperties);
    $sdesc = searchbydesc($request, $searchdesc);
    return (($sid) || ($stype) || ($sname) || ($sfrom) || ($sto) || ($stimestamp) || ($shistory) || ($sproperties) || ($sdesc));
}
function searchbyid(Request $request, $thisid)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext=="") {
        return true;
    }
    if (($sselect == "searchbyid") && ($thisid == $stext)) {
        return true;
    } else {
        return false;
    }
}
function searchbytype(Request $request, $thistype)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'searchbytype') && ($thistype == $stext)) {
        return true;
    } else {
        return false;
    }
}
function searchbyname(Request $request, $thisname)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'searchbyname') && ($thisname == $stext)) {
        return true;
    } else {
        return false;
    }
}
function searchbyfrom(Request $request, $thisfrom)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'searchbyfrom') && ($thisfrom == $stext)) {
        return true;
    } else {
        return false;
    }
}
function searchbyto(Request $request, $thisto)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'searchbyto') && ($thisto == $sselect)) {
        return true;
    } else {
        return false;
    }
}
function searchbytimestamp(Request $request, $thistimestamp)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'searchbytimestamp') && (stringcontains($thistimestamp, $stext))) {
        return true;
    } else {
        return false;
    }
}
function searchbyhistory(Request $request, $thishistory)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    //todo loop through history
    $ret=false;
    foreach ($thishistory as $history => $historyitems) {
    }
    return $ret;
}
function searchbyproperties(Request $request, $thisproperties)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    //todo: loop through properties
    $ret=false;
    /*foreach($thisproperties as $property=>$propertyvalue) {

    }*/
    return $ret;
}
function searchbydesc(Request $request, $thisdesc)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'searchbydesc') && (stringcontains($thisdesc, $stext))) {
        return true;
    } else {
        return false;
    }
}
function searchbystaffname(Request $request, $thisstaffname)
{
    $stext = $request->input('searchtext');
    $sselect = $request->input('searchselect');
    if ($stext == "") {
        return true;
    }
    if (($sselect == 'staffname') && (stringcontains($thisstaffname, $stext))) {
        return true;
    } else {
        return false;
    }
}


function stringcontains($str, $substr)
{
    if (strpos(str, substr) !== false) {
        return true;
    } else {
        return false;
    }
}

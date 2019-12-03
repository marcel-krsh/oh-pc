<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncMonitoring extends Model
{
    // public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';



    //
    protected $guarded = ['id'];
    public $timestamps = true;

    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getStartDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getCompletedDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getConfirmedDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getOnSiteMonitorEndDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }


}

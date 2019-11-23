<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\SoftDeletes;

class AuditAuditor extends Model
{
    // public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];
    public $timestamps = false;

    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }

    /**
     * Person
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person() : HasOne
    {
        return $this->hasOne(\App\Models\People::class, 'person_key', 'user_key');
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function isScheduled($audit_id, $day_id) : int
    {
        if(count(ScheduleTime::where('auditor_id','=',$this->user_id)->where('audit_id','=',$audit_id)->where('day_id','=',$day_id)->get())){
            return 1;
        }else{
            return 0;
        }
    }
}

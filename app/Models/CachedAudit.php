<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \App\Models\ScheduleTime;
use Carbon;
use Event;
use Auth;

class CachedAudit extends Model
{
    protected $fillable = [
        'id',
        'audit_id',
        'audit_key',
        'project_id',
        'project_key',
        'project_ref',
        'status',
        'lead',
        'lead_json',
        'title',
        'pm',
        'address',
        'city',
        'state',
        'zip',
        'total_buildings',
        'inspection_icon',
        'inspection_status',
        'inspection_status_text',
        'inspection_schedule_date',
        'inspection_schedule_text',
        'inspectable_items',
        'total_items',
        'audit_compliance_icon',
        'audit_compliance_status',
        'audit_compliance_status_text',
        'followup_status',
        'followup_status_text',
        'followup_date',
        'file_audit_icon',
        'file_audit_status',
        'file_audit_status_text',
        'nlt_audit_icon',
        'nlt_audit_status',
        'nlt_audit_status_text',
        'lt_audit_icon',
        'lt_audit_status',
        'lt_audit_status_text',
        'smoke_audit_icon',
        'smoke_audit_status',
        'smoke_audit_status_text',
        'auditor_status_icon',
        'auditor_status',
        'auditor_status_text',
        'message_status_icon',
        'message_status',
        'message_status_text',
        'document_status_icon',
        'document_status',
        'document_status_text',
        'history_status_icon',
        'history_status',
        'history_status_text',
        'step_id',
        'step_status_icon',
        'step_status',
        'step_status_text',
        'estimated_time',
        'estimated_time_needed',
        'created_at',
        'updated_at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($cached_audit) {
            Event::fire('cachedaudit.created', $cached_audit);
        });

        // static::updated(function ($audit) {
        //     Event::fire('audit.updated', $audit);
        // });

        // static::deleted(function ($audit) {
        //     Event::fire('audit.deleted', $audit);
        // });
    }

    
    public function auditors() : HasMany
    {
        return $this->hasMany(\App\Models\AuditAuditor::class, 'audit_id', 'audit_id');
    }

    public function lead_auditor() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'lead');
    }

    public function days() : HasMany
    {
        return $this->hasMany(\App\Models\ScheduleDay::class, 'audit_id')->orderBy('date','asc');
    }

    public function progress() : HasMany{
        return $this->hasMany(\App\Models\GuideProgress::class, 'audit_id')->orderBy('id','desc');
    }

    public function current_step()
    {
        if($this->has('progress')){
            return $this->progress()->first();
        }else{
            return 0;
        }
    }

    public function checkStatus($type = null)
    {

        // 1) auditor
        // no auditors 
        
        // 2) scheduling
        // check for conflicts: unapproved conflicts on any given day, matches the estimated time
        // scheduled date pink if past and next step hasn't been set to audit in progress

        // 3) compliance.
        // if not enough units to meet compliance
        // if going out of compliance, the row goes pink.
        
        // 4) assignments 
        // if some areas are unassigned
        
        // 5) findings & followups
        // within 24h of followup due date, unresolved findings
        
        // 6) messages, documents
        
        $checks = array();

        $checks['status'] = '';

        switch ($type) {
            case "auditors":
                
                // are there no auditors?
                //if(!count())


                break;
            case "schedules":
                // defaults: we assume that we still have a new audit
                // also the case for : are there any schedules?
                $checks['inspection_status_text'] = 'AUDIT NEEDS SCHEDULING'; 
                $checks['inspection_schedule_date'] = null; 
                $checks['inspection_schedule_text'] = 'CLICK TO SCHEDULE THAT AUDIT'; 
                $checks['inspection_status'] = 'action-needed'; 
                $checks['inspection_icon'] = 'a-mobile-repeat';           

                // if we have some schedules in the system
                if($this->inspection_schedule_json !== null){
                    // are there conflicts? TBD
                    // 
                    // $sum_scheduled_time = 0;
                    // $estimated_time = $this->estimated_time;
                    // $first_scheduled_date = null;
                    // $hours_still_needed = $this->hours_still_needed;
                    // 
                    // foreach($this->inspection_schedule_json as $schedule){
                    //      // figure out conflicts here
                    //      // sum total time
                    //      // $sum_scheduled_time
                    //      // get the first $first_scheduled_date
                    // }
                    // 
                    // if($sum_scheduled_time < $estimated_time){
                    //      $checks['inspection_status_text'] = 'AUDIT STILL NEEDS '.$hours_still_needed.' SCHEDULED'; 
                    //      $checks['inspection_schedule_date'] = $this->inspection_schedule_date; 
                    //      $checks['inspection_schedule_text'] = 'CLICK TO RESCHEDULE THAT AUDIT'; 
                    //      $checks['inspection_status'] = 'action-needed'; 
                    //      $checks['inspection_icon'] = 'a-mobile-repeat';   
                    // }
                    // 
                    // // is scheduled date past and next step not "audit in progress" or above
                    // $scheduled_date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $first_scheduled_date);
                    // 
                    // if($this->next_step()){
                    //  $next_step = $this->next_step()->order; // we use the order to compare steps stored in guide_steps
                    // }else{
                    //  $next_step = 0;
                    // }
                    // 
                    // if($first_scheduled_date->isFuture() && $next_step < 4){
                    //      $checks['inspection_status_text'] = 'SCHEDULED DATE PAST, UDPATE NEXT STEP'; 
                    //      $checks['inspection_schedule_date'] = $this->inspection_schedule_date; 
                    //      $checks['inspection_schedule_text'] = 'CLICK TO RESCHEDULE THAT AUDIT'; 
                    //      $checks['inspection_status'] = 'action-needed'; 
                    //      $checks['inspection_icon'] = 'a-mobile-repeat';   
                    //      $checks['status'] = 'critical';   
                    // }
                }
                
                break;
            case "compliance":
                
                break;
            case "assignments":
                
                break;
            case "findings":
                
                break;
            case "reports":
                
                break;
            default:
               return null;
        }

        return $checks;
    }

    public function estimated_hours() 
    {
        if($this->estimated_time){
            return explode(':', $this->estimated_time)[0];
        }else{
            return null;
        }
    }

    public function estimated_minutes() 
    {
        if($this->estimated_time){
            return explode(':', $this->estimated_time)[1];
        }else{
            return null;
        }
    }

    public function hours_still_needed() 
    {
        if($this->estimated_time_needed){
            $time = explode(':', $this->estimated_time_needed);
            return $time[0].":".$time[1];
        }else{
            return null;
        }
    }

    public function estimated_chart_data()
    {
        // used to display the chart on the assignment page
        // chart data depends on the number of days
        // backgroundColor
        // labels
        // data

        // ----------------------------------------------------------------
        // put this in the AuditEvent when updating the cachedaudit!
        // calculate needed time
        if($this->estimated_time){
            $estimated_time = explode(':', $this->estimated_time);
            $estimated_time_in_minutes = $estimated_time[0]*60 + $estimated_time[1];
        }
        $time_scheduled = 0;
        foreach($this->days as $day){
            $time_scheduled = $time_scheduled + ScheduleTime::where('audit_id','=',$this->audit_id)->where('day_id','=',$day->id)->sum('span') * 15;
        }

        $needed_time_in_hours = floor(($estimated_time_in_minutes - $time_scheduled) / 60);
        $needed_time_in_minutes = ($estimated_time_in_minutes - $time_scheduled) % 60;
        
        $needed_time = $needed_time_in_hours.':'.$needed_time_in_minutes.':00';
        if($needed_time != $this->estimated_time_needed){
            // update the cachedaudit record with the new needed time
            $this->update(['estimated_time_needed' => $needed_time]);
            $this->fresh();
        }
        // ----------------------------------------------------------------


        $needed = 0;
        if($this->estimated_time_needed){
            $estimated_time_needed = explode(':', $this->estimated_time_needed);
            //$minutes = 100 * $estimated_time_needed[1] / 60;
            $needed = ltrim($estimated_time_needed[0], '0').'.'.$estimated_time_needed[1];
        }

        $output['data'] = '['.$needed;
        $output['labels'] = '[\'Needed\'';
        $output['backgroundColor'] = '[chartColors.needed';

        foreach($this->days as $day){
            $output['backgroundColor'] = $output['backgroundColor'].',chartColors.estimated';
            $output['labels'] = $output['labels'].',"'.formatDate($day->date, 'F d, Y').'"';

            $schedules_total = ScheduleTime::where('audit_id','=',$this->audit_id)->where('day_id','=',$day->id)->sum('span') * 15 / 60;
            $output['data'] = $output['data'].','.$schedules_total;
        }

        $output['data'] = $output['data'].']';
        $output['labels'] = $output['labels'].']';
        $output['backgroundColor'] = $output['backgroundColor'].']';

        return $output;
    }

    public function getLeadJsonAttribute($value)
    {
        return json_decode($value);
    }

    public function getInspectionScheduleJsonAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    /**
     * audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    // amenity_inspections table is where we store all the amenities that need to be inspected
    public function inspection_items() : HasMany
    {
        return $this->hasMany(\App\Models\AmenityInspection::class, 'audit_id');
    }

    public function total_items()
    {
        return $this->inspection_items()->count();
    }

    public function auditor_items()
    {
        // count all the amenity_inspections items belonging to the current user
        return $this->inspection_items()->where('auditor_id','=',Auth::user()->id)->count();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;
use Illuminate\Support\Str;


class Audit extends Model
{
    protected $guarded = ['id'];

    public $timestamps = true;

    function getCompletedDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getConfirmedDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getStartDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getOnSiteMonitorEndDateAttribute($value)
    {
    	return milliseconds_mutator($value);
    }



    public static function boot()
    {
        parent::boot();

        static::created(function ($audit) {
            Event::dispatch('audit.created', $audit);
        });

        static::updated(function ($audit) {
            Event::dispatch('audit.updated', $audit);
        });

        // static::deleted(function ($audit) {
        //     Event::fire('audit.deleted', $audit);
        // });
    }
    public function total_items() : int {
        // this is the total of project amenities, plus buildings, plus units
        // $total = 0;
        $total = $this->project->total_building_count;
        $total = $total + $this->project_amenity_inspections->count();
        $total = $total + $this->unique_unit_inspections->count();
        //dd($this->project->total_building_count,$this->project_amenity_inspections->count(),$this->unique_unit_inspections->count());
        return  $total;

    }

    public function auditors() : HasMany
    {
        return $this->hasMany(\App\Models\AuditAuditor::class, 'audit_id');
    }

    public function total_inspection_units(){
        return \App\Models\UnitInspection::where('audit_id',$this->id)->groupBy('unit_id')->count();
    }

    public function project_details() : HasOne
    {
        return $this->hasOne(\App\Models\ProjectDetails::class, 'project_id', 'project_id')->where('audit_id',$this->id);
    }
    public function lead() : HasOne
    {

        return $this->hasOne(\App\Models\User::class, 'devco_key', 'user_key');
    }
    public function project(): HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'project_key','development_key');
    }
    public function amenity_inspections() : HasMany {
       return $this->hasMany('\App\Models\AmenityInspection');
    }

    public function project_amenity_inspections() : HasMany {
       return $this->hasMany('\App\Models\AmenityInspection')->whereNull('building_id')->whereNull('unit_id')->with('amenity');
    }

    public function building_inspections() : HasMany {
       return $this->hasMany('\App\Models\BuildingInspection')->with('building')->orderBy('building_id');
    }
    public function unit_inspections() : HasMany {
       return $this->hasMany('\App\Models\UnitInspection')->with('program')->with('building')->orderBy('building_id')->orderBy('unit_id');
    }
    public function unique_unit_inspections() : HasMany {
        return $this->hasMany('\App\Models\UnitInspection')->with('building')->groupBy('unit_id')->orderBy('building_id');

    }
    public function nlts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'nlt');

        })->whereNull('cancelled_at');
    }
    public function site_nlts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'nlt');

        })
        ->whereNull('cancelled_at')
        ->whereNull('building_id')->whereNull('unit_id');
    }
    public function building_nlts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'nlt');

        })
        ->whereNull('cancelled_at')
        ->whereNotNull('building_id');
    }
    public function unit_nlts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'nlt');

        })
        ->whereNull('cancelled_at')
        ->whereNotNull('unit_id');
    }
    public function lts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'lt');

        })->whereNull('cancelled_at');
    }
    public function site_lts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'lt');

        })
        ->whereNull('cancelled_at')
        ->whereNull('building_id')->whereNull('unit_id');
    }
    public function building_lts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'lt');

        })
        ->whereNull('cancelled_at')
        ->whereNotNull('building_id');
    }
    public function unit_lts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'lt');

        })
        ->whereNull('cancelled_at')
        ->whereNotNull('unit_id');
    }
    public function files() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'file');

        })->whereNull('cancelled_at');
    }
    public function findings() : HasMany
    {
        return $this->hasMany('\App\Models\Finding');
    }
    public function reports() : HasMany
    {
        return $this->hasMany('\App\Models\CrrReport');
    }
    public function uncorrectedFindings() : HasMany {
        return $this->hasMany('\App\Models\Finding')->whereNull('cancelled_at')->whereNull('auditor_last_approved_resolution_at')->with('amenity_inspection')->with('auditor')->with('amenity')->with('finding_type')
                ->with('building')->with('unit')->with('unit.building.address')->with('building.address')->with('amenity_inspection.unit_programs')->with('amenity_inspection.unit_programs.program')->with('comments')->with('project.address')->with('photos')->orderBy('building_id','desc')->orderBy('unit_id');
    }
    public function reportableFindings() : HasMany {
        return $this->hasMany('\App\Models\Finding')->whereNull('cancelled_at')->with('amenity_inspection')->with('auditor')->with('amenity')->with('finding_type')
                ->with('building')->with('unit')->with('unit.building.address')->with('building.address')->with('amenity_inspection.unit_programs')->with('amenity_inspection.unit_programs.program')->with('comments')->with('project.address')->with('photos')->orderBy('building_id','desc')->orderBy('unit_id');
    }
    public function reportableLtFindings() : HasMany {
        return $this->hasMany('\App\Models\Finding')->whereHas('finding_type', function( $query ) {
            $query->where('type', '=', 'lt');
        })->whereNull('cancelled_at')->with('amenity_inspection')->with('auditor')->with('amenity')->with('finding_type')->with('building')->with('unit')->with('unit.building.address')->with('building.address')->with('amenity_inspection.unit_programs')->with('amenity_inspection.unit_programs.program')->with('comments')->with('project.address')->orderBy('building_id','desc')->orderBy('unit_id');
    }
    public function ranCompliance(){
        $this->update(['compliance_run'=>1,'rerun_compliance'=>null]);
    }

    public function stats() : HasMany
    {
        return $this->hasMany(\App\Models\StatsCompliance::class);
    }

    public function stats_compliance(){
        // fetches the stats from the stats_compliance table if they exist
        // otherwise, create them using the summary stored in the audit

        if(count($this->stats) == 0){
            // calculate and save data for each program
            $selection_summary = json_decode($this->selection_summary, 1);

            $summary_required = 0;
            $summary_selected = 0;
            $summary_needed = 0;
            $summary_inspected = 0;
            $summary_to_be_inspected = 0;
            $summary_optimized_remaining_inspections = 0;
            $summary_optimized_sample_size = 0;
            $summary_optimized_completed_inspections = 0;

            $summary_required_file = 0;
            $summary_selected_file = 0;
            $summary_needed_file = 0;
            $summary_inspected_file = 0;
            $summary_to_be_inspected_file = 0;
            $summary_optimized_remaining_inspections_file = 0;
            $summary_optimized_sample_size_file = 0;
            $summary_optimized_completed_inspections_file = 0;

            // $program_keys_array = array();
            // $program_keys_array[1] = explode(',', SystemSetting::get('program_bundle')); // 1 - FAF || NSP || TCE || RTCAP || 811 units
            // $program_keys_array[2] = explode(',', SystemSetting::get('program_811')); // 2 - 811 units
            // $program_keys_array[3] = explode(',', SystemSetting::get('program_medicaid')); // 3 - Medicaid units
            // $program_keys_array[4] = explode(',', SystemSetting::get('program_home')); // 4 - HOME
            // $program_keys_array[5] = explode(',', SystemSetting::get('program_ohtf')); // 5 - OHTF
            // $program_keys_array[6] = explode(',', SystemSetting::get('program_nhtf')); // 6 - NHTF
            // $program_keys_array[7] = explode(',', SystemSetting::get('program_htc')); // 7 - HTC
            if(null !== $this->selection_summary){

                foreach($selection_summary['programs'] as $program){ // those are "groups"!

                    // count selected units using the list of program ids
                    $program_keys = explode(',', $program['program_keys']);
                    $selected_units_site = UnitInspection::whereIn('program_key', $program_keys)->where('audit_id', '=', $this->id)->where('group_id', '=', $program['group'])->where('is_site_visit','=',1)->get()->count();
                    $selected_units_file = UnitInspection::whereIn('program_key', $program_keys)->where('audit_id', '=', $this->id)->where('group_id', '=', $program['group'])->where('is_file_audit','=',1)->get()->count();

                    $needed_units_site = max($program['required_units'] - $selected_units_site, 0);
                    $needed_units_file = max($program['required_units_file'] - $selected_units_file, 0);

                    $unit_keys = $program['units_before_optimization'];

                    //whereIn('unit_key', $program['units_after_optimization'])
                    $inspected_units_site = UnitInspection::where('audit_id', '=', $this->id)
                                ->where('group_id', '=', $program['group'])
                                // ->whereHas('amenity_inspections', function($query) {
                                //     $query->where('completed_date_time', '!=', null);
                                // })
                                ->where('is_site_visit', '=', 1)
                                ->where('complete', '!=', NULL)
                                //->select('unit_id')->groupBy('unit_id')->get()
                                ->get()
                                ->count();

                                //whereIn('unit_key', $unit_keys)

                    $inspected_units_file = UnitInspection::where('audit_id', '=', $this->id)
                                ->where('group_id', '=', $program['group'])
                                ->where('is_file_audit', '=', 1)
                                ->where('complete', '!=', NULL)
                                //->select('unit_id')->groupBy('unit_id')->get()
                                ->get()
                                ->count();

                    $to_be_inspected_units_site = $selected_units_site - $inspected_units_site;
                    $to_be_inspected_units_file = $selected_units_file - $inspected_units_file;

                    $group = $program['group'];
                    $newstat = new StatsCompliance([
                        'audit_id' => $this->id,
                        'monitoring_key' => $this->monitoring_key,
                        'project_id' => $this->project_id,
                        'development_key' => $this->development_key,
                        'group_id' => $program['group'],
                        'group_name' => $program['name'],
                        'required_site' => $program['required_units'],
                        'required_file' => $program['required_units_file'],
                        'selected_site' => $selected_units_site,
                        'selected_file' => $selected_units_file,
                        'needed_site' => $needed_units_site,
                        'needed_file' => $needed_units_file,
                        'inspected_site' => $inspected_units_site,
                        'inspected_file' => $inspected_units_file,
                        'tobeinspected_site' => $to_be_inspected_units_site,
                        'tobeinspected_file' => $to_be_inspected_units_file
                    ]);
                    $newstat->save();
                }
            }

            return $this->stats;

        }else{
            return $this->stats;
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

    public function cached_audit() : HasOne
    {
        //return $this->hasOne(\App\Models\CachedAudit::class)->where('audit_id',$this->id);
        return $this->hasOne(\App\Models\CachedAudit::class, 'audit_id','id');
    }
    public function is_archived() : bool {
        if($this->cached_audit){
            if($this->cached_audit->step_id !== 67){
                return false;
            } else {
                return true;
            }
        }else{
            return false;
        }
    }





}

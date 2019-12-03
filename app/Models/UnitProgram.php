<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitProgram extends Model
{
    public $timestamps = true;

    function getCreatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    //
    protected $guarded = ['id'];

    public function program(): HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'program_key', 'program_key');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'unit_key', 'unit_key');
    }

    public function audit(): HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'monitoring_key', 'monitoring_key');
    }

    public function project_program(): HasOne
    {
        return $this->hasOne(\App\Models\ProjectProgram::class, 'project_program_key', 'project_program_key');
    }

    // public function unit_inspections()
    // {
    //     // there may be one record for site and one for file in there...
    //     return \App\Models\UnitInspection::where('program_id', '=', $this->program_id)->where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit_id)->get();
    // }

    public function hasSiteInspection()
    {
        return \App\Models\UnitInspection::where('program_key', '=', $this->program_key)->where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit_id)->where('is_site_visit', '=', 1)->count();
    }

    public function hasFileInspection()
    {
        return \App\Models\UnitInspection::where('program_key', '=', $this->program_key)->where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit_id)->where('is_file_audit', '=', 1)->count();
    }

    public function unitHasSelection()
    {
        return \App\Models\UnitInspection::where('audit_id', '=', $this->audit_id)
            ->where('unit_id', '=', $this->unit_id)
            ->where(function ($query) {
                $query->where('is_file_audit', '=', 1)
                    ->orWhere('is_site_visit', '=', 1);
            })
            ->count();

    }

    public function unitInspected()
    {
        return $this->hasMany(\App\Models\UnitInspection::class, 'unit_id', 'unit_id')
            ->where(function ($query) {
                $query->where('is_file_audit', '=', 1)
                    ->orWhere('is_site_visit', '=', 1);
            });
    }
}

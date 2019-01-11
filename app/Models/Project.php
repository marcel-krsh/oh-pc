<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CachedAudit;
use App\Models\SystemSetting;
use App\Models\Building;

class Project extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    /**
     * audits
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audits() : HasMany
    {
        return $this->hasMany(\App\Models\CachedAudit::class, 'project_id');
    }

    public function amenities() : HasMany
    {
        return $this->hasMany('\App\Models\ProjectAmenity');
    }

    public function currentAudit() : CachedAudit
    {
        $audit = CachedAudit::where('project_id', '=', $this->id)->orderBy('id', 'desc')->first();
        return $audit;
    }

    public function address() : HasOne
    {
        return $this->hasOne(\App\Models\Address::class, 'address_id', 'physical_address_id');
    }

    

    public function programs() : HasMany
    {
        return $this->hasMany(\App\Models\ProjectProgram::class, 'project_id', 'project_id')->where('project_program_status_type_id', SystemSetting::get('active_program_status_type_id'));
    }

    public function buildings() : HasMany
    {
        return $this->hasMany('\App\Models\Building');
    }

    public function units() : HasManyThrough {
        return $this->hasManyThrough('App\Models\Unit', 'App\Models\Building');
    }

    public function projectProgramUnitCounts()
    {

        $programs = $this->programs;
        $programCounts = [];
        foreach ($programs as $program) {
            $count = UnitProgram::where('audit_id', $this->currentAudit()->audit_id)
                                            ->where('program_id', $program->program_id)
                                            ->count();
            $programCounts[] = [$program->programs->program_name => $count,'program_id'=>$program->program_id];
        }
        if (count($programCounts)<1) {
            $programCounts[] = ['No Programs Found' => 'NA'];
        }
        return $programCounts;
    }
}

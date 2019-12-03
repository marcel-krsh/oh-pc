<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon;

class ProjectDetail extends Model
{
	  public $timestamps = true;

    protected $fillable = [
        'project_id',
        'audit_id',
        'last_audit_completed',
        'next_audit_due',
        'score_percentage',
        'score',
        'total_building',
        'total_building_common_areas',
        'total_building_systems',
        'total_building_exteriors',
        'total_project_common_areas',
        'total_units',
        'market_rate',
        'subsidized',
        'programs',
        'owner_name',
        'owner_poc',
        'owner_phone',
        'owner_fax',
        'owner_email',
        'owner_address',
        'owner_address2',
        'owner_city',
        'owner_state',
        'owner_zip',
        'manager_name',
        'manager_poc',
        'manager_phone',
        'manager_fax',
        'manager_email',
        'manager_address',
        'manager_address2',
        'manager_city',
        'manager_state',
        'manager_zip',
        'created_at',
        'updated_at'
    ];

    /**
     * Audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
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

    // public function getProgramsAttribute($value) {
    //   return json_decode($value);
    // }
}

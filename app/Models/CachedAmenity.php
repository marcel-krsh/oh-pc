<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon;

class CachedAmenity extends Model
{
    protected $fillable = [
        'audit_id',
        'project_id',
        'building_id',
        'unit_id',
        'amenity_type_id',
        'status',
        'auditor',
        'auditor_name',
        'auditor_color',
        'auditor_initials',
        'name',
        'finding_nlt_status',
        'finding_lt_status',
        'finding_sd_status',
        'finding_photo_status',
        'finding_comment_status',
        'finding_copy_status',
        'finding_trash_status',
        'created_at',
        'updated_at'
    ];

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
     * Audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    /**
     * Unit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
    }

    /**
     * Building
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
    }

    /**
     * Amenity Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function amenity_type() : HasOne
    {
        return $this->hasOne(\App\Models\AmenityType::class, 'id', 'amenity_type_id');
    }
}

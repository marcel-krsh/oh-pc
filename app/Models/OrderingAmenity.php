<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderingAmenity extends Model
{
    protected $table = 'ordering_amenities';

    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'audit_id',
        'project_id',
        'building_id',
        'unit_id',
        'amenity_id',
        'order'
    ];

    /**
     * Building
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\CachedBuilding::class, 'id', 'building_id');
    }

    /**
     * Amenity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function amenity() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAmenity::class, 'id', 'amenity_id');
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
     * Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedBuildingUnit::class, 'id', 'unit_id');
    }

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
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderingBuildingArea extends Model
{
    protected $table = 'ordering_building_area';

    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'audit_id',
        'building_id',
        'area_id',
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
     * Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function area() : HasOne
    {
        return $this->hasOne(\App\Models\CachedBuildingArea::class, 'id', 'area_id');
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

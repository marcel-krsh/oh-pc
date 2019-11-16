<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderingUnit extends Model
{
    protected $table = 'ordering_unit';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'audit_id',
        'project_id',
        'building_id',
        'unit_id',
        'order',
        'amenity_inspection_id',
    ];

    public function amenity_inspection() : HasOne
    {
        return $this->hasOne(\App\Models\AmenityInspection::class, 'id', 'amenity_inspection_id');
    }

    /**
     * Building.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\CachedBuilding::class, 'building_id', 'building_id')->where('audit_id', '=', $this->audit_id);
    }

    /**
     * Project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    /**
     * Area.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedUnit::class, 'id', 'unit_id');
    }

    /**
     * Audit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
    }

    /**
     * User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function auditors()
    {
        // $this->building_id is the cachedbuilding id

        if ($this->unit) {

            //dd($this->audit_id, $this->building_id, $this->id);
            // get all the auditors for that building/units in the building
            $auditor_ids = \App\Models\AmenityInspection::where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit->unit_id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();

            $auditors = User::whereIn('id', $auditor_ids)->get();
        } else {
            $auditors = null;
        }

        return $auditors;
    }

    public function amenity_inspections()
    {
        $amenities = \App\Models\AmenityInspection::where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit->unit_id)->whereNotNull('unit_id')->get();

        return $amenities;
    }
}

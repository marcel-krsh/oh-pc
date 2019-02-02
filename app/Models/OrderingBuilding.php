<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderingBuilding extends Model
{
    protected $table = 'ordering_building';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'audit_id',
        'project_id',
        'building_id',
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

    public function auditors()
    {
        //dd($this->audit_id, $this->id, $this->building_id, $this->building->building_id);
        // $this->building_id is the cachedbuilding id

        //dd($this->audit_id, $this->building_id, $this->id);
        // get all the auditors for that building/units in the building
        $auditor_ids = \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('building_id','=',$this->building->building_id)->whereNotNull('auditor_id')->whereNotNull('building_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();

        // we are missing building_ids in the table, we for now we need to go through the units individually
        $auditor_unit_ids = array();
        
        $units = Unit::where('building_id', '=', $this->building->building_id)->get();

        foreach($units as $unit){
            $auditor_unit_ids = array_merge($auditor_unit_ids, \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('unit_id','=',$unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
        }

        $auditor_ids = array_merge($auditor_ids, $auditor_unit_ids);

        $auditors = User::whereIn('id', $auditor_ids)->get();

        return $auditors;
    }
}

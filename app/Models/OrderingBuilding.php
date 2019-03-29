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
        'project_id',
        'audit_id',
        'building_id',
        'amenity_id',
        'order',
        'amenity_inspection_id'
    ];

    /**
     * Building
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function building() : HasOne
    // {
    //     return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
    // }

    public function building() : HasOne
    {
        if($this->building_id === NULL){
            return $this->hasOne(\App\Models\CachedBuilding::class, 'amenity_id', 'amenity_id')->where('audit_id','=',$this->audit_id)->where('amenity_inspection_id', '=', $this->amenity_inspection_id);
            //$cachedbuilding = \App\Models\CachedBuilding::where('audit_id','=',$this->audit_id)->where('amenity_id','=',$this->amenity_id)->first(); 
        }else{
            //$cachedbuilding = \App\Models\CachedBuilding::where('audit_id','=',$this->audit_id)->where('building_id','=',$this->building_id)->first();
            return $this->hasOne(\App\Models\CachedBuilding::class, 'building_id', 'building_id');
        }

        //return $cachedbuilding;
    }

    /**
     * Audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'audit_id', 'audit_id');
    }

    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    public function amenity_inspection() : HasOne
    {
        return $this->hasOne(\App\Models\AmenityInspection::class, 'id', 'amenity_inspection_id');
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
       
        // if building_id is null, we are looking at a building-level amenity that should only have one auditor
        if($this->building->building_id){
            $auditor_ids = \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('building_id','=',$this->building->building_id)->whereNotNull('auditor_id')->whereNotNull('building_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
            

            // we are missing building_ids in the table, we for now we need to go through the units individually
            $auditor_unit_ids = array();
            
            $units = Unit::where('building_id', '=', $this->building->building_id)->get();

            foreach($units as $unit){
                $auditor_unit_ids = array_merge($auditor_unit_ids, \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('unit_id','=',$unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
            }

            $auditor_ids = array_merge($auditor_ids, $auditor_unit_ids);
        }else{
            $auditor_ids = \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('amenity_id','=',$this->amenity_id)->whereNotNull('auditor_id')->whereNull('building_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
        }

        $auditors = User::whereIn('id', $auditor_ids)->get();

        return $auditors;
    }

    public function amenity_inspections()
    {
        $amenities = \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('unit_id','=',$this->building->building_id)->whereNotNull('building_id')->get();

        return $amenities;
    }
}

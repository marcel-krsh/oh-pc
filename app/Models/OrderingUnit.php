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
     * Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedUnit::class, 'id', 'unit_id');
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
        // $this->building_id is the cachedbuilding id

        //dd($this->audit_id, $this->building_id, $this->id);
        // get all the auditors for that building/units in the building
        $auditor_ids = \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('unit_id','=',$this->unit->unit_id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();

        $auditors = User::whereIn('id', $auditor_ids)->get();

        return $auditors;
    }

    public function amenity_inspections()
    {
        $amenities = \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('unit_id','=',$this->unit->unit_id)->whereNotNull('unit_id')->get();

        return $amenities;
    }

    public function amenities_and_findings()
    {
        // total
        // name (with numbering)
        // link to findings modal
        
        // manage name duplicates, number them based on their id
        $amenity_names = array();
        $amenities = $this->amenity_inspections;

        foreach($amenities as $amenity){
            $amenity_names[$amenity->amenity->amenity_description][] = $amenity->amenity_inspection_id;
        }

        $output = array();
        foreach($amenities as $amenity){
            $key = array_search($amenity->amenity_inspection_id, $amenity_names[$amenity->amenity->amenity_description]);
            $key = $key + 1;
            $name = $amenity->amenity->amenity_description." ".$key;

            $output[] = '';
        }



    }
}

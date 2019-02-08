<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AmenityInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    protected $table = 'amenity_inspections';
    

    //
    protected $guarded = ['id'];

    public function amenity() : HasOne
    {
    	return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
    }
    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
    }

    public function cached_unit() : object
    {
        $cachedUnit = CachedUnit::where('unit_id',$this->unit_id)->where('audit_id',$this->audit_id)->first();

        return $cachedUnit;
    }

    public function building_inspection() : object
    {
        $buildingInspection = BuildingInspection::where('building_id',$this->building_id)->where('audit_id',$this->audit_id)->first();

        return $buildingInspection;
    }

    public function unit_has_multiple() : bool
    {
        $total = AmenityInspection::where('amenity_id',$this->amenity_id)->where('unit_id',$this->unit_id)->where('audit_id',$this->audit_id)->count();
        if($total > 1){
            return true;
        } else {
            return false;
        }
    }

    public function building_has_multiple() : bool
    {
        $total = AmenityInspection::where('amenity_id',$this->amenity_id)->where('building_id',$this->building_id)->where('audit_id',$this->audit_id)->count();
        if($total > 1){
            return true;
        } else {
            return false;
        }
    }
    public function project_has_multiple() : bool
    {
        $total = AmenityInspection::where('amenity_id',$this->amenity_id)->where('project_id',$this->project_id)->where('audit_id',$this->audit_id)->count();
        if($total > 1){
            return true;
        } else {
            return false;
        }
    }

    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'auditor_id');
    }

    
}

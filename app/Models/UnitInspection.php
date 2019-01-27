<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';//
    protected $guarded = ['id'];

    
    public function amenities() : HasMany
    {
        return $this->hasMany(\App\Models\UnitAmenity::class, 'unit_id', 'unit_id');
    }

    public function amenity_inspections() : HasMany
    {
        return $this->hasMany(\App\Models\AmenityInspection::class, 'unit_id', 'unit_id');
    }

    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
    }

    public function program() : HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'program_key', 'program_key');
    }

    public function hasSiteInspection()
    {
        if($this->is_site_visit){
            return 1;
        }elseif(\App\Models\UnitInspection::where('program_id', '=', $this->program_id)->where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit_id)->where('is_site_visit', '=', 1)->count()){
            return 1;
        }
        return 0;
    }

    public function hasFileInspection()
    {
        if($this->is_file_audit){
            return 1;
        }elseif(\App\Models\UnitInspection::where('program_id', '=', $this->program_id)->where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $this->unit_id)->where('is_file_audit', '=', 1)->count()){
            return 1;
        }
        return 0;
    }

    
}

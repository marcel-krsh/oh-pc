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

    public function building_inspection() 
    {
        $buildingInspection = BuildingInspection::where('building_id',$this->building_id)->where('audit_id',$this->audit_id)->first();

        //dd($buildingInspection);

        return $buildingInspection;
    }

    public function findings() : HasMany
    {
        return $this->hasMany('\App\Models\Finding', 'amenity_id');
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

    public function findings_total()
    {
        // either use the # in the row or calculate based on findings records in the db? 
        // using the row:
        $nlt_count = ($this->nlt_count) ? $this->nlt_count : 0;
        $lt_count = ($this->lt_count) ? $this->lt_count : 0;
        $file_count = ($this->file_count) ? $this->file_count : 0;
        $followup_count = ($this->followup_count) ? $this->followup_count : 0;

        return $nlt_count + $lt_count + $file_count + $followup_count;
    }

    public function amenity_type()
    {
        if($this->amenity){
            return $this->amenity->amenity_description;
        }else{
            return '';
        }
    }

    public function status()
    {
        /*
        if completed: green solid outline with the number of findings or a checkmark if no findings
        otherwise
        if follow-up within 24h: red double outline
        if follow-up later than 24h: purple dashed
        if finding and no follow-up, or follow-up done, blue dotted line
         */
        
        if($this->completed_date_time !== NULL){
            $status = "ok-actionable";
        }else{
            if(count($this->findings)){
                foreach ($this->findings as $finding) {
                    if($finding->has_followup_overdue()){
                        $status = "action-required"; break;
                    }elseif($finding->has_followup_within_24h()){
                        $status = "action-needed";
                    }
                }
            }else{
                $status = "ok-actionable";
            }
        }

        return $status;
    }
    
}

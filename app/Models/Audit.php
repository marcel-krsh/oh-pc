<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;

class Audit extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($audit) {
            Event::fire('audit.created', $audit);
        });

        static::updated(function ($audit) {
            Event::fire('audit.updated', $audit);
        });

        // static::deleted(function ($audit) {
        //     Event::fire('audit.deleted', $audit);
        // });
    }
    public function total_items() : int {
        // this is the total of project amenities, plus buildings, plus units
        // $total = 0;
        $total = $this->project->total_building_count;
        $total = $total + $this->project_amenity_inspections->count(); 
        $total = $total + $this->total_unique_unit_inspections();
        dd($this->project->total_building_count,$this->project_amenity_inspections->count(),$this->total_unique_unit_inspections());
        return  $total;

    }

    public function total_inspection_units(){
        return \App\UnitInspection::where('audit_id',$this->id)->groupBy('unit_id')->count();
    }

    public function project_details() : HasOne
    {
        return $this->hasOne(\App\Models\ProjectDetails::class, 'project_id', 'project_id')->where('audit_id',$this->id);
    }
    public function project(): HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id','project_id');
    }
    public function amenity_inspections() : HasMany {
       return $this->hasMany('\App\Models\AmenityInspection');
    }
    public function project_amenity_inspections() : HasMany {
       return $this->hasMany('\App\Models\AmenityInspection')->whereNull('building_id')->whereNull('unit_id');
    }
    public function unit_inspections() : HasMany {
       return $this->hasMany('\App\Models\UnitInspection');
    }
    public function total_unique_unit_inspections() : int {
       $total = \App\Models\UnitInspection::where('audit_id',$this->id)->select('unit_id')->groupBy('unit_id')->count();
       return $total;
    }
    public function nlts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->where('allita_type','nlt');
    }
    public function lts() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->where('allita_type','lt');
    }
    public function files() : HasMany
    {
        return $this->hasMany('\App\Models\Finding')->where('allita_type','file');
    }
    public function findings() : HasMany
    {
        return $this->hasMany('\App\Models\Finding');
    }
    public function ranCompliance(){
        $this->update(['compliance_run'=>1,'rerun_compliance'=>null]);
    }

}

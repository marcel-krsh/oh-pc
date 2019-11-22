<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Carbon;

class CachedBuilding extends Model
{
    protected $fillable = [
        'audit_id',
        'audit_key',
        'building_id',
        'building_name',
        'building_key',
        'project_id',
        'project_key',
        'amenity_id',
        'lead_id',
        'lead_key',
        'status',
        'type',
        'type_total',
        'type_text',
        'type_text_plural',
        'program_total',
        'finding_total',
        'finding_file_status',
        'finding_nlt_status',
        'finding_lt_status',
        'finding_sd_status',
        'finding_file_total',
        'finding_nlt_total',
        'finding_lt_total',
        'finding_sd_total',
        'finding_file_completed',
        'finding_nlt_completed',
        'finding_lt_completed',
        'finding_sd_completed',
        'address',
        'city',
        'state',
        'zip',
        'followup_date',
        'followup_description',
        'auditors_json',
        'amenities_json',
        'findings_json',
        'created_at',
        'updated_at',
        'amenity_inspection_id'
    ];

    public function amenity_inspection() : HasOne
    {
        // used when building-level amenity
        return $this->hasOne(\App\Models\AmenityInspection::class, 'id', 'amenity_inspection_id');
    }

    public function getAmenitiesJsonAttribute($value)
    {
        return json_decode($value);
    }

    public function getAuditorsJsonAttribute($value)
    {
        return json_decode($value);
    }

    public function getFindingsJsonAttribute($value)
    {
        return json_decode($value);
    }

    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
    }

    public function units() : HasManyThrough {
        return $this->hasManyThrough('App\Models\Unit', 'App\Models\Building', 'id', 'building_id', 'building_id', 'id');
    }

    public function is_amenity()
    {
        if(!$this->building_id){
            return true;
        }else{
            return false;
        }
    }

    public function recount_findings()
    {
        // fix total
        if(null !== $this){
            //$this->findingstotal();
            if($this->amenity_inspection_id == null){
                $unit_ids = $this->units()->pluck('units.id')->toArray();
                $building_id = $this->building_id;

                // fix finding type totals
                $total_nlt = \App\Models\Finding::where('audit_id','=',$this->audit_id)
                                                    ->where(function ($query) use ($building_id, $unit_ids) {
                                                        $query->where('building_id','=',$building_id)
                                                                ->orwhereIn('unit_id', $unit_ids);
                                                    })
                                                    ->whereHas('finding_type', function($query) {
                                                        $query->where('type', '=', 'nlt');
                                                    })->count();
                // fix the count
                //if($this->finding_nlt_total != $total_nlt){
                    $this->finding_nlt_total = $total_nlt;
                    
                //}

                $total_file = \App\Models\Finding::where('audit_id','=',$this->audit_id)
                                                    ->where(function ($query) use ($building_id, $unit_ids) {
                                                        $query->where('building_id','=',$building_id)
                                                                ->orwhereIn('unit_id', $unit_ids);
                                                    })
                                                    ->whereHas('finding_type', function($query) {
                                                        $query->where('type', '=', 'file');
                                                    })->count();

                //if($this->finding_file_total != $total_file){
                    $this->finding_file_total = $total_file;
                    
                //}

                $total_lt = \App\Models\Finding::where('audit_id','=',$this->audit_id)
                                                    ->where(function ($query) use ($building_id, $unit_ids) {
                                                        $query->where('building_id','=',$building_id)
                                                                ->orwhereIn('unit_id', $unit_ids);
                                                    })
                                                    ->whereHas('finding_type', function($query) {
                                                        $query->where('type', '=', 'lt');
                                                    })->count();

                //if($this->finding_lt_total != $total_lt){
                    $this->finding_lt_total = $total_lt;
                    $this->save();
                //}
            }else{
                /// different criteria
                
                $total_nlt = \App\Models\Finding::where('audit_id','=',$this->audit_id)
                                                    ->where('amenity_inspection_id',$this->amenity_inspection_id)
                                                    ->whereHas('finding_type', function($query) {
                                                        $query->where('type', '=', 'nlt');
                                                    })->count();
                
                $total_lt = \App\Models\Finding::where('audit_id','=',$this->audit_id)
                                                    ->where('amenity_inspection_id',$this->amenity_inspection_id)
                                                    ->whereHas('finding_type', function($query) {
                                                        $query->where('type', '=', 'lt');
                                                    })->count();
                $this->finding_nlt_total = $total_nlt;
                $this->finding_lt_total = $total_lt;
                $this->save();
            }
        }
    }

    public function findingstotal()
    {
        $current_finding_total = $this->finding_total;

        $building_id = $this->building_id;
        $unit_ids = $this->units()->pluck('units.id')->toArray();

        // is it a building?
        if($building_id){
            $building_findings = \App\Models\Finding::where('audit_id','=',$this->audit_id)->where('building_id','=',$building_id)->count();
            $unit_findings = \App\Models\Finding::where('audit_id','=',$this->audit_id)->whereIn('unit_id',$unit_ids)->count();

            $total = $building_findings + $unit_findings;
        }else{
            // it is an amenity
            if($this->amenity() === null) {
                //dd($this);
                // this happens when the amenity at the project level was deleted
                $total = 0;
            } else {
                $total = $this->amenity()->findings_total();
            }
        }

        // fix the count
        if($current_finding_total != $total){
            $this->finding_total = $total;
            $this->save();
        }

        return $total;
        
    }

    public function amenity()
    {
        // this only applies when working with a project-level amenity listed along other buildings in the cache
        return \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('id','=',$this->amenity_inspection_id)->first();
    }

    public function amenity_inspections()
    {
        return \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('building_id','=',$this->building_id)->whereNotNull('building_id')->get();
    }

    public function amenities_and_findings()
    {
        // total
        // name (with numbering)
        // link to findings modal
        // blue dotted outline if they need to be done, otherwise completed
        
        //$amenity_inspections = $this->amenity_inspections();

        // manage name duplicates, number them based on their id
        $amenity_names = array();
        $amenities = $this->amenity_inspections();

        if(count($amenities) == 0){
            return [];
        }

        foreach($amenities as $amenity){
            $amenity_names[$amenity->amenity->amenity_description][] = $amenity->id;
        }

        $output = array();
        $output_completed = array();
      
        foreach($amenities as $amenity){
            $key = array_search($amenity->id, $amenity_names[$amenity->amenity->amenity_description]);
            
            if($key > 0){
                $key = $key + 1;
                $name = $amenity->amenity->amenity_description." ".$key;
            }else{
                $name = $amenity->amenity->amenity_description;
            }

            $status = $amenity->status();

            if($amenity->completed_date_time !== NULL){
                $completed = 1;

                $output_completed[] = [
                    "id" => $amenity->id,
                    "findings_total" => $amenity->findings_total(),
                    "name" => $name,
                    "status" => $status,
                    "completed" => $completed
                ];
            }else{
                $completed = 0;

                $output[] = [
                    "id" => $amenity->id,
                    "findings_total" => $amenity->findings_total(),
                    "name" => $name,
                    "status" => $status,
                    "completed" => $completed
                ];
            }

            
        }

        // prioritize not completed amenities
        foreach($output_completed as $o){
            $output[] = $o;
        }
        
        $output = array_filter($output); // remove empty elements
        return $output;
    }
}

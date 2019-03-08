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

    public function amenity()
    {
        // this only applies when working with a project-level amenity listed along other buildings in the cache
        return \App\Models\AmenityInspection::where('audit_id','=',$this->audit_id)->where('amenity_id','=',$this->amenity_id)->where('cachedbuilding_id','=',$this->id)->first();
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

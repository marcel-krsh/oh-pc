<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Auth;

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

    public function cached_unit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedUnit::class, 'unit_id', 'unit_id');
    }

    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
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

    public function swap_remove(Audit $audit)
    {
        // only remove the cached information if both site and file are removed
        //dd($audit->id, $this->unit_id);
        $check_if_not_last_one = UnitInspection::where('audit_id','=',$audit->id)
                                    ->where('unit_id','=',$this->unit_id)
                                    ->where(function ($query) {
                                        $query->where('is_site_visit', '=', 1)
                                              ->orWhere('is_file_audit', '=', 1);
                                    })->count();

        if($check_if_not_last_one == 0){
            $cached_unit = CachedUnit::where('audit_id','=',$audit->id)->where('unit_id','=',$this->unit_id)->first();

            $cached_building = CachedBuilding::where('audit_id','=',$audit->id)->where('building_id','=',$cached_unit->building_id)->first();

            // unit_id in OrderingUnit is cached_unit_id...
            $ordering_unit = OrderingUnit::where('audit_id','=',$audit->id)->where('building_id','=',$cached_unit->building_id)->where('unit_id','=',$cached_unit->id)->delete();

            $new_type_total = $cached_building->type_total - 1;

            $cached_unit->delete();

            $cached_building->update([
                'type_total' => $new_type_total
            ]);
        }

        // regardless, we need to remove that record
        $this->delete();

    }

    public function swap_add(Audit $audit)
    {
        // we may be running this several times, so we have to check that the record doesn't already exists for that unit
        if(CachedUnit::where('audit_id','=',$audit->id)->where('unit_id','=',$this->unit_id)->count() == 0){

            // insert unit amenities
            foreach($this->amenities as $ua){
               AmenityInspection::insert([
                    'audit_id'=>$audit->id,
                    'monitoring_key'=>$audit->monitoring_key,
                    'unit_key'=>$this->unit_key,
                    'unit_id'=>$this->unit_id,
                    'amenity_id'=>$ua->amenity_id,
                    'amenity_key'=>$ua->amenity->amenity_key,

               ]);
            }

            $unit_amenities = AmenityInspection::where('unit_id',$this->unit_id)->with('amenity')->get();
            //dd($unit_amenities, $this->unit_id);
            $uaCount = 0;
            //Unit amenity json:
            //[{"id": "295", "qty": "2", "type": "Elevator", "status": "pending"},]
            $uaJson = array();
            forEach($unit_amenities as $ua){
                if($ua->amenity->inspectable){
                    $uaCount++;
                    // if($jsonRun == 1){
                    //     $uaJson .= ' , ';
                    //     //insert comma between groups
                    // }
                    $jsonRun = 1;

                    $uaJson[] = [
                        "id" => $ua->amenity_id,
                        "qty" => "0",
                        "type" => addslashes($ua->amenity->amenity_description),
                        "status" => "",
                        "common_area" => $ua->common_area,
                        "project" => $ua->project,
                        "building_system" => $ua->building_system,
                        "building_exterior" => $ua->building_exterior,
                        "unit" => $ua->unit,
                        "file" => $ua->file
                    ];

                }
            }

            $jsonRun = 0;

            $cached_unit = new CachedUnit([
                'audit_id' => $audit->id,
                'audit_key' => $audit->monitoring_key,
                'project_id' => $this->project_id,
                'project_key' => $this->project_key,
                'amenity_id' => null,
                'building_id' => $this->building_id,
                'building_key' => $this->building_key,
                'status' => null,
                'type' => 'amenity',
                'type_total' => $uaCount,
                'type_text' => 'AMENITY',
                'type_text_plural' => 'AMENITIES',
                'program_total' => null,
                'finding_total' => 0,
                'finding_file_status' => '',
                'finding_nlt_status' => '',
                'finding_lt_status' => '',
                'finding_sd_status' => '',
                'finding_file_total' => '0',
                'finding_nlt_total' => '0',
                'finding_lt_total' => '0',
                'finding_sd_total' => '0',
                'finding_file_completed' => '0',
                'finding_nlt_completed' => '0',
                'finding_lt_completed' => '0',
                'finding_sd_completed' => '0',
              //  'followup_date' => '',
                'address' => $this->unit->building->address->line_1,
                'city' => $this->unit->building->address->city,
                'state' => $this->unit->building->address->state,
                'zip' => $this->unit->building->address->zip,
                'auditors_json' => null,
                'amenities_json' => json_encode($uaJson),
                'unit_id'=>$this->unit->id,
                'unit_key'=>$this->unit->unit_key,
                'unit_name'=>$this->unit->unit_name
            ]);
            $cached_unit->save();

            $cached_building = CachedBuilding::where('audit_id','=',$audit->id)->where('building_id','=',$cached_unit->building_id)->first();

            $new_type_total = $cached_building->type_total + 1;

            $cached_building->update([
                'type_total' => $new_type_total
            ]);
        }
    }

    public function auditors($audit_id)
    {
          $auditor_ids = \App\Models\AmenityInspection::where('audit_id','=',$audit_id)->where('unit_id','=', $this->unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
          if($auditor_ids)
          $auditors = User::whereIn('id', $auditor_ids)->get();
          else
          $auditors = null;

      return $auditors;
    	return $this->hasOne(\App\Models\OrderingUnit::class, 'unit_id', 'unit_id')->orderBy('id', 'desc');
    }


}

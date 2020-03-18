<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BuildingInspection extends Model
{
  public $timestamps = true;
  //protected $dateFormat = 'Y-m-d\TH:i:s.u';

  public function amenities(): HasMany
  {
    return $this->hasMany(\App\Models\BuildingAmenity::class, 'building_id', 'building_id');
  }

  public function documents(): HasMany
  {
    return $this->hasMany(\App\Models\Document::class, 'building_id', 'building_id')->where('audit_id',$this->audit_id);
  }

  public function building(): HasOne
  {
    return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
  }

  public function order_building()
  {
    return $this->hasOne(\App\Models\OrderingBuilding::class, 'building_id', 'building_id')->orderBy('id', 'desc');
  }

  //
  protected $guarded = ['id'];

  public function auditors($audit_id)
  {
    if ($this->building) {
      $auditor_ids = \App\Models\AmenityInspection::where('audit_id', $audit_id)->where('building_id', '=', $this->building->id)->whereNotNull('auditor_id')->whereNotNull('building_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
      $auditor_unit_ids = [];
      $auditor_unit_ids = Unit::where('building_id', '=', $this->building->id)->pluck('id');
      $auditor_ids = \App\Models\AmenityInspection::where('audit_id', '=', $this->audit_id)->whereIn('unit_id', $auditor_unit_ids)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
      //opitmize this!
      // foreach ($units as $unit) {
      //   $auditor_unit_ids = array_merge($auditor_unit_ids, \App\Models\AmenityInspection::where('audit_id', '=', $this->audit_id)->where('unit_id', '=', $unit->id)->whereNotNull('unit_id')->whereNotNull('auditor_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray());
      // }

      // $auditor_ids = array_merge($auditor_ids, $auditor_unit_ids);
    } else {
      $auditor_ids = \App\Models\AmenityInspection::where('audit_id', '=', $this->audit_id)->where('amenity_id', '=', $this->amenity_id)->whereNotNull('auditor_id')->whereNull('building_id')->select('auditor_id')->groupBy('auditor_id')->get()->toArray();
    }

    $auditors = User::whereIn('id', $auditor_ids)->get();

    return $auditors;
  }
}

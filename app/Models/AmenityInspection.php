<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;


class AmenityInspection extends Model
{
  public $timestamps = false;
  //protected $dateFormat = 'Y-m-d\TH:i:s.u';
  protected $table = 'amenity_inspections';

  function getUpdatedAtAttribute($value)
  {
  	return milliseconds_mutator($value);
  }
  function getCompletedDateTimeAttribute($value)
  {
  	return milliseconds_mutator($value);
  }

  //
  protected $guarded = ['id'];

  public static function boot()
  {
    parent::boot();
    static::created(function ($amenity) {
      Event::listen('amenity.created', $amenity);
    });
     static::updated(function ($amenity) {
      Event::listen('amenity.updated', $amenity);
    });
    static::deleted(function ($amenity) {
      Event::listen('amenity.deleted', $amenity);
    });
  }

  public function amenity(): HasOne
  {
    return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
  }

  public function cached_audit(): HasOne
  {
    return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
  }

  public function unit(): HasOne
  {
    return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
  }

  public function project(): HasOne
  {
    return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
  }

  public function unit_programs(): HasMany
  {

    return $this->hasMany(\App\Models\UnitProgram::class, 'unit_id', 'unit_id');
  }

  public function cached_unit():  ? CachedUnit
  {
    $cachedUnit = CachedUnit::where('unit_id', $this->unit_id)->where('audit_id', $this->audit_id)->first();

    return $cachedUnit;
  }

  public function building() : HasOne
  {
    return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
  }

  public function cached_building(): CachedBuilding
  {
    $cachedBuilding = CachedBuilding::where('building_id', $this->building_id)->where('audit_id', $this->audit_id)->first();

    return $cachedBuilding;
  }

  public function building_inspection()
  {
    $buildingInspection = BuildingInspection::where('building_id', $this->building_id)->where('audit_id', $this->audit_id)->first();

    //dd($buildingInspection);

    return $buildingInspection;
  }

  public function findings(): HasMany
  {
    return $this->hasMany('\App\Models\Finding', 'amenity_id');
  }

  public function building_unit_amenity_names()
  {
    if ($this->unit_id) {
      //$unit_name = $this->unit->unit_name;
      //$building_name = $this->unit->building->building_name;
      $amenity_name = $this->amenity->amenity_description;
      //return $building_name .":". $unit_name . ":" .$amenity_name;
      return $amenity_name;
    } elseif ($this->building_id) {

      //$building_name = $this->building->building_name;
      $amenity_name = $this->amenity->amenity_description;
      //return $building_name . ":" .$amenity_name;
      return $amenity_name;
    } elseif ($this->project_id) {

      $amenity_name = $this->amenity->amenity_description;
      return $amenity_name;
    }

    return '';
  }

  public function building_unit_name()
  {
    if ($this->unit_id) {
      $unit_name     = $this->unit->unit_name;
      $building_name = $this->unit->building->building_name;
      return $building_name . ":" . $unit_name;
    } elseif ($this->building_id) {

      $building_name = $this->building->building_name;
      return $building_name;
    }

    return '';
  }

  public function address()
  {
    if ($this->unit_id) {
      if ($this->unit->building->address) {
        $address = $this->unit->building->address->formatted_address();
        return $address;
      } else {
        return "NO ADDRESS IN DEVCO";
      }
    } elseif ($this->building_id) {
      if ($this->building->address) {
        $address = $this->building->address->formatted_address();
        return $address;
      } else {
        return "NO ADDRESS IN DEVCO";
      }
    } elseif ($this->project_id) {

      if ($this->project->address) {
        $address = $this->project->address->formatted_address();

        return $address;
      } else {
        return "NO ADDRESS IN DEVCO";
      }
    }

    return '';
  }

  public function unit_has_multiple(): bool
  {
    $total = AmenityInspection::where('amenity_id', $this->amenity_id)->where('unit_id', $this->unit_id)->where('audit_id', $this->audit_id)->count();
    if ($total > 1) {
      return true;
    } else {
      return false;
    }
  }

  public function building_has_multiple(): bool
  {
    $total = AmenityInspection::where('amenity_id', $this->amenity_id)->where('building_id', $this->building_id)->where('audit_id', $this->audit_id)->count();
    if ($total > 1) {
      return true;
    } else {
      return false;
    }
  }

  public function project_has_multiple(): bool
  {
    $total = AmenityInspection::where('amenity_id', $this->amenity_id)->where('project_id', $this->project_id)->where('audit_id', $this->audit_id)->count();
    if ($total > 1) {
      return true;
    } else {
      return false;
    }
  }

  public function user(): HasOne
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'auditor_id');
  }

  public function findings_total()
  {
    // either use the # in the row or calculate based on findings records in the db?
    // using the row:
    // $nlt_count = ($this->nlt_count) ? $this->nlt_count : 0;
    // $lt_count = ($this->lt_count) ? $this->lt_count : 0;
    // $file_count = ($this->file_count) ? $this->file_count : 0;
    // $followup_count = ($this->followup_count) ? $this->followup_count : 0;
    // return $nlt_count + $lt_count + $file_count + $followup_count;

    // calculating
    return \App\Models\Finding::where('audit_id', '=', $this->audit_id)->where('amenity_inspection_id', '=', $this->id)->count();
  }

  public function amenity_type()
  {
    if ($this->amenity) {
      return $this->amenity->amenity_description;
    } else {
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

    if (null !== $this->completed_date_time) {
      $status = "ok-actionable";
    } else {
      if (count($this->findings)) {
        foreach ($this->findings as $finding) {
          if ($finding->has_followup_overdue()) {
            $status = "action-required";
            break;
          } elseif ($finding->has_followup_within_24h()) {
            $status = "action-needed";
          }
        }
      } else {
        $status = "pending";
      }
    }

    return $status;
  }
}

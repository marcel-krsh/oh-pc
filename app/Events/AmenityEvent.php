<?php

namespace App\Events;

use App\Models\AmenityInspection;
use App\Models\BuildingInspection;
use App\Models\UnitInspection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class AmenityEvent
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  public function amenityCreated(AmenityInspection $amenity)
  {
    /*
    Check which property is this amenity added (unit, building or site)
    Mark that particular property as incomplete, as new amenity is added
    Unit Level
    Unitinspections - mark complete = 0
     */
    $units = [];
    if (!is_null($amenity->unit_id)) {
      $units = UnitInspection::where('audit_id', $amenity->audit_id)
        ->where('unit_id', $amenity->unit_id)
        ->get();
      foreach ($units as $key => $unit) {
        $unit->complete = 0;
        $unit->save();
      }
    }
  }

  public function amenityDeleted(AmenityInspection $amenity)
  {
    /*
    Check which property is this amenity added (unit, building or site)
    Fetch all the amenities associated with this property
    Check if all the amenities are completed
    If yes, mark this property as complete = 1
    If no, mark this as complete = 2
     */
    $units = [];
    if (!is_null($amenity->unit_id)) {
      $unit_inspections = AmenityInspection::where('unit_id', $amenity->unit_id)->where('audit_id', $amenity->audit_id)->whereNull('completed_date_time')->count();
      $units            = UnitInspection::where('audit_id', $amenity->audit_id)
        ->where('unit_id', $amenity->unit_id)
        ->get();
      if ($unit_inspections > 0) {
        foreach ($units as $key => $unit) {
          $unit->complete = 0;
          $unit->save();
        }
      } else {
        foreach ($units as $key => $unit) {
          $unit->complete = 1;
          $unit->save();
        }
      }
    }
  }

  public function amenityUpdated(AmenityInspection $amenity)
  {
    /*
    Check which property is this amenity updated (unit, building or site)
    Fetch all the amenities associated with this property
    Check if all the amenities are completed
    If yes, mark this property as complete = 1
    If no, mark this as complete = 2
     */
    $units = [];
    //Log::info($amenity);
    if (!is_null($amenity->unit_id)) {
      $unit_inspections = AmenityInspection::where('unit_id', $amenity->unit_id)->where('audit_id', $amenity->audit_id)->whereNull('completed_date_time')->count();
      $units            = UnitInspection::where('audit_id', $amenity->audit_id)
        ->where('unit_id', $amenity->unit_id)
        ->get();
      if (count($units) > 0) {
        $building_id               = $units->first()->building_id;
        $unit_ids                  = UnitInspection::where('audit_id', $amenity->audit_id)->where('building_id', '=', $building_id)->pluck('unit_id');
        $amenity_inspections_unit  = AmenityInspection::where('audit_id', '=', $amenity->audit_id)->whereIn('unit_id', $unit_ids)->whereNull('completed_date_time')->get();
        $amenity_inspections_build = AmenityInspection::where('audit_id', '=', $amenity->audit_id)->where('building_id', '=', $building_id)->whereNull('unit_id')->whereNull('completed_date_time')->get();
        $amenity_inspections       = $amenity_inspections_build->merge($amenity_inspections_unit);
        $amenity_inspections       = $amenity_inspections->count();
        $buildings                 = BuildingInspection::where('audit_id', $amenity->audit_id)->where('building_id', $building_id)->get();
        Log::info('unit - ' . $amenity_inspections_unit->count());
        Log::info('building - ' . $amenity_inspections_build->count());
        Log::info('ins - ' . $amenity_inspections);
        if ($unit_inspections > 0) {
          foreach ($units as $key => $unit) {
            $unit->complete = 0;
            $unit->save();
          }
        } else {
          foreach ($units as $key => $unit) {
            $unit->complete = 1;
            $unit->save();
          }
        }
        if ($amenity_inspections > 0) {
          foreach ($buildings as $key => $building) {
            $building->complete = 0;
            $building->save();
          }
        } else {
          foreach ($buildings as $key => $building) {
            $building->complete = 1;
            $building->save();
          }
        }
      }
    } elseif (!is_null($amenity->building_id)) {
      $building_id               = $amenity->building_id;
      $unit_ids                  = UnitInspection::where('audit_id', $amenity->audit_id)->where('building_id', '=', $amenity->building_id)->pluck('unit_id');
      $amenity_inspections_unit  = AmenityInspection::where('audit_id', '=', $amenity->audit_id)->whereIn('unit_id', $unit_ids)->whereNull('completed_date_time')->get();
      $amenity_inspections_build = AmenityInspection::where('audit_id', '=', $amenity->audit_id)->where('building_id', '=', $building_id)->whereNull('unit_id')->whereNull('completed_date_time')->get();
      $amenity_inspections       = $amenity_inspections_build->merge($amenity_inspections_unit);
      $amenity_inspections       = $amenity_inspections->count();

      // $units = UnitInspection::where('audit_id', $amenity->audit_id)
      //    ->whereIn('unit_id', $unit_ids)
      //    ->get();
      $buildings = BuildingInspection::where('audit_id', $amenity->audit_id)->where('building_id', $amenity->building_id)->get();
      Log::info('building - ' . $buildings->first()->building_id);
      Log::info('count = ' . $amenity_inspections);

      // Log::info('unit_ids' . $unit_ids);
      // Log::info('build' .$amenity->building_id);
      // Log::info('ins' .$amenity_inspections);
      // Log::info('sql: ' . $amenity_inspections->toSql());
      if ($amenity_inspections > 0) {
        foreach ($buildings as $key => $building) {
          $building->complete = 0;
          $building->save();
        }
      } else {
        foreach ($buildings as $key => $building) {
          $building->complete = 1;
          $building->save();
        }
      }
    }
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('channel-name');
  }
}

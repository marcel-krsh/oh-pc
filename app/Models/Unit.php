<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    public function household() : HasOne
    {
        return $this->hasOne(\App\Models\Household::class, 'unit_id', 'unit_id');
    }

    public function unitBedroom() : HasOne
    {
        return $this->hasOne(\App\Models\UnitBedroom::class, 'id', 'unit_bedroom_id');
    }

    public function bedroomCount() : int
    {
        return $this->unitBedroom->unit_bedroom_number;
    }

    public function building() : HasOne
    {
        return $this->hasOne('\App\Models\Building');
    }

    public function project_id() : int
    {
        return $this->building->project_id;
    }

    public function household_events() : HasMany
    {
        return $this->hasMany(\App\Models\HouseholdEvent::class, 'unit_id', 'unit_id');
    }

    public function isAssistedUnit() : bool
    {
        foreach ($this->household_events()->get() as $event) {
            if ($event->rental_assistance_amount > 0) {
                return true;
            }
        }
        return false;
    }

    public function programs() : HasMany
    {
        return $this->hasMany(\App\Models\UnitProgram::class, 'unit_id', 'unit_id');
    }
}

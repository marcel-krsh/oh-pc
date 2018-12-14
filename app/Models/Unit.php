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
        return $this->hasOne(\App\Models\Household::class, 'unit_key', 'unit_key');
    }

    public function household_events() : HasMany
    {
    	return $this->hasMany(\App\Models\HouseholdEvent::class, 'unit_key', 'unit_key');
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
}

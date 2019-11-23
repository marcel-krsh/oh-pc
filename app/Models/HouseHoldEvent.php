<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HouseholdEvent extends Model
{
    // public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];
    public $timestamps = false;

    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }

    public function type() : HasOne
    {
        return $this->hasOne(\App\Models\EventType::class, 'event_type_key','event_type_key');
    }
    public function rent_assistance_type() : HasOne
    {
        return $this->hasOne(\App\Models\RentalAssistanceType::class, 'id','rental_assistance_type_id');
    }
    public function rent_level() : HasOne
    {
        return $this->hasOne(\App\Models\RentLevel::class, 'id','rent_level_id');
    }
    public function income_level() : HasOne
    {
        return $this->hasOne(\App\Models\RentLevel::class, 'id','income_level_id');
    }
}

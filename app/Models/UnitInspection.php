<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    
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

    //
    protected $guarded = ['id'];
}

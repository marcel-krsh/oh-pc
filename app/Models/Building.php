<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Building extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    

    //
    protected $guarded = ['id'];

    /**
     * Units
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function units() : HasMany
    {
        return $this->hasMany(\App\Models\Unit::class, 'building_id', 'id');
    }

    public function amenities() : HasMany
    {
        return $this->hasMany(\App\Models\BuildingAmenity::class, 'building_id', 'id');
    }

    public function address() : HasOne
    {
        return $this->hasOne(\App\Models\Address::class, 'id', 'physical_address_id');
    }
    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }
}

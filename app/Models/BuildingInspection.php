<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BuildingInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    public function amenities() : HasMany
    {
        return $this->hasMany(\App\Models\BuildingAmenity::class, 'building_id', 'building_id');
    }

    public function building() : HasOne
    {
    	return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
    }

    public function order_building()
    {
    	return $this->hasOne(\App\Models\OrderingBuilding::class, 'building_id', 'building_id')->orderBy('id', 'desc');
    }

    //
    protected $guarded = ['id'];

}

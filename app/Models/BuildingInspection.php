<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    public function amenities()
    {
        return $this->hasMany(\App\Models\BuildingAmenity::class, 'building_id', 'building_id');
    }

    //
    protected $guarded = ['id'];
}

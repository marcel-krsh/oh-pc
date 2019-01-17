<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    
    public function amenities()
    {
        return $this->hasMany(\App\Models\UnitAmenity::class, 'unit_id', 'unit_id');
    }

    public function unit()
    {
        return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
    }

    //
    protected $guarded = ['id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;


class UnitAmenity extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    use SoftDeletes;

    public function amenity() : HasOne
    {
        return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
    }
}

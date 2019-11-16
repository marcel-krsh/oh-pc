<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAmenity extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    use SoftDeletes;

    protected $guarded = ['id'];

    public function amenity() : HasOne
    {
        return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
    }
}

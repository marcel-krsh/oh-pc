<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;


class ProjectAmenity extends Model
{
    // public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    use SoftDeletes;

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
    public function amenity() : HasOne
    {
        return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
    }
}

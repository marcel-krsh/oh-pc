<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AmenityInspection extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    protected $table = 'amenity_inspections';
    

    //
    protected $guarded = ['id'];

    public function amenity() : HasOne
    {
    	return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
    }

    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'auditor_id');
    }

    
}

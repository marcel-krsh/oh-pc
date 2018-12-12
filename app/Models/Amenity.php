<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Amenity extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $table = 'amenities';
    protected $guarded = ['id'];

    /**
     * Building
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
    }

    /**
     * Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    /**
     * Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
    }

}

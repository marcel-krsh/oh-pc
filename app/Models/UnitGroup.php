<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitGroup extends Model
{
    // public $timestamps = true;
    protected $table = 'unit_group';
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];
    public $timestamps = false;

    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }

    public function unit() : HasOne
    {
        return $this->hasOne(\App\Models\Unit::class, 'unit_id', 'unit_id');
    }
}
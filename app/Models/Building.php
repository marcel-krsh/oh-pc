<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->hasMany(\App\Models\Unit::class, 'building_key', 'building_key');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnitProgram extends Model
{
    public $timestamps = true;
	//protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $guarded = ['id'];

    public function program() : HasOne
    {
    	return $this->hasOne(\App\Models\Program::class, 'program_key', 'program_key');
    }

    public function unit() : HasOne
    {
    	return $this->hasOne(\App\Models\Unit::class, 'unit_key', 'unit_key');
    }

    public function audit() : HasOne
    {
    	return $this->hasOne(\App\Models\Audit::class, 'monitoring_key', 'monitoring_key');
    }
}

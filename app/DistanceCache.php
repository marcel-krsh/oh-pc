<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DistanceCache extends Model
{
    public $timestamps = true;

    public function start(): HasOne {
    	return $this->hasOne(\App\Models\Address::class, 'id', 'starting_address_id');
    }
    public function end(): HasOne {
    	return $this->hasOne(\App\Models\Address::class, 'id', 'ending_address_id');
    }
}

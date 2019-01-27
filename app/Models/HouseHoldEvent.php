<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HouseholdEvent extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    public function type() : HasOne
    {
        return $this->hasOne(\App\Models\EventType::class, 'event_type_key','event_type_key');
    }
}

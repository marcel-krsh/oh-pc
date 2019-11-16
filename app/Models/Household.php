<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Household extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    public function household_size() : HasOne
    {
        return $this->hasOne(\App\Models\HouseholdSize::class, 'id', 'household_size_id');
    }

    public function move_in_household_size() : HasOne
    {
        return $this->hasOne(\App\Models\HouseholdSize::class, 'id', 'household_size_move_in_id');
    }

    public function special_needs() : HasOne
    {
        return $this->hasOne(\App\Models\SpecialNeed::class, 'id', 'special_needs_id');
    }
}

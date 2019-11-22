<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * County Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class County extends Model
{
    /**
     * State
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state() : BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Target Areas
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function targetAreas() : HasMany
    {
        return $this->hasMany(TargetArea::class, 'county_id');
    }

    /**
     * Programs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function programs() : HasMany
    {
        return $this->hasMany(Program::class);
    }
}

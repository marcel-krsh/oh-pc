<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * State Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class State extends Model
{
    /**
     * Counties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function counties() : HasMany
    {
        $this->hasMany(County::class, 'state_id');
    }

    /**
     * Entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entity() : HasMany
    {
        return $this->hasMany(\App\Models\Entity::class);
    }
}

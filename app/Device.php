<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Device Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Device extends Model
{
    /**
     * Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users() : HasMany
    {
        return $this->hasMany(\App\VisitLists::class, 'device_id', 'device_id');
    }

    /**
     * Wipe User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wipeUser() : HasOne
    {
        return $this->hasOne(\App\User::class, 'id', 'wiped_by');
    }
}

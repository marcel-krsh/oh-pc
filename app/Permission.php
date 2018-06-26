<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Permission Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Permission extends Model
{
    /**
     * Roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}

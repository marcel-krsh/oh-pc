<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Role extends Model
{
    /**
     * Permissions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class, "roles_and_permissions");
    }

    /**
     * Give Permission To
     *
     * @param \App\Permission $permission
     */
    public function givePermissionTo(Permission $permission)
    {
        $this->permissions()->save($permission);
    }
}

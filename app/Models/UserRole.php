<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * UserRole Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class UserRole extends Model
{
	protected $table = 'users_roles';

    protected static $logAttributes = [
        'role_id',
        'user_id'
    ];

    protected $fillable = [
        'role_id',
        'user_id'
    ];

    public function role() : HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}

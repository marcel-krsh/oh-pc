<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * UserRole Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class UserRole extends Model
{
    protected static $logAttributes = [
        'role_id',
        'user_id'
    ];
}

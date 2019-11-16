<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use User;

/**
 * Notice Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Notice extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'read',
        'body',
        'id',
        'owner_id',
    ];
}

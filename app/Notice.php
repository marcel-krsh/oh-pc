<?php

namespace App;

use User;

use Illuminate\Database\Eloquent\Model;

/**
 * Notice Model
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
        'owner_id'
    ];
}

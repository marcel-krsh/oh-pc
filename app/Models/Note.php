<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Note Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Note extends Model
{
    protected $fillable = [
        'note',
        'user_id',
        'project_id'
    ];

    /**
     * Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

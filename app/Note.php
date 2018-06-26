<?php

namespace App;

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
        'owner_id',
        'parcel_id'
    ];

    /**
     * Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
}

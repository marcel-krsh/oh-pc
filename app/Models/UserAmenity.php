<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * UserRole Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class UserAmenity extends Model
{
    protected $fillable = [
        'amenity_id',
        'user_id',
        'is_lead',
    ];

    /**
     * User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    /**
     * Amenity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function amenity() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAmenity::class, 'id', 'amenity_id');
    }
}

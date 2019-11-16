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
class UserAudit extends Model
{
    protected $fillable = [
        'audit_id',
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
     * Audit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAmenity::class, 'id', 'audit_id');
    }
}

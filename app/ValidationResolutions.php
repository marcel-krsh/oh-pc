<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ValidationResolutions Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ValidationResolutions extends Model
{
    protected $table = 'validation_resolutions';

    protected $fillable = [
        'parcel_id',
        'resolution_type',
        'resolution_id',
        'resolution_lb_notes',
        'resolution_system_notes',
        'resolution_hfa_notes',
        'lb_resolved',
        'lb_resolved_at',
        'hfa_resolved',
        'hfa_resolved_at',
        'requires_hfa_resolution'
    ];

    /**
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parcel() : HasOne
    {
        return $this->hasOne(\App\Parcel::class, 'id', 'parcel_id');
    }

    /**
     * Has Parcel
     *
     * @return bool
     */
    public function hasParcel()
    {
        if ($this->parcel()->count() > 0) {
            return true;
        }
        return false;
    }
}

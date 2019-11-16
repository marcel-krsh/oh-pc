<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * AmenityHud Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class AmenityHud extends Pivot
{
    protected $table = 'amenity_hud';

    protected $fillable = [
        'amenity_id',
        'hud_inspectable_area_id',
    ];

    /**
     * Amenity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function amenity() : HasOne
    {
        return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
    }

    /**
     * Hud Inspectable Area.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hud() : HasOne
    {
        return $this->hasOne(\App\Models\HudInspectableArea::class, 'id', 'hud_inspectable_area_id');
    }
}

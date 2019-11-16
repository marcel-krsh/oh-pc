<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * HudInspectableArea Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class HudInspectableArea extends Model
{
    protected $table = 'hud_inspectable_areas';

    protected $fillable = [
        'name',
        'site',
        'building_system',
        'building_exterior',
        'common_area',
        'unit',
        'file',
    ];

    /**
     * finding_types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finding_types() : HasMany
    {
        return $this->hasMany(HudFindingType::class, 'hud_inspectable_area_id', 'id');
    }

    /**
     * Amenities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function amenities() : HasMany
    {
        return $this->hasMany(AmenityHud::class, 'hud_inspectable_area_id', 'id');
    }
}

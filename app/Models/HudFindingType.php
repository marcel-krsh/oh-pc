<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * HudFindingType Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class HudFindingType extends Model
{
    protected $table = 'hud_finding_type';

    protected $fillable = [
        'hud_inspectable_area_id',
        'finding_type_id'
    ];

    /**
     * Finding Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function finding_type() : HasOne
    {
        return $this->hasOne(\App\Models\FindingType::class, 'id', 'finding_type_id');
    }

    /**
     * Hud Inspectable Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hud() : HasOne
    {
        return $this->hasOne(\App\Models\HudInspectableArea::class, 'id', 'hud_inspectable_area_id');
    }
}

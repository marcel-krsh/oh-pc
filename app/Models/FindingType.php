<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * FindingType Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class FindingType extends Model
{
    protected $table = 'finding_types';

    protected $fillable = [
        'name',
        'nominal_item_weight',
        'criticality',
        'one',
        'two',
        'three',
        'type',
        'site',
        'building_exterior',
        'building_system',
        'common_area',
        'unit'
    ];

    /**
     * Boilerplates
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function boilerplates() : HasMany
    {
        return $this->hasMany(FindingTypeBoilerplate::class, 'finding_type_id', 'id');
    }

    /**
     * HUDS
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function huds() : HasMany
    {
        return $this->hasMany(HudFindingType::class, 'finding_type_id', 'id');
    }

    /**
     * Default Followups
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function default_followups() : HasMany
    {
        return $this->hasMany(DefaultFollowup::class, 'finding_type_id', 'id');
    }
}

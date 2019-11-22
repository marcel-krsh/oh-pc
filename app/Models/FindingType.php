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
        'one_description',
        'two_description',
        'three_description',
        'type',
        'site',
        'building_exterior',
        'building_system',
        'common_area',
        'unit',
        'file'
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
    public function huds() 
    {
        $huds = HudInspectableArea::select('hud_inspectable_areas.*')->join('hud_finding_type','hud_inspectable_area_id', '=', 'hud_inspectable_areas.id')->where('hud_finding_type.finding_type_id',$this->id)->get();
        return $huds;
    }

    /**
     * AMENITIES
     *
     * @return object
     */
    public function amenities() : object
    {
        $amenities = Amenity::select('amenities.*') //select Amenity Info
            ->join('amenity_hud','amenities.id','amenity_id') // get the related huds
            ->join('hud_finding_type','amenity_hud.hud_inspectable_area_id','hud_finding_type.hud_inspectable_area_id') // get the related findings
            ->where('hud_finding_type.finding_type_id',$this->id) // filter to just those with this finding
            ->get();
        return $amenities;
    }


    /**
     * Default Followups
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function default_follow_ups() : HasMany
    {
        return $this->hasMany(DefaultFollowup::class, 'finding_type_id', 'id');
    }
}

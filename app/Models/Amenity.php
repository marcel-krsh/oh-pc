<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Amenity extends Model
{
    // public $timestamps = false;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $table = 'amenities';
    protected $guarded = ['id'];
    public $timestamps = false;

    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }

     /**
     * hud
     */

    public function huds() : HasMany {
        return $this->hasMany('App\Models\AmenityHud');

    }

    /**
     * finding types
     */
    public function finding_types()
    {
        $finding_types = FindingType::
            join('hud_finding_type', 'finding_types.id', '=', 'hud_finding_type.finding_type_id')
            ->join('amenity_hud', 'hud_finding_type.hud_inspectable_area_id', '=', 'amenity_hud.hud_inspectable_area_id')
            ->select('finding_types.*')
            ->where('amenity_hud.amenity_id',$this->id)
            ->groupBY('finding_types.id')
            ->get();

        //dd($finding_types,$this->id);

        return $finding_types;
    }
}

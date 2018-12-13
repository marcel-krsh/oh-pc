<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;

class Amenity extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $table = 'amenities';
    protected $guarded = ['id'];

     /**
     * hud
     */
    public function huds() : HasManyThrough
    {
        return $this->belongsToMany('App\Models\HudInspectionArea', 'amenity_hud', 'amenity_id', 'hud_inspection_area_id')->withPivot([
                            'created_by',
                            'updated_by'
                        ]);
    }

    /**
     * finding types
     */
    public function finding_types() 
    {
        $finding_types = \DB::table('finding_types')
            ->join('amenity_hud','finding_types.id','=','amenity_hud.amenity_id')
            ->join('hud_inspectable_areas','amenity_hud.hud_inspectable_area_id','=','hud_inspectable_areas.id')
            ->join('hud_finding_type','hud_inspectable_areas.id','=','hud_finding_type.hud_inspectable_area_id')
            ->join('finding_types','hud_finding_type.finding_type_id','=','finding_types.id')
            ->select('finding_types.*')->get();

        return $finding_types;
    }
}

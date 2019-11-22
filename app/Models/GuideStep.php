<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * GuideStep Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class GuideStep extends Model
{
    protected $table = 'guide_steps';
    
    protected $fillable = [
        'parent_id',
        'guide_step_type_id',
        'name',
        'session_name',
        'icon',
        'step_help',
        'name_completed',
        'hfa'
    ];

    /**
     * Step Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stepType() : BelongsTo
    {
        return $this->belongsTo('App\GuideStepType', 'guide_step_type_id');
    }

    /**
     * Parent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(\App\Models\GuideStep::class, 'parent_id');
    }

    /**
     * Children
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() : HasMany
    {
        return $this->hasMany(\App\Models\GuideStep::class, 'parent_id')->orderBy('id', 'ASC');
    }

    /**
     * Progress
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progess() : HasMany
    {
        return $this->hasMany(\App\Models\GuideProgress::class);
    }

    /**
     * Is Next Step
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function isNextStep() : HasMany
    {
        return $this->hasMany(\App\Models\Parcel::class, 'next_step');
    }
}

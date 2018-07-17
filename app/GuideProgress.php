<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * GuideProgress Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class GuideProgress extends Model
{
    protected $table = 'guide_progress';
    
    protected $fillable = [
        'guide_step_id',
        'type_id',
        'started',
        'completed'
    ];

    /**
     * Step Guide
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guideStep()
    {
        return $this->hasOne(\App\GuideStep::class, 'id', 'guide_step_id');
    }
}

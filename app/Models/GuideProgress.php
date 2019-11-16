<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GuideProgress Model.
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
        'project_id',
        'audit_id',
        'user_id',
        'started',
        'completed',
    ];

    /**
     * Step Guide.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guideStep()
    {
        return $this->hasOne(\App\Models\GuideStep::class, 'id', 'guide_step_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TargetArea Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class TargetArea extends Model
{
    protected $fillable = [
        'county_id',
        'target_area_name'
    ];

    public $timestamps = false;

    /**
     * County
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county() : BelongsTo
    {
        return $this->belongsTo(County::class);
    }
}

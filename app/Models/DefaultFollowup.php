<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * DefaultFollowup Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DefaultFollowup extends Model
{
    protected $table = 'default_followups';

    protected $fillable = [
        'finding_type_id',
        'description',
        'quantity',
        'duration',
        'assignment',
        'reply',
        'photo',
        'doc',
        'doc_categories',
    ];

    /**
     * Finding Type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function finding_type() : HasOne
    {
        return $this->hasOne(\App\Models\FindingType::class, 'id', 'finding_type_id');
    }
}

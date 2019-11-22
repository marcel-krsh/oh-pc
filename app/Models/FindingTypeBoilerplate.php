<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Finding Type Boilerplate Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class FindingTypeBoilerplate extends Model
{
    protected $table = 'finding_type_boilerplate';

    protected $fillable = [
        'finding_type_id',
        'boilerplate_id'
    ];

    /**
     * FindingType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function finding_type() : HasOne
    {
        return $this->hasOne(\App\Models\FindingType::class, 'id', 'finding_type_id');
    }

    /**
     * Boilerplate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function boilerplate() : HasOne
    {
        return $this->hasOne(\App\Models\Boilerplate::class, 'id', 'boilerplate_id');
    }
}

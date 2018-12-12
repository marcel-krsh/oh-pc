<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'type'
    ];

}
    


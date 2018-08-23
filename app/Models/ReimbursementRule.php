<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ReimbursementRule Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ReimbursementRule extends Model
{
    protected $table = "reimbursement_rules";

    protected $fillable = [
        'minimum_units',
        'maximum_units',
        'maximum_reimbursement',
        'program_rules_id'
    ];
}

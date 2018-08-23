<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ApprovalActionType Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ApprovalActionType extends Model
{
    protected $table = 'approval_action_types';

    public $timestamps = false;

    protected $fillable = [
        'approval_action_name'
    ];
}

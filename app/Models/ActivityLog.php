<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ApprovalActionType Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ActivityLog extends Model
{
    protected $table = 'activity_log';

    public $timestamps = true;

    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type'
    ];
}

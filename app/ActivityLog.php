<?php

namespace App;

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

    public $timestamps = false;

    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type'
    ];
}
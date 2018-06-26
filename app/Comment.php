<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Comment Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'uid',
        'parcel_id',
        'user_id',
        'recorded_date',
        'site_visit_id',
        'comment',
        'deleted'
    ];
}

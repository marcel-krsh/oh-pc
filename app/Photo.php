<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Photo Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'uid',
        'parcel_id',
        'user_id',
        'recorded_date',
        'site_visit_id',
        'notes',
        'latitude',
        'longitude',
        'correction_id',
        'comment_id',
        'deleted'
    ];
}

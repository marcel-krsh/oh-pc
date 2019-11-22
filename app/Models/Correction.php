<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Correction Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Correction extends Model
{
    protected $table = 'corrections';

    protected $fillable = [
        'uid',
        'parcel_id',
        'user_id',
        'recorded_date',
        'site_visit_id',
        'notes',
        'corrected',
        'corrected_site_visit_id',
        'corrected_user_id',
        'corrected_date',
        'deleted'
    ];
}

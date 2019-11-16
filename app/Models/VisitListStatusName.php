<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * VisitListStatusName Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class VisitListStatusName extends Model
{
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    //
    protected $guarded = ['id'];
}

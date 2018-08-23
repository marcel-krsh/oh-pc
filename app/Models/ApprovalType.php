<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ApprovalType Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ApprovalType extends Model
{
    protected $table = 'approval_types';

    public $timestamps = false;

    protected $fillable = [
        'approval_type_name',
        'table_name'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DocumentCategory Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DocumentCategory extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';
    
    protected $guarded = ['id'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Role Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Role extends Model
{

  public function scopeActive($query)
  {
    return $query->where('active', 1);
  }

}

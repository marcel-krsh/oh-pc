<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * UserRole Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class UserNotificationPreferences extends Model
{
  /**
   * User
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function user(): HasOne
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
  }

}

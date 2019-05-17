<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * CommunicationRecipient Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class NotificationsTriggered extends Model
{
  protected $table = 'notifications_triggered';

  protected $casts = [
    'data' => 'array',
  ];

  public function scopeActive($query)
  {
    return $query->where('active', 1);
  }

  public function scopeInactive($query)
  {
    return $query->where('active', 0);
  }

  public function from_user(): HasOne
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'from_id');
  }

  public function to_user(): HasOne
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'to_id');
  }

}

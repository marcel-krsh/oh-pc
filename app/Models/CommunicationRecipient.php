<?php

namespace App\Models;

use Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * CommunicationRecipient Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class CommunicationRecipient extends Model
{
  protected $fillable = [
    'communication_id',
    'user_id',
    'seen',
    'seen_at',
  ];

  public static function boot()
  {
    parent::boot();

    /* @todo: move to observer class */
    static::created(function ($cr) {
      Event::dispatch('communication.created', $cr);
      // Log::info('Fired event?');
    });
  }

  /**
   * Communication
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function communication(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Communication::class);
  }

  public function user(): HasOne
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
  }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;

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
        'seen'
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: move to observer class */

        static::created(function ($communication_recipient) {
            Event::fire('communication.recipient.created', $communication_recipient);
            Log::info('Fired event?');
        });

        // static::updated(function ($transaction) {
        //     Event::fire('transactions.updated', $transaction);
        // });

        // static::deleted(function ($transaction) {
        //     Event::fire('transactions.deleted', $transaction);
        // });
    }

    /**
     * Communication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function communication() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Communication::class);
    }

    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}

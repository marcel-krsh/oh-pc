<?php

namespace App\Models;

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
        'seen'
    ];

    /**
     * Communication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function communication() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Communication::class);
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;

/**
 * Communication Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Communication extends Model
{
    protected $fillable = [
        'parent_id',
        'owner_id',
        'audit_id',
        'owner_type',
        'message',
        'subject'
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: move to observer class */

        static::created(function ($communication) {
            Event::fire('communications.created', $communication);
        });

        // static::updated(function ($transaction) {
        //     Event::fire('transactions.updated', $transaction);
        // });

        // static::deleted(function ($transaction) {
        //     Event::fire('transactions.deleted', $transaction);
        // });
    }

    /**
     * Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'owner_id');
    }

    /**
     * Parent Communication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Communication::class, 'parent_id');
    }

    /**
     * Replies
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies() : HasMany
    {
        return $this->hasMany(\App\Models\Communication::class, 'parent_id');
    }

    /**
     * Audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
    }

    /**
     * Documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents() : HasMany
    {
        return $this->hasMany(\App\Models\CommunicationDocument::class);
    }

    /**
     * Recipients
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients() : HasMany
    {
        return $this->hasMany(\App\Models\CommunicationRecipient::class);
    }
}

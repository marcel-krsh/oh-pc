<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'parcel_id',
        'owner_type',
        'message',
        'subject'
    ];

    /**
     * Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner() : HasOne
    {
        return $this->hasOne(\App\User::class, 'id', 'owner_id');
    }

    /**
     * Parent Communication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(\App\Communication::class, 'parent_id');
    }

    /**
     * Replies
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies() : HasMany
    {
        return $this->hasMany(\App\Communication::class, 'parent_id');
    }

    /**
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parcel() : HasOne
    {
        return $this->hasOne(\App\Parcel::class, 'id', 'parcel_id');
    }

    /**
     * Documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents() : HasMany
    {
        return $this->hasMany(\App\CommunicationDocument::class);
    }

    /**
     * Recipients
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients() : HasMany
    {
        return $this->hasMany(\App\CommunicationRecipient::class);
    }
}

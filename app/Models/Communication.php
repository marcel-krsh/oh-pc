<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;
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
        'subject',
        'project_id'
    ];

    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $casts = [
        'communication_id' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: move to observer class */

        // static::created(function ($communication) {
        //     Event::fire('communications.created', $communication);
        // });

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
     * Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    /**
     * Documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents() : HasMany
    {
        return $this->hasMany(\App\Models\SyncDocuware::class, 'communication_id->communication_id');
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

    public function local_documents()
		{
		    return $this->belongsToMany('App\Models\Document', 'communication_documents', 'communication_id', 'document_id')->whereNull('sync_docuware_id');
		}

		public function docuware_documents()
		{
		    return $this->belongsToMany('App\Models\SyncDocuware', 'communication_documents', 'communication_id', 'sync_docuware_id')->whereNull('document_id');
		}
}

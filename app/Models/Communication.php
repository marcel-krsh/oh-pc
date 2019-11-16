<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Communication Model.
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
    'project_id',
    'finding_ids',
  ];

    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $casts = [
    'communication_id' => 'json',
    'findings_ids' => 'array',
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
     * Owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'owner_id');
    }

    /**
     * Parent Communication.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Audit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit(): HasOne
    {
        // return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    /**
     * Project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project(): HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    /**
     * Documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(\App\Models\SyncDocuware::class, 'communication_id->communication_id');
    }

    /**
     * Recipients.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(\App\Models\CommunicationRecipient::class);
    }

    public function message_recipients()
    {
        return $this->belongsToMany(\App\Models\User::class, 'communication_recipients', 'communication_id', 'user_id')->withPivot('seen', 'seen_at');
    }

    public function local_documents()
    {
        return $this->belongsToMany(\App\Models\Document::class, 'communication_documents', 'communication_id', 'document_id')->whereNull('sync_docuware_id');
    }

    public function docuware_documents()
    {
        return $this->belongsToMany(\App\Models\SyncDocuware::class, 'communication_documents', 'communication_id', 'sync_docuware_id')->whereNull('document_id');
    }

    public function report_notification()
    {
        return $this->hasOne(\App\Models\NotificationsTriggered::class, 'communication_id', 'id')->where('type_id', 2);
    }
}

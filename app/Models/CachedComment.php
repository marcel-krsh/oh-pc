<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon;

class CachedComment extends Model
{
    protected $fillable = [
        'audit_id',
        'building_id',
        'area_id',
        'project_id',
        'finding_id',
        'parent_id',
        'user_id', // author
        'user_name', // author
        'user_json', // user id, name, etc

        'status',
        'type', // finding, comment, photo, document, followup
        'type_icon',
        'type_text',
        'content',

        'finding_type',
        
        'document_id', // one document can be referenced
        'document_json', // id, title, size, etc + categories

        'photos_json', // multiple photos can be stored in JSON

        'followup_date',
        'followup_assigned_id',
        'followup_assigned_name',
        'followup_actions_json',

        'actions_json', // followup, comment ,document, photo

        'created_at',
        'updated_at'
    ];

   
    /**
     * Parent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(\App\Models\CachedComment::class, 'parent_id');
    }

    /**
     * Replies
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies() : HasMany
    {
        return $this->hasMany(\App\Models\CachedComment::class, 'parent_id');
    }

    public function stats_replies_followup_count()
    {
        return $this->replies()
                    ->where('type', '=', 'followup')
                    ->count();
    }

    public function stats_replies_comment_count()
    {
        return $this->replies()
                    ->where('type', '=', 'comment')
                    ->count();
    }

    public function stats_replies_photo_count()
    {
        return $this->replies()
                    ->where('type', '=', 'photo')
                    ->count();
    }

    public function stats_replies_document_count()
    {
        return $this->replies()
                    ->where('type', '=', 'document')
                    ->count();
    }

    /**
     * Building
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function building() : HasOne
    {
        return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
    }

    /**
     * Finding
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function finding() : HasOne
    {
        return $this->hasOne(\App\Models\Building::class, 'id', 'finding_id');
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    /**
     * Audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    /**
     * Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function document() : HasOne
    {
        return $this->hasOne(\App\Models\Document::class, 'id', 'document_id');
    }

    // breaks the jquery templating in audits.js
    // public function getActionsJsonAttribute($value) {
    //   return json_decode($value);
    // }

    // public function getPhotosJsonAttribute($value) {
    //   return json_decode($value);
    // }

    public function getUserJsonAttribute($value)
    {
        return json_decode($value);
    }

    // public function getDocumentJsonAttribute($value) {
    //   return json_decode($value);
    // }

    public function getFollowupActionsJsonAttribute($value)
    {
        return json_decode($value);
    }
}

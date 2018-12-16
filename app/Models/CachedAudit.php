<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon;
use Event;

class CachedAudit extends Model
{
    protected $fillable = [
        'id',
        'audit_id',
        'audit_key',
        'project_id',
        'project_key',
        'project_ref',
        'status',
        'lead',
        'lead_json',
        'title',
        'pm',
        'address',
        'city',
        'state',
        'zip',
        'total_buildings',
        'inspection_icon',
        'inspection_status',
        'inspection_status_text',
        'inspection_schedule_date',
        'inspection_schedule_text',
        'inspectable_items',
        'total_items',
        'audit_compliance_icon',
        'audit_compliance_status',
        'audit_compliance_status_text',
        'followup_status',
        'followup_status_text',
        'followup_date',
        'file_audit_icon',
        'file_audit_status',
        'file_audit_status_text',
        'nlt_audit_icon',
        'nlt_audit_status',
        'nlt_audit_status_text',
        'lt_audit_icon',
        'lt_audit_status',
        'lt_audit_status_text',
        'smoke_audit_icon',
        'smoke_audit_status',
        'smoke_audit_status_text',
        'auditor_status_icon',
        'auditor_status',
        'auditor_status_text',
        'message_status_icon',
        'message_status',
        'message_status_text',
        'document_status_icon',
        'document_status',
        'document_status_text',
        'history_status_icon',
        'history_status',
        'history_status_text',
        'step_status_icon',
        'step_status',
        'step_status_text',
        'created_at',
        'updated_at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($cached_audit) {
            Event::fire('cachedaudit.created', $cached_audit);
        });

        // static::updated(function ($audit) {
        //     Event::fire('audit.updated', $audit);
        // });

        // static::deleted(function ($audit) {
        //     Event::fire('audit.deleted', $audit);
        // });
    }

    public function getLeadJsonAttribute($value) {
      return json_decode($value);
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
     * audits
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function communications() : HasMany
    {
        return $this->hasMany(\App\Models\Communication::class, 'audit_id');
    }
}

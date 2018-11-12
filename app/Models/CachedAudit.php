<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class CachedAudit extends Model
{
    protected $fillable = [
        'id',
        'project_id',
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

    public function getLeadJsonAttribute($value) {
      return json_decode($value);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class CachedBuildingArea extends Model
{
    protected $fillable = [
        'id',
        'audit_id',
        'building_id',
        'status',
        'type',
        'type_total',
        'type_text',
        'type_text_plural',
        'program_total',
        'finding_total',
        'finding_file_status',
        'finding_nlt_status',
        'finding_lt_status',
        'finding_sd_status',
        'finding_file_total',
        'finding_nlt_total',
        'finding_lt_total',
        'finding_sd_total',
        'finding_file_completed',
        'finding_nlt_completed',
        'finding_lt_completed',
        'finding_sd_completed',
        'followup_date',
        'address',
        'city',
        'state',
        'zip',
        'auditors_json',
        'areas_json',
        'created_at',
        'updated_at'
    ];

    public function getAreasJsonAttribute($value) {
      return json_decode($value);
    }

    public function getAuditorsJsonAttribute($value) {
      return json_decode($value);
    }
}

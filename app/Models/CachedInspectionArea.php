<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class CachedInspectionArea extends Model
{
    protected $fillable = [
        'id',
        'audit_id',
        'building_id',
        'area_id',
        'status',
        'name',
        'auditor',
        'auditor_json',
        'finding_nlt_status',
        'finding_lt_status',
        'finding_sd_status',
        'finding_photo_status',
        'finding_comment_status',
        'finding_copy_status',
        'finding_trash_status',
        'created_at',
        'updated_at'
    ];

    public function getAuditorJsonAttribute($value) {
      return json_decode($value);
    }
}

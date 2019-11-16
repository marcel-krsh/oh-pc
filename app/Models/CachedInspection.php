<?php

namespace App\Models;

use Carbon;
use Illuminate\Database\Eloquent\Model;

class CachedInspection extends Model
{
    protected $fillable = [
        'audit_id',
        'project_id',
        'building_id',
        'unit_id',
        'status',
        'address',
        'city',
        'state',
        'zip',
        'auditors_json',
        'type',
        'type_total',
        'type_text',
        'type_text_plural',
        'menu_json',
        'created_at',
        'updated_at',
    ];

    public function getMenuJsonAttribute($value)
    {
        return json_decode($value);
    }

    public function getAuditorsJsonAttribute($value)
    {
        return json_decode($value);
    }
}

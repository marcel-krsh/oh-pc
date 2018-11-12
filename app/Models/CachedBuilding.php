<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class CachedBuilding extends Model
{
    protected $fillable = [
        'id',
        'audit_id',
        'status',
        'type',
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

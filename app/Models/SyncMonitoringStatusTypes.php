<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncMonitoringStatusTypes extends Model
{
    //\
    public $timestamps = true;
    //
    protected $fillable = [
        'monitoring_status_type_key',
        'allita_id',
        'monitoring_status_description',
        'last_edited',
        'created_at',
        'updated_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduleDay extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public function times() : HasMany
    {
        return $this->hasMany(\App\Models\ScheduleTime::class, 'day_id', 'id');
    }

    public function cached_audit() : HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'audit_id', 'audit_id');
    }

    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }
}

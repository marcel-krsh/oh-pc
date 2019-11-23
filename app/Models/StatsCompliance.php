<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatsCompliance extends Model
{
    // public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $table = 'stats_compliance';
    public $timestamps = false;

    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getCreatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }

    //
    protected $guarded = ['id'];

    public function program() : HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'program_key', 'program_key');
    }

    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'monitoring_key', 'monitoring_key');
    }

    public function project(): HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'project_key','development_key');
    }
}

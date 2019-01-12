<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;

class Audit extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($audit) {
            Event::fire('audit.created', $audit);
        });

        static::updated(function ($audit) {
            Event::fire('audit.updated', $audit);
        });

        // static::deleted(function ($audit) {
        //     Event::fire('audit.deleted', $audit);
        // });
    }

    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'project_key', 'development_key');
    }
    public function nlts() : HasMany
    {
        return $this->hasMany('\App\Models\Findings')->where('allita_type','nlt');
    }
    public function lts() : HasMany
    {
        return $this->hasMany('\App\Models\Findings')->where('allita_type','lt');
    }
    public function files() : HasMany
    {
        return $this->hasMany('\App\Models\Findings')->where('allita_type','file');
    }
}

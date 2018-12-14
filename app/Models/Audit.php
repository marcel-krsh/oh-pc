<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
}

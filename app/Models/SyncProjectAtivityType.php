<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProjectAtivityType extends Model
{
    public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'project_activity_type_key',
    	'activity_name',
    	'last_edited'
    ];
}

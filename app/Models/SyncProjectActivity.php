<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProjectActivity extends Model
{
    public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'project_activity_key',
    	'project_key',
    	'project_program_key',
    	'project_activity_type_key',
    	'last_edited'
    ];
}

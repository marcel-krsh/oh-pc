<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProjectRole extends Model
{
   public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'project_role_key',
    	'role_name',
    	'last_edited'
    ];
}

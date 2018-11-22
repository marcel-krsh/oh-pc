<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProjectContactRole extends Model
{
    public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'project_contact_role_key',
    	'project_key',
    	'project_role_key',
    	'organization_key',
    	'last_edited'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProject extends Model
{
    public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'project_key',
    	'project_name',
    	'physical_address_key',
    	'default_phone_number_key',
    	'total_unit_count',
    	'total_building_count',
    	'project_number',
    	'sample_size',
    	'last_edited'

    ]
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncProjectAmenity extends Model
{
    public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'project_amenity_key',
    	'project_key',
    	'project_program_key',
    	'amenity_type_key',
    	'comment',
    	'last_edited'
    ];
}

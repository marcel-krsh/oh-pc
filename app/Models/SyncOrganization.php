<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncOrganization extends Model
{
    public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
    	'allita_id',
    	'organization_key',
    	'default_address_key',
    	'default_phone_number_key',
    	'default_fax_number_key',
    	'default_contact_person_key',
    	'parent_organization_key',
    	'organization_name',
    	'fed_id_number',
    	'last_edited',
    ];
}

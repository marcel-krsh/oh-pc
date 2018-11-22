<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncPeople extends Model
{	public $timestamps = true;
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    

    //
    protected $fillable = [
        'allita_id',
    	'person_key',
    	'last_name',
    	'first_name',
    	'default_phone_number_key',
    	'default_fax_number_key',
    	'default_email_address_key',
    	'last_edited',
    ];
}

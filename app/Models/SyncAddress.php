<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncAddress extends Model
{
	public $timestamps = true;
	protected $dateFormat = 'Y-m-d\TH:i:s.u';

	

    //
    protected $fillable = [
        'devco_id',
        'allita_id',
        'line_1',
        'line_2',
        'city',
        'state_id',
        'state',
        'zip',
        'zip_4',
        'longitude',
        'latitude',
        'last_edited',
        'created_at',
        'updated_at'
    ];
}

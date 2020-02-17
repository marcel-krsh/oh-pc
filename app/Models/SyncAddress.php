<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncAddress extends Model
{
	// public $timestamps = true;
	//protected $dateFormat = 'Y-m-d G:i:s.u';

	//
	protected $guarded = ['id'];
	public $timestamps = true;

	protected $fillable = [
		'line_1',
		'line_2',
		'city',
		'state',
		'zip',
		'zip_4',
		'longitude',
		'latitude',
		'last_edited',
	];

	public function getLastEditedAttribute($value)
	{
		return milliseconds_mutator($value);
	}
}

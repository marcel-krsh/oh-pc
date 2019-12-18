<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceContact extends Model
{
	public $timestamps = true;

	// protected $fillable = [
	// 	'compliance_contact_key',
	// 	'address',
	// 	'project_key',
	// 	'city',
	// 	'zip',
	// 	'review_cycle',
	// 	'next_inspection',
	// ];

	protected $guarded = ['id'];

}

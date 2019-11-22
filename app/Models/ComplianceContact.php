<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Date;

class ComplianceContact extends Model
{
    public $timestamps = false;

        public function getDateFormat() {
					return 'Y-m-d H:i:s.u';
				}

				public function getUpdatedAtAttribute($value)
				{
					if (!empty($value)) {
						return Carbon::createFromFormat('Y-m-d H:i:s', $value);
					} else {
						return null;
					}
				}


    // protected $dateFormat = 'Y-m-d\TH:i:s.u';
  //   protected $dateFormat = 'Y-m-d H:i:s';
    // protected $dateFormat = 'Y-m-d';
	// protected $dates = [ 'created_at', 'updated_at'];

 //    protected $casts = [
	// 	    'updated_at' => 'datetime:Y-m-d H:i:s.u',
	// 	    'created_at' => 'datetime:Y-m-d H:i:s',
	// 	    'last_edited' => 'datetime:Y-m-d H:i:s.u',
	// 	];
		 // protected $appends = ['updated_at'];
// // protected $dateFormat = 'Y-m-d H:i:sO';
// protected $dates = ['created_at',
// 'updated_at':'Y-m-d H:i:s.u'];
//protected $dateFormat = 'Y-m-d H:i:s.u';

 // protected $dateFormat = 'Y-m-d H:i:s';

// protected $casts = [
//    'updated_at' => 'datetime:Y-m-d H:i:s.v',
// ];
//
// protected $hidden = ['last_edited'];
// protected $dates = ['created_at', 'updated_at'];


// public function getCreatedAtAttribute($value)
//     {
//     // example
//     return $value . '.000';

//     }
//     public function getNextInspectionAttribute($value)
//     {
//     // example
//     return $value . '.000';

//     }
// public function getUpdateAtAttribute(string $value): Carbon
// {
//     return Carbon::createFromFormat('Y-m-d H:i:s.u', $value);
// }


    // public function getUpdatedAtAttribute($value)
    // {
    //     $format = Str::contains($value, '.') ? 'Y-m-d H:i:s.u' : 'Y-m-d H:i:s';

    //     return Carbon::createFromFormat($format, $value);
    // }
		// public function getUpdatedAtAttribute()
		// {
		// 	return Carbon::parse($this->updated_at)->format('Y-m-d H:i:s');
		// }

//     public function getDateFormat()
// {
//     return 'Y-m-d H:i:s.u';
// }

// public function fromDateTime($value)
// {
//     return substr(parent::fromDateTime($value), 0, -3);
// }

//     protected $guarded = ['id'];
//     Any idea how to make this work if created_at has no milliseconds but updated_at has milliseconds?
// If dateFormat is used
// `protected $dateFormat = 'Y-m-d H:i:s.u';`
// I get missing Data, as created_at doesn't have milli seconds
// if
// `protected $dateFormat = 'Y-m-d H:i:s';`
// is used, I get trailing data error as updated_at has milliseconds!
}

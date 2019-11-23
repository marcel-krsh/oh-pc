<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAddressType extends Model
{
    // public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';



    //
    protected $guarded = ['id'];

    public $timestamps = false;

    function getUpdatedAtAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
}

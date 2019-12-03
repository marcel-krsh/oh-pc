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

    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CrrReport extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';
    
    protected $guarded = ['id'];

    public function lead(): HasOne{
    	return $this->hasOne('App\Models\User','id','lead_id');
    }

    public function manager(): HasOne{
    	return $this->hasOne('App\Models\User','id','manager_id');
    }

    public function audit(): HasOne{
    	return $this->hasOne('App\Models\Audit');
    }

    public function project(): HasOne{
    	return $this->hasOne('App\Models\Project');
    }

    public function comments(): HasMany{
    	return $this->hasMany('App\Models\CrrComment');
    }

    public function views(): HasMany{
    	return $this->hasMany('App\Models\CrrView');
    }


}

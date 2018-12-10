<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CachedAudit;

class Project extends Model
{
    public $timestamps = true;
	//protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    /**
     * audits
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audits() : HasMany
    {
        return $this->hasMany(\App\Models\CachedAudit::class, 'project_id');
    }

    public function currentAudit() : CachedAudit {
    	$audit = CachedAudit::where('project_ref', '=', $this->id)->orderBy('id', 'desc')->first();
    	if($audit){
    		return $audit;
    	}else{
    		return null;
    	}
    }

}

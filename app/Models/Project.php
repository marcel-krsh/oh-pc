<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CachedAudit;
use App\Models\SystemSetting;

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

    public function address() : HasOne
    {
        return $this->hasOne(\App\Models\Address::class, 'address_key', 'physical_address_key');
    }

    

    public function programs() : HasMany
    {
        return $this->hasMany(\App\Models\ProjectProgram::class, 'project_key', 'project_key');
    }

    public function buildings() : HasMany
    {
        return $this->hasMany(\App\Models\Building::class, 'development_key', 'project_key');
    }

    // TBD needed for the unit selection process
    public function isLeasePurchase()
    {
        
        $leaseProgramKeys = SystemSetting::get('lease_purchase');
        settype($leaseProgramKeys, 'array');
        
        if( in_array($this->program_key, $leaseProgramKeys) ){ return 1; }else{ return 0; }
        
    }

}

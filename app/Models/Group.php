<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $timestamps = true;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function groups()
    {
    	$groups = array();

    	// 1 - FAF || NSP || TCE || RTCAP || 811 units
	    $program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
	    if(in_array($this->program_key, $program_bundle_ids)) $groups[] = 1;

	    // 2 - 811 units
	    $program_811_ids = explode(',', SystemSetting::get('program_811'));
	    if(in_array($this->program_key, $program_811_ids)) $groups[] = 2;

	    // 3 - Medicaid units
	    $program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
	    if(in_array($this->program_key, $program_medicaid_ids)) $groups[] = 3;

	    // 4 - HOME
	    $program_home_ids = explode(',', SystemSetting::get('program_home'));
	    if(in_array($this->program_key, $program_home_ids)) $groups[] = 4;

	    // 5 - OHTF
	    $program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
	    if(in_array($this->program_key, $program_ohtf_ids)) $groups[] = 5;

	    // 6 - NHTF
	    $program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
	    if(in_array($this->program_key, $program_nhtf_ids)) $groups[] = 6;

	    // 7 - HTC
	    $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
	    if(in_array($this->program_key, $program_htc_ids)) $groups[] = 7;

	    return $groups;
    }

}

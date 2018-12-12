<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SystemSetting;

class SyncUser extends Model
{
    public $timestamps = true;
	//protected $dateFormat = 'Y-m-d\TH:i:s.u';
	public $ohfa_id = SystemSetting::get('ohfa_organization_id');

	

    //
    protected $guarded = ['id'];
}

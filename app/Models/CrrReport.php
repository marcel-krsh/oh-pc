<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CrrReport extends Model
{
    protected $casts = [
        'report_history' => 'array',
        //'crr_data' => 'array'
    ];


    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public function lead(): HasOne{
    	return $this->hasOne('App\Models\User','id','lead_id');
    }

    public function manager(): HasOne{
    	return $this->hasOne('App\Models\User','id','manager_id');
    }

    public function status_name(): string {
        $statusName = CrrApprovalType::find($this->crr_approval_type_id);
        return $statusName->name;
    }

    public function crr_approval_type(): HasOne{
        return $this->hasOne('App\Models\CrrApprovalType','id','crr_approval_type_id');
    }

    public function audit(): HasOne{
    	return $this->hasOne('App\Models\Audit','id','audit_id');
    }

    public function template(){

        $template = CrrReport::whereId($this->from_template_id)->first();
        //dd($template);
        //dd($this, $this->from_template_id,$template);
        return $template;
    }

    public function project(): HasOne{
    	return $this->hasOne('App\Models\Project','id','project_id');
    }


    public function comments(): HasMany{
    	return $this->hasMany('App\Models\CrrComment');
    }

    public function views(): HasMany{
    	return $this->hasMany('App\Models\CrrView');
    }

    public function sections(): HasMany{

        return $this->hasMany('App\Models\CrrSection')
        ->join('crr_section_orders','crr_sections.id','=','crr_section_orders.crr_section_id')
        ->orderBy('order','asc')
        ;
    }

    public function signators() {

        return $this->project->contactRoles;
    }


}

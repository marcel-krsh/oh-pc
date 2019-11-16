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

    public function lead(): HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'lead_id');
    }

    public function manager(): HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'manager_id');
    }

    public function status_name(): string
    {
        $statusName = CrrApprovalType::find($this->crr_approval_type_id);
        if ($statusName !== null) {
            return $statusName->name;
        } else {
            return 'NA';
        }
    }

    public function crr_approval_type(): HasOne
    {
        return $this->hasOne(\App\Models\CrrApprovalType::class, 'id', 'crr_approval_type_id');
    }

    public function audit(): HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    public function cached_audit(): HasOne
    {
        return $this->hasOne(\App\Models\CachedAudit::class, 'audit_id', 'audit_id');
    }

    public function template()
    {
        // return $this->hasOne('App\Models\CrrReport','id','from_template_id');
        $template = self::whereId($this->from_template_id)->first();
        //dd($template);
        //dd($this, $this->from_template_id,$template);
        return $template;
    }

    public function project(): HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(\App\Models\CrrComment::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(\App\Models\CrrView::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(\App\Models\CrrSection::class)
        ->join('crr_section_orders', 'crr_sections.id', '=', 'crr_section_orders.crr_section_id')
        ->orderBy('order', 'asc');
    }

    public function signators()
    {
        $allowedRoles = [2, 30006, 27, 30005, 21, 16, 20, 5, 28, 17, 650012, 37, 38, 39, 40];
        $roles = $this->project->contactRoles;
        $rolesFiltered = collect($roles)->whereIn('project_role_key', $allowedRoles);
        //dd($roles,$rolesFiltered);
        return $rolesFiltered;
    }
}

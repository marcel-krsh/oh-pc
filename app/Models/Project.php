<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CachedAudit;
use App\Models\SystemSetting;
use App\Models\Building;
use Carbon;

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

    public function contactRoles() : HasMany
    {
        return $this->hasMany(\App\Models\ProjectContactRole::class, 'project_id');
    }

    public function amenities() : HasMany
    {
        return $this->hasMany('\App\Models\ProjectAmenity');
    }

    public function currentAudit() : CachedAudit
    {
        $audit = CachedAudit::where('project_id', '=', $this->id)->orderBy('id', 'desc')->first();
        return $audit;
    }

    public function pm()
    {
        $pm_contact = $this->contactRoles()->where('project_role_key', '=', 21)
                                ->with('organization.address')
                                ->first();

        $pm_organization = '';
        $pm_address = '';

        if ($pm_contact) { 
            if ($pm_contact->organization) {
                $pm_organization = $pm_contact->organization->organization_name;
                $pm_address = $pm_contact->organization->address->formatter_address;
            }
            if ($pm_contact->person) {
                $pm_name = $pm_contact->person->first_name." ".$pm_contact->person->last_name;
                $pm_phone = $pm_contact->person->phone->number();
                $pm_fax = $pm_contact->person->fax->number();
                $pm_email = $pm_contact->person->email->email_address;
            }
            
        }

        return ['organization'=> $pm_organization, 'name'=>$pm_name, 'email'=>$pm_email, 'phone'=>$pm_phone, 'fax'=>$pm_fax, 'address'=>$pm_address ];
    }

    public function owner()
    {
        $owner_contact = $this->contactRoles()->where('project_role_key', '=', 20)
                                ->with('organization.address')
                                ->first();

        $owner_organization = '';
        $owner_address = '';

        if ($owner_contact) { 
            if ($owner_contact->organization) {
                $owner_organization = $owner_contact->organization->organization_name;
                $owner_address = $owner_contact->organization->address->formatter_address;
            }
            if ($owner_contact->person) {
                $owner_name = $owner_contact->person->first_name." ".$owner_contact->person->last_name;
                $owner_phone = $owner_contact->person->phone->number();
                $owner_fax = $owner_contact->person->fax->number();
                $owner_email = $owner_contact->person->email->email_address;
            }
            
        }

        return ['organization'=> $owner_organization, 'name'=>$owner_name, 'email'=>$owner_email, 'phone'=>$owner_phone, 'fax'=>$owner_fax, 'address'=>$owner_address ];
    }

    public function complianceContacts() : HasOne
    {
        return $this->hasOne(\App\Models\ComplianceContact::class, 'project_key', 'project_key');
    }

    public function nextDueDate()
    {
        $compliance_contacts = $this->complianceContacts()->first();
        $next_inspection = $compliance_contacts->next_inspection;
        if($next_inspection == null){ 
            return 'N/A';
        }else{
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $next_inspection)->format('F j, Y');
        }
    }

    public function lastAudit()
    {
        $audit = Audit::where('development_key', '=', $this->project_key)->where('completed_date', '!=', null)->orderBy('id', 'desc')->first();
        return $audit;
    }

    public function lastAuditCompleted()
    { 
        $audit = $this->lastAudit();
        if($audit){
            if($audit->completed_date){
                $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $audit->completed_date)->format('F j, Y');
                return $date;
            }
        }
        
        return "N/A";
    }

    public function address() : HasOne
    {
        return $this->hasOne(\App\Models\Address::class, 'id', 'physical_address_id');
    }

    

    public function programs() : HasMany
    {
        return $this->hasMany(\App\Models\ProjectProgram::class, 'project_id')->where('program_status_type_id', SystemSetting::get('active_program_status_type_id'));
    }

    public function buildings() : HasMany
    {
        return $this->hasMany('\App\Models\Building');
    }

    public function units() : HasManyThrough {
        return $this->hasManyThrough('App\Models\Unit', 'App\Models\Building');
    }

    public function projectProgramUnitCounts()
    {

        $programs = $this->programs;
        $programCounts = [];
        foreach ($programs as $program) {
            $count = UnitProgram::where('audit_id', $this->currentAudit()->audit_id)
                                            ->where('program_id', $program->program_id)
                                            ->count();
            $programCounts[] = [$program->program->program_name => $count,'program_id'=>$program->program_id];
        }
        if (count($programCounts)<1) {
            $programCounts[] = ['No Programs Found' => 'NA'];
        }
        return $programCounts;
    }

    public function stats_total_buildings()
    {
        return count($this->buildings);
    }

    public function stats_total_units()
    {
        return count($this->units);
    }

    public function stat_program_units()
    {

        $programs = $this->programs; 
        $program_units = [];
        foreach ($programs as $program) { 
            $count = UnitProgram::where('audit_id', $this->currentAudit()->audit_id)
                                            ->where('program_id', '=', $program->program_id)
                                            ->count(); 

            $program_units[] = ["name" => $program->program->program_name, "units" => $count, "program_id" => $program->program_id]; 
        }

        
        return $program_units;
    }
}

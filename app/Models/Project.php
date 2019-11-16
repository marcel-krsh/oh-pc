<?php

namespace App\Models;

use App\Models\Audit;
use App\Models\Building;
use App\Models\CachedAudit;
use App\Models\SystemSetting;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Session;

class Project extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    /**
     * audits.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audits() : HasMany
    {
        return $this->hasMany(\App\Models\CachedAudit::class, 'project_key', 'project_key')->orderBy('id', 'desc');
    }

    public function contactRoles() : HasMany
    {
        return $this->hasMany(\App\Models\ProjectContactRole::class, 'project_id');
    }

    public function amenities() : HasMany
    {
        return $this->hasMany(\App\Models\ProjectAmenity::class);
    }

    public function currentAudit()
    {
        $audit = CachedAudit::where('project_id', '=', $this->id)->orderBy('id', 'desc')->first();

        return $audit;
    }

    public function pm()
    {
        //$ra = ReportAccess::where('project_id', $this->id)->where('default', 1)->first();
        $pm_contact = $this->contactRoles()->where('project_role_key', '=', 21)
                                ->with('organization.address')
                                ->first();

        $pm_organization_id = '';
        $pm_organization = '';
        $pm_address = '';
        $pm_id = '';
        $pm_line_1 = '';
        $pm_line_2 = '';
        $pm_city = '';
        $pm_state = '';
        $pm_zip = '';
        $pm_name = '';
        $pm_email = '';
        $pm_phone = '';
        $pm_fax = '';

        if ($pm_contact) {
            if ($pm_contact->organization) {
                $pm_organization_id = $pm_contact->organization_id;
                $pm_organization = $pm_contact->organization->organization_name;
                if ($pm_contact->organization->address) {
                    $pm_address = $pm_contact->organization->address->formatted_address();
                    $pm_line_1 = $pm_contact->organization->address->line_1;
                    $pm_line_2 = $pm_contact->organization->address->line_2;
                    $pm_city = $pm_contact->organization->address->city;
                    $pm_state = $pm_contact->organization->address->state;
                    $pm_zip = $pm_contact->organization->address->zip;
                }
            }
            if ($pm_contact->person) {
                $pm_person_id = $pm_contact->person->id;
                $pm = User::where('person_id', '=', $pm_person_id)->first();
                if ($pm) {
                    $pm_id = $pm->id;
                } else {
                    $pm_id = '';
                }

                $pm_name = $pm_contact->person->first_name.' '.$pm_contact->person->last_name;
                if ($pm_contact->person->phone) {
                    $pm_phone = $pm_contact->person->phone->number();
                }
                if ($pm_contact->person->fax) {
                    $pm_fax = $pm_contact->person->fax->number();
                }
                if ($pm_contact->person->email) {
                    $pm_email = $pm_contact->person->email->email_address;
                }
            }
        }

        return ['organization_id' => $pm_organization_id, 'pm_id' => $pm_id, 'organization'=> $pm_organization, 'name'=>$pm_name, 'email'=>$pm_email, 'phone'=>$pm_phone, 'fax'=>$pm_fax, 'address'=>$pm_address, 'line_1'=>$pm_line_1, 'line_2'=>$pm_line_2, 'city'=>$pm_city, 'state'=>$pm_state, 'zip'=>$pm_zip];
    }

    public function owner()
    {
        $owner_contact = $this->contactRoles()->where('project_role_key', '=', 20)
                                ->with('organization.address')
                                ->first();

        $owner_organization_id = '';
        $owner_organization = '';
        $owner_name = '';
        $owner_phone = '';
        $owner_fax = '';
        $owner_email = '';
        $owner_address = '';
        $owner_line_1 = '';
        $owner_line_2 = '';
        $owner_city = '';
        $owner_state = '';
        $owner_zip = '';
        $owner_id = '';

        if ($owner_contact) {
            if ($owner_contact->organization) {
                $owner_organization_id = $owner_contact->organization_id;
                $owner_organization = $owner_contact->organization->organization_name;
                if ($owner_contact->organization->address) {
                    $owner_address = $owner_contact->organization->address->formatted_address();
                    $owner_line_1 = $owner_contact->organization->address->line_1;
                    $owner_line_2 = $owner_contact->organization->address->line_2;
                    $owner_city = $owner_contact->organization->address->city;
                    $owner_state = $owner_contact->organization->address->state;
                    $owner_zip = $owner_contact->organization->address->zip;
                }
            }
            if ($owner_contact->person) {
                $owner_person_id = $owner_contact->person->id;
                $owner = User::where('person_id', '=', $owner_person_id)->first();
                if ($owner) {
                    $owner_id = $owner->id;
                }
                $owner_name = $owner_contact->person->first_name.' '.$owner_contact->person->last_name;
                if ($owner_contact->person->phone) {
                    $owner_phone = $owner_contact->person->phone->number();
                }
                if ($owner_contact->person->fax) {
                    $owner_fax = $owner_contact->person->fax->number();
                }
                if ($owner_contact->person->email) {
                    $owner_email = $owner_contact->person->email->email_address;
                }
            }
        }

        return ['organization_id'=> $owner_organization_id, 'owner_id'=> $owner_id, 'organization'=> $owner_organization, 'name'=>$owner_name, 'email'=>$owner_email, 'phone'=>$owner_phone, 'fax'=>$owner_fax, 'address'=>$owner_address, 'line_1'=>$owner_line_1, 'line_2'=>$owner_line_2, 'city'=>$owner_city, 'state'=>$owner_state, 'zip'=>$owner_zip];
    }

    public function complianceContacts() : HasOne
    {
        return $this->hasOne(\App\Models\ComplianceContact::class, 'project_key', 'project_key');
    }

    public function nextDueDate()
    {
        $compliance_contacts = $this->complianceContacts()->first();
        if (! $compliance_contacts) {
            return 'N/A';
        }
        $next_inspection = $compliance_contacts->next_inspection;
        if ($next_inspection == null) {
            return 'N/A';
        } else {
            return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $next_inspection)->format('F j, Y');
        }
    }

    public function selected_audit($audit_id = 0, $reports = 0)
    {
        if ($audit_id) {
            $selected_audit = CachedAudit::where('audit_id', '=', $audit_id)->with('audit');
            if ($reports) {
                $selected_audit = $selected_audit->with('audit.reports');
            }
            $selected_audit = $selected_audit->first();
        } elseif (Session::has('project.'.$this->id.'.selectedaudit') && Session::get('project.'.$this->id.'.selectedaudit') != '') {
            $audit_id = Session::get('project.'.$this->id.'.selectedaudit');
            $selected_audit = CachedAudit::where('audit_id', '=', $audit_id)->with('audit');
            if ($reports) {
                $selected_audit = $selected_audit->with('audit.reports');
            }
            $selected_audit = $selected_audit->first();
        } else {
            $selected_audit = CachedAudit::where('project_id', '=', $this->id)->orderBy('id', 'desc')->with('audit');
            if ($reports) {
                $selected_audit = $selected_audit->with('audit.reports');
            }
            $selected_audit = $selected_audit->first();

            Session::put('project.'.$this->id.'.selectedaudit', $audit_id);
        }

        return $selected_audit;
    }

    public function details($audit_id = 0)
    {
        // details is a cache of the project's information at the time of the audit.
        // details is updated whenever the respective sources are changed, as long as the most current audit is not archived

        $selected_audit = $this->selected_audit($audit_id);

        if (! $selected_audit) {
            // no audit for this project yet, use default project default
            // first check if there are default values and add them if not
            $details = ProjectDetail::where('project_id', '=', $this->id)
                    ->orderBy('id', 'desc')
                    ->first();
        } else {
            $details = ProjectDetail::where('project_id', '=', $this->id)
                    ->where('audit_id', '=', $selected_audit->audit_id)
                    ->orderBy('id', 'desc')
                    ->first();
        }

        if (! $details) {
            // create a default record
            $details = $this->set_project_defaults();
        }

        return $details;
    }

    public function auditSpecificProjectDetails($audit_id)
    {
        // details is a cache of the project's information at the time of the audit.
        // details is updated whenever the respective sources are changed, as long as the most current audit is not archived

        if (! $audit_id) {
            // no audit for this project yet, use default project default
            // first check if there are default values and add them if not
            $details = ProjectDetail::where('project_id', '=', $this->id)
                    ->orderBy('id', 'desc')
                    ->first();
        } else {
            $details = ProjectDetail::where('project_id', '=', $this->id)
                    ->where('audit_id', '=', $audit_id)
                    ->orderBy('id', 'desc')
                    ->first();
        }

        if (! $details) {
            // create a default record
            $details = $this->set_project_defaults();
        }

        return $details;
    }

    public function set_project_defaults($audit_id = null)
    {
        // create a record in project_details table with the current stats, contact info

        //$programs = $this->programs->get(['program_id','total_unit_count'])->toJson();
        $programs = [];
        foreach ($this->programs as $program) {
            $count = $program->total_unit_count;
            $programs[] = ['name' => $program->program->program_name, 'units' => $count, 'program_id' => $program->program_id];
        }

        $last_audit = $this->lastAudit();
        if($last_audit){
            $last_audit_completed_date = $last_audit->completed_date;
        }else{
            $last_audit_completed_date = null;
        }
        // if(is_null($audit_id)){
        //     $audit_id = $this->selected_audit()->audit_id;
        // }

        if ($this->complianceContacts()->first()) {
            $next_inspection = $this->complianceContacts()->first()->next_inspection;
        } else {
            $next_inspection = null;
        }

        // number of units with unit_identity_key == 6
        $market_rate = $this->market_rate_units()->count();

        // subsidized units are units with programs
        $subsidized = $this->program_units_total();

        $details = new ProjectDetail([
                'project_id' => $this->id,
                'audit_id' => $audit_id,
                'last_audit_completed' => $last_audit_completed_date,
                'next_audit_due' => $next_inspection,
                'score_percentage' => null,
                'score' => 'N/A',
                'total_building' => $this->total_building_count,
                'total_building_common_areas' => null,
                'total_building_systems' => null,
                'total_building_exteriors' => null,
                'total_project_common_areas' => null,
                'total_units' => $this->total_unit_count,
                'market_rate' => $market_rate,
                'subsidized' => $subsidized,
                'programs' => json_encode($programs),
                'owner_name' => $this->owner()['organization'],
                'owner_poc' => $this->owner()['name'],
                'owner_phone' => $this->owner()['phone'],
                'owner_fax' => $this->owner()['fax'],
                'owner_email' => $this->owner()['email'],
                'owner_address' => $this->owner()['line_1'],
                'owner_address2' => $this->owner()['line_2'],
                'owner_city' => $this->owner()['city'],
                'owner_state' => $this->owner()['state'],
                'owner_zip' => $this->owner()['zip'],

                'manager_name' => $this->pm()['organization'],
                'manager_poc' => $this->pm()['name'],
                'manager_phone' => $this->pm()['phone'],
                'manager_fax' => $this->pm()['fax'],
                'manager_email' => $this->pm()['email'],
                'manager_address' => $this->pm()['line_1'],
                'manager_address2' => $this->pm()['line_2'],
                'manager_city' => $this->pm()['city'],
                'manager_state' => $this->pm()['state'],
                'manager_zip' => $this->pm()['zip'],
            ]);
        $details->save();

        return $details;
    }

    public function lastAudit()
    {
        $audit = Audit::where('development_key', '=', $this->project_key)->where('completed_date', '!=', null)->orderBy('id', 'desc')->first();

        return $audit;
    }

    public function lastAuditCompleted()
    {
        $audit = $this->lastAudit();
        if ($audit) {
            if ($audit->completed_date) {
                $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $audit->completed_date)->format('F j, Y');

                return $date;
            }
        }

        return 'N/A';
    }

    public function address() : HasOne
    {
        return $this->hasOne(\App\Models\Address::class, 'id', 'physical_address_id');
    }

    public function programs() : HasMany
    {
        $programKeys = [];
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_htc')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_bundle')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_811')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_faf')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_nsp')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_tce')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_rtcap')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_medicaid')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_home')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_ohtf')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_nhtf')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('lease_purchase')));
        // $test = ProjectProgram::where('project_id','45055')->where('program_status_type_id', SystemSetting::get('active_program_status_type_id'))
        //         ->whereIn('program_key',$programKeys)->get();
        // dd($test,$programKeys);
        // need to make this read from system settings (not hard code) for program statuses
        return $this->hasMany(\App\Models\ProjectProgram::class, 'project_id')

                ->whereIn('program_key', $programKeys)
                ->where(function ($query) {
                    $query->orWhere('project_program_status_type_key', 30012);
                    $query->orWhere('project_program_status_type_key', 30004);
                    $query->orWhere('project_program_status_type_key', 30009);
                    $query->orWhere('project_program_status_type_key', 30010);
                })->with('program');
    }

    public function all_other_programs() : HasMany
    {
        $programKeys = [];
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_htc')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_bundle')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_811')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_faf')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_nsp')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_tce')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_rtcap')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_medicaid')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_home')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_ohtf')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('program_nhtf')));
        $programKeys = array_merge($programKeys, explode(',', SystemSetting::get('lease_purchase')));

        // we don't exclude non active programs as we may still get back program funding
        return $this->hasMany(\App\Models\ProjectProgram::class, 'project_id')
                ->whereNotIn('program_key', $programKeys);
    }

    public function buildings() : HasMany
    {
        return $this->hasMany(\App\Models\Building::class)->where('building_status_key', '=', 1)->with('address');
    }

    public function units() : HasManyThrough
    {
        return $this->hasManyThrough(\App\Models\Unit::class, \App\Models\Building::class);
    }

    public function market_rate_units() : HasManyThrough
    {
        return $this->hasManyThrough(\App\Models\Unit::class, \App\Models\Building::class)->where('unit_identity_id', '=', 6);
    }

    public function program_units_total()
    {
        $total = 0;

        $units = UnitProgram::select('unit_id')
                            ->where('audit_id', $this->currentAudit()->audit_id)
                            ->groupBy('unit_id')
                            ->get();

        $total = $total + count($units);

        return $total;
    }

    public function projectProgramUnitCounts()
    {
        $programs = $this->programs;
        $programCounts = [];
        foreach ($programs as $program) {
            $count = UnitProgram::where('audit_id', $this->currentAudit()->audit_id)
                                            ->where('program_id', $program->program_id)
                                            ->count();
            $programCounts[] = [$program->program->program_name => $count, 'program_id'=>$program->program_id];
        }
        if (count($programCounts) < 1) {
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

    public function stats_total_market_rate_units()
    {
        return count($this->market_rate_units);
    }

    public function stat_program_units()
    {
        $programs = $this->programs;
        $program_units = [];
        foreach ($programs as $program) {
            // $count = UnitProgram::where('audit_id', $this->currentAudit()->audit_id)
            //                                 ->where('program_id', '=', $program->program_id)
            //                                 ->count();

            $count = $program->total_unit_count;

            $program_units[] = ['name' => $program->program->program_name.' '.$program->program_id.' '.$this->currentAudit()->audit_id, 'units' => $count, 'program_id' => $program->program_id];
        }

        return $program_units;
    }

    public function is_project_contact($user_id = 1)
    {
        return count($this->contactRoles->where('user_id', $user_id));
    }

    public function project_users()
    {
        return $this->hasMany(\App\Models\ReportAccess::class, 'project_id', 'id');
    }

    public function documents() : HasMany
    {
        return $this->hasMany(\App\Models\Document::class, 'project_id', 'id')->with('user');
    }
}

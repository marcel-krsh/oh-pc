<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * User Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id',
        'badge_color',
        'email_token',
        'active',
        'validate_all',
        'entity_type',
        'api_token',
        'devco_key',
        'last_accessed',
        'socket_id',
        'person_key',
        'person_id',
        'last_edited',
        'user_status_key',
        'user_status_id',
        'organization_id',
        'organization_key',
        'organization',
        'availability_max_hours',
        'availability_lunch',
        'availability_max_driving'
    ];

    protected static $logAttributes = [
        'name',
        'email',
        'organization_id',
        'badge_color'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * A user can have many messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
      return $this->hasMany(Message::class);
    }

    /**
     * Roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() : HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function roles_list()
    {
        $roles = $this->roles()->get();
        $output = '';
        foreach($roles as $role){
            if($output == ''){
                $output = $output.$role->role->name;
            }else{
                $output = $output.', '.$role->role->name;
            }

            
        }
        if($output == ''){
            $output = 'NO ACCESS';
        }
        return $output;
    }

    /**
     * Person
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person() : HasOne
    {
        return $this->hasOne(People::class, 'person_key', 'person_key');
    }

    /**
     * Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function has_organization() : int
    {
        if(is_null($this->organization_id)){
            return false;
        } else {
            return true;
        }
    }
    public function has_address() : int
    {
        if(is_null($this->organization_id) || is_null($this->organization_details->address)){
            return false;
        } else {
            return true;
        }
    }

    public function organization_details() : HasOne
    {
        return $this->hasOne(Organization::class, 'id', 'organization_id');
    }

    public function auditor_addresses() : HasMany
    {
        return $this->HasMany(Address::class, 'user_id', 'id');
    }

    public function addresses() : HasMany
    {
        return $this->HasMany(Address::class, 'user_id', 'id');
    }

    public function initials() : string
    {

        $person = $this->person;
        //dd($person, $this->id, $this->person_id);
        $initials = substr($person->first_name, 0, 1);
        $initials .= substr($person->last_name, 0, 1);
        return strtoupper($initials);
    }

    public function full_name() : string
    {
        $fullName = "NA";
        if($this->person){
            $fullName = $this->person->first_name." ".$this->person->last_name;
        }
        return $fullName;
    }
    /**
     * Is From Organization
     *
     * @param $id
     *
     * @return int
     */
    public function isFromOrganization($id) : int
    {
        if ($id == $this->organization_id) {
            return 1;
        }
        return 0;
    }

    public function isOhfa() : bool
    {
        $ohfa_id = SystemSetting::get('ohfa_organization_id');
        if ($ohfa_id == $this->organization_id) {
            return 1;
        }
        return 0;
    }

    public function hasRole($role_id) : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->role_id == $role_id) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Has PM level access
     *
     * @return bool
     */
    public function pm_access() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id >= 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Has Auditor level access
     *
     * @return bool
     */
    public function auditor_access() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id >= 2) {
                return true;
            }
        }
        return false;
    }

    /**
     * Has Manager level access
     *
     * @return bool
     */
    public function manager_access() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id >= 3) {
                return true;
            }
        }
        return false;
    }

    /**
     * Has Admin level access
     *
     * @return bool
     */
    public function admin_access() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id >= 4) {
                return true;
            }
        }
        return false;
    }

    /**
     * Has Root level access
     *
     * @return bool
     */
    public function root_access() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id >= 5) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get Perms
     *
     * @return array
     */
    public function getPerms()
    {
        $permnames = [];
        foreach ($this->roles as $userrole) {
            foreach ($userrole->permissions as $perm) {
                array_push($permnames, $perm->permission_name);
            }
        }
        return $permnames;
    }

    /**
     * Verify
     */
    public function verify()
    {
        $this->verified = 1;
        $this->email_token = null;
        $this->save();
    }

    /**
     * Activate
     */
    public function activate()
    {
        $this->active = 1;
        $this->save();
    }

    /**
     * Increment Tries
     */
    public function incrementTries()
    {
        $this->tries = $this->tries + 1;
        $this->save();
    }

    /**
     * Reset Tries
     */
    public function resetTries()
    {
        $this->tries = 0;
        $this->save();
    }

    /**
     * Deactivate
     */
    public function deactivate()
    {
        $this->active = 0;
        $this->save();
    }

    /**
     * Is Active
     *
     * @return mixed
     */
    public function isActive()
    {
        //proxy return value
        return $this->active;
    }

    public function isScheduled($audit_id, $day_id) : int
    {
        if(count(ScheduleTime::where('auditor_id','=',$this->id)->where('audit_id','=',$audit_id)->where('day_id','=',$day_id)->get())){
            return 1;
        }else{
            return 0;
        }
    }
}

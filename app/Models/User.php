<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'organization'
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
     * Roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles() : BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    /**
     * Person
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function person() : HasOne
    {
        return $this->hasOne(People::class, 'id','person_id');
    }

    public function initials() : string {

        $initials = substr($this->person()->first_name, 0,1);
        $initials .= substr($this->person()->last_name, 0,1);
        return $initials;

    }

    public function full_name() : string {
        $fullName = $this->person->first_name." ".$this->person->last_name;
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

    /**
     * Is Landbank Admin
     *
     * @return bool
     */
    public function isLandbankAdmin() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 4) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Parcel Approver
     * @return bool
     */
    public function isLandbankParcelApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 13) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landback Request Approver
     *
     * @return bool
     */
    public function isLandbankRequestApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 14) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Invoice Approver
     *
     * @return bool
     */
    public function isLandbankInvoiceApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 17) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Member Approver
     *
     * @return bool
     */
    public function isLandbankMemberApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 6) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Disposition Reviewer
     *
     * @return bool
     */
    public function isLandbankDispositionReviewer() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 10) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Disposition Approver
     *
     * @return bool
     */
    public function isLandbankDispositionApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 11) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Disposition Manager
     *
     * @return bool
     */
    public function isLandbankDispositionManager() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 12) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Landbank Simple Approval
     *
     * @return bool
     */
    public function isLandBankSimpleApproval() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 36) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Admin
     *
     * @return bool
     */
    public function isHFAAdmin() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 3) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Approver
     *
     * @return bool
     */
    public function isHFAPOApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 15) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Compliance Auditor
     *
     * @return bool
     */
    public function isHFAComplianceAuditor() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 16) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Primary Invoice Approver
     *
     * @return bool
     */
    public function isHFAPrimaryInvoiceApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 18) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Secondary Invoice Approver
     *
     * @return bool
     */
    public function isHFASecondaryInvoiceApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 19) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Tertiary Invoice Approver
     *
     * @return bool
     */
    public function isHFATertiaryInvoiceApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 20) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Fiscal Agency
     * @return bool
     */
    public function isHFAFiscalAgent() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 21) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Simple Approval
     *
     * @return bool
     */
    public function isHFASimpleApproval() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 35) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Landbank Approver
     *
     * @return bool
     */
    public function isHFALandbankApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 5) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Disposition Approver
     *
     * @return bool
     */
    public function isHFADispositionApprover() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 7) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Lien Manager
     *
     * @return bool
     */
    public function isHFALienManager() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 8) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is HFA Disposition Reviewer
     *
     * @return bool
     */
    public function isHFADispositionReviewer() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 9) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Notified Of Lien Release Request
     *
     * @return bool
     */
    public function isNotifiedOfLienReleaseRequest() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 28) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can Delete Parcels
     *
     * @return bool
     */
    public function canDeleteParcels() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 23 || $role->id == 26) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can Edit Parcels
     *
     * @return bool
     */
    public function canEditParcels() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 23 || $role->id == 30) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can Reassign Parcels
     *
     * @return bool
     */
    public function canReassignParcels() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 24) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can Manage Users
     *
     * @return bool
     */
    public function canManageUsers() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 25 || $role->id == 22) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can View Dispositions
     *
     * @return bool
     */
    public function canViewDispositions() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 3 || $role->id == 7 || $role->id == 9 || $role->id == 10 || $role->id == 11 || $role->id == 12 || $role->id == 16 || $role->id == 27 || $role->id == 21) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can View Vendor Stats
     *
     * @return bool
     */
    public function canViewVendorStats() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 3 || $role->id == 70) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Auditor
     *
     * @return bool
     */
    public function isAuditor() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 3 || $role->id == 37) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Site Visit Manager
     *
     * @return bool
     */
    public function isSiteVisitManager() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 71) {
                return true;
            }
        }
        return false;
    }

    /**
     * Is Site Visit Auditor
     *
     * @return bool
     */
    public function isSiteVisitAuditor() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 72) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can View Site Visits
     *
     * @return bool
     */
    public function canViewSiteVisits() : bool
    {
        foreach ($this->roles()->get() as $role) {
            if ($role->id == 73 || $role->id == 74) {
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
}
